<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.261023
 */

namespace JeffreyBostoenExtensions\MailToTicket;

// iTop internals.
use DBObjectSearch;
use DBObjectSet;
use Exception;
use MetaModel;
use ormDocument;
use Throwable;
use utils;

// iTop events.
use Combodo\iTop\Service\Events\{
	EventData,
	EventService,
	iEventServiceSetup
};
use Combodo\iTop\Service\Events\Description\{
	EventDataDescription,
	EventDescription
};

// iTop classes.
use TriggerMailToTicketProcessingOnEvent;

// Module classes.
use JeffreyBostoenExtensions\MailToTicket\Steps\Base as BaseStep;

/**
 * Interface iMailProcessingEventProvider. Implemented by another extension that fires its own events (e.g. for a custom policy).
 *
 * Besides this, the extension must:
 * - Add the code to the values of "code" (and the category to the values of "category", when it is a new one) of the class MailProcessingEvent in its own datamodel (delta).
 * - Add dictionary entries for these values.
 * The objects of the events are created upon setup (see Events::CreateMissingEventObjects()).
 * Fire the event with Events::FireEvent(), using the code.
 *
 * A step finds the event of its violation by convention: "<settings prefix>_violation" or "<settings prefix>_detected" (see Base::GetViolationEvent()).
 */
interface iMailProcessingEventProvider {

	/**
	 * Gets the events of the extension.
	 *
	 * @return array<string, string> Category by event code. The code should start with its category (e.g. "policy_my_check_violation" in category "policy").
	 */
	public static function GetMailProcessingEvents() : array;

}

/**
 * Enum eEvent. Defines the events of this extension that can be fired during the processing of incoming e-mails.
 *
 * The first part of a code is the category (see Events::CATEGORIES), the rest indicates the event.
 * Every case needs a matching value of "code" (and its category in "category") of the class MailProcessingEvent in the datamodel, and a dictionary entry.
 * Other extensions can not add cases: they use iMailProcessingEventProvider.
 *
 * The comment on the right indicates what *extra* values are available as "$details->...$" (besides the general ones, see Events::GetBaseContextArguments()).
 * The reference that is shown to administrators is kept in PlaceholderReference::GetEventRows(): keep it in line with these comments.
 * Naming of the extra values:
 * - Objects: "name->object()", e.g. "caller->object()".
 * - Counts: "<name>_count".
 */
enum eEvent : string {

	// - Cron task (one run of the background process).

		case CRON_TASK_STARTED = 'cron_task_started';
		case CRON_TASK_FINISHED = 'cron_task_finished'; // message_count, processed_count, deleted_count, moved_count, marked_as_error_count, undesired_count, skipped_count, duration_seconds

	// - Mailbox.

		case MAILBOX_STARTED = 'mailbox_started'; // message_count
		case MAILBOX_FINISHED = 'mailbox_finished';
		case MAILBOX_CONNECTION_FAILED = 'mailbox_connection_failed'; // error_message

	// - Message.

		case MESSAGE_STARTED = 'message_started';
		case MESSAGE_FINISHED = 'message_finished';
		case MESSAGE_DECODE_FAILED = 'message_decode_failed'; // invalid_reasons
		case MESSAGE_DELETED = 'message_deleted';
		case MESSAGE_MOVED = 'message_moved';
		case MESSAGE_MARKED_UNDESIRED = 'message_marked_undesired';
		case MESSAGE_MARKED_ERROR = 'message_marked_error';
		case MESSAGE_NO_TICKET = 'message_no_ticket'; // No ticket was created or linked. See also: outcome_summary.

	// - Ticket.

		case TICKET_CREATED = 'ticket_created'; // ticket->object()
		case TICKET_UPDATED = 'ticket_updated'; // ticket->object()

	// - Caller.

		case CALLER_CREATED = 'caller_created'; // caller->object()
		case CALLER_INACTIVATED = 'caller_inactivated'; // caller->object()

	// - Policies. Violations always fire, regardless of the configured behavior (see "behavior").
	//   Context arguments for all of them: step_id, step_class, step_precedence, behavior.

		case POLICY_ATTACHMENT_FORBIDDEN_MIMETYPE_VIOLATION = 'policy_attachment_forbidden_mimetype_violation';
		case POLICY_MAIL_SIZE_TOO_BIG_VIOLATION = 'policy_mail_size_too_big_violation';
		case POLICY_NO_SUBJECT_VIOLATION = 'policy_no_subject_violation';
		case POLICY_OTHER_EMAIL_CALLER_THAN_TICKET_CALLER_VIOLATION = 'policy_other_email_caller_than_ticket_caller_violation';
		case POLICY_OTHER_RECIPIENTS_VIOLATION = 'policy_other_recipients_violation';
		case POLICY_SENDER_AUTHENTICATION_VIOLATION = 'policy_sender_authentication_violation'; // authentication_states
		case POLICY_SENDER_EMAIL_ADDRESS_VIOLATION = 'policy_sender_email_address_violation';
		case POLICY_TICKET_CLOSED_VIOLATION = 'policy_ticket_closed_violation';
		case POLICY_TICKET_RESOLVED_VIOLATION = 'policy_ticket_resolved_violation';
		case POLICY_TICKET_UNKNOWN_VIOLATION = 'policy_ticket_unknown_violation';
		case POLICY_UNDESIRED_PATTERN_VIOLATION = 'policy_undesired_pattern_violation';
		case POLICY_UNKNOWN_CALLER_VIOLATION = 'policy_unknown_caller_violation';
		case POLICY_AUTOREPLY_DETECTED = 'policy_autoreply_detected';
		case POLICY_NON_DELIVERY_REPORT_DETECTED = 'policy_non_delivery_report_detected';

	// - Errors.

		case ERROR_PROCESSING = 'error_processing'; // error_code, error_message

	/**
	 * Gets the category of this event: the first part of its code.
	 *
	 * @return string
	 */
	public function GetCategory() : string {

		foreach(Events::CATEGORIES as $sCategory) {
			if(str_starts_with($this->value, $sCategory.'_')) {
				return $sCategory;
			}
		}

		return '';

	}

}

/**
 * Class Events. Fires the events of the mail processing and activates the triggers (TriggerMailToTicketProcessingOnEvent) subscribed to them.
 */
class Events implements iEventServiceSetup {

	/** @var string MAIL_PROCESSING_EVENT Name of the event. */
	public const MAIL_PROCESSING_EVENT = 'MAIL_TO_TICKET_PROCESSING_EVENT';

	/** @var string[] CATEGORIES Event categories. Every event code starts with one of them. */
	public const CATEGORIES = [
		'cron_task',
		'mailbox',
		'message',
		'ticket',
		'caller',
		'policy',
		'error',
	];

	/**
	 * @var array<string, int[]>|null $aTriggerIdsByEventCode Keys of the subscribed triggers, by event code.
	 * Built once per cron task (see ResetSubscriptions()), so events nobody subscribed to are nearly free.
	 */
	private static ?array $aTriggerIdsByEventCode = null;

	/**
	 * Forgets the known subscriptions, so they are loaded again when needed.
	 *
	 * @return void
	 */
	public static function ResetSubscriptions() : void {

		static::$aTriggerIdsByEventCode = null;

	}

	/**
	 * Gets the keys of the triggers subscribed to the events.
	 *
	 * @return array<string, int[]>
	 */
	private static function GetSubscriptions() : array {

		if(static::$aTriggerIdsByEventCode === null) {

			static::$aTriggerIdsByEventCode = [];

			$oSet = new DBObjectSet(DBObjectSearch::FromOQL_AllData('SELECT lnkMailProcessingEventToTrigger'));
			while($oLink = $oSet->Fetch()) {
				static::$aTriggerIdsByEventCode[$oLink->Get('code')][] = (int)$oLink->Get('trigger_id');
			}

		}

		return static::$aTriggerIdsByEventCode;

	}

	/**
	 * @var array<string, string>|null $aEventCategories Category by event code, of all known events (this extension and iMailProcessingEventProvider implementations).
	 */
	private static ?array $aEventCategories = null;

	/**
	 * Gets all known events.
	 *
	 * @return array<string, string> Category by event code.
	 */
	public static function GetKnownEvents() : array {

		if(static::$aEventCategories === null) {

			static::$aEventCategories = [];

			foreach(eEvent::cases() as $eEvent) {
				static::$aEventCategories[$eEvent->value] = $eEvent->GetCategory();
			}

			foreach(get_declared_classes() as $sClassName) {
				if(is_subclass_of($sClassName, iMailProcessingEventProvider::class)) {
					foreach($sClassName::GetMailProcessingEvents() as $sCode => $sCategory) {
						static::$aEventCategories[(string)$sCode] = (string)$sCategory;
					}
				}
			}

		}

		return static::$aEventCategories;

	}

	/**
	 * Whether this is the code of a known event.
	 *
	 * @param string $sCode Event code.
	 *
	 * @return bool
	 */
	public static function IsKnownEvent(string $sCode) : bool {

		return isset(static::GetKnownEvents()[$sCode]);

	}

	/**
	 * Whether any trigger is subscribed to this event.
	 *
	 * @param eEvent|string $mEvent Event (or its code).
	 *
	 * @return bool
	 */
	public static function HasSubscribers(eEvent|string $mEvent) : bool {

		return isset(static::GetSubscriptions()[$mEvent instanceof eEvent ? $mEvent->value : $mEvent]);

	}

	/**
	 * Fires an event.
	 * Nothing happens (and nothing is collected) when no trigger is subscribed to it.
	 *
	 * @param eEvent|string $mEvent Event (or its code, for an event of another extension).
	 * @param array $aContextArguments Extra data related to the event (see the comments on the cases of eEvent). Text values are available as "$details->...$".
	 *
	 * @return void
	 */
	public static function FireEvent(eEvent|string $mEvent, array $aContextArguments = []) : void {

		if(!static::HasSubscribers($mEvent)) {
			return;
		}

		$oEventData = new EventData(static::MAIL_PROCESSING_EVENT, null, [
			'event' => $mEvent instanceof eEvent ? $mEvent->value : $mEvent,
			'context_arguments' => $aContextArguments,
		]);

		EventService::FireEvent($oEventData);

	}

	/**
	 * Creates the missing MailProcessingEvent objects. These are reference data, one per known event.
	 * Called upon setup; existing events (and their links to triggers) are never modified.
	 *
	 * @return int Number of created events.
	 */
	public static function CreateMissingEventObjects() : int {

		$aExisting = [];
		$oSet = new DBObjectSet(DBObjectSearch::FromOQL_AllData('SELECT MailProcessingEvent'));
		while($oEvent = $oSet->Fetch()) {
			$aExisting[$oEvent->Get('code')] = true;
		}

		$iCreated = 0;
		foreach(static::GetKnownEvents() as $sCode => $sCategory) {

			if(isset($aExisting[$sCode])) {
				continue;
			}

			try {
				MetaModel::NewObject('MailProcessingEvent', [
					'category' => $sCategory,
					'code' => $sCode,
				])->DBInsert();
				$iCreated++;
			}
			catch(Exception $e) {
				// Most likely this errors because the code already exists.
			}

		}

		return $iCreated;

	}

	/**
	 * @inheritDoc
	 */
	public function RegisterEventsAndListeners() {

		EventService::RegisterEvent(new EventDescription(
			static::MAIL_PROCESSING_EVENT,
			null,
			'An event during the processing of incoming e-mails.',
			'',
			[
				new EventDataDescription(
					'event',
					'The code of the event that was triggered.',
					'string',
				),
				new EventDataDescription(
					'context_arguments',
					'Context arguments related to the event.',
					'array',
				),
			],
			'jb-email-synchro'
		));

		EventService::RegisterListener(
			static::MAIL_PROCESSING_EVENT,
			[$this, 'OnMailProcessingEvent']
		);

	}

	/**
	 * Gets the context arguments that are available for every event, as far as the information is known at that point.
	 *
	 * Objects (iTop objects, or PlaceholderValues for text values) are passed as "<name>->object()".
	 * Text values that were collected under the name of an object (e.g. "mail->subject") are also kept as plain arguments, so they can be used as query arguments (e.g. ":mail->caller_email").
	 *
	 * @param string $sEventCode Event code.
	 *
	 * @return array
	 */
	public static function GetBaseContextArguments(string $sEventCode) : array {

		$aArgs = [];

		// - The event object makes its attributes available (e.g. "$event->code$"), and the localized labels (e.g. "$event->label(code)$"), in the language of the action.

			$oEvent = MetaModel::GetObjectByColumn('MailProcessingEvent', 'code', $sEventCode, false);
			if($oEvent !== null) {
				$aArgs['event->object()'] = $oEvent;
			}

		// - Mail placeholders, including the date and time of processing.
		//   These are provided by the steps, which may not be available.

			$oMail = ProcessingHelper::GetMailOrNull();
			if(class_exists(BaseStep::class)) {

				$aArgs = array_merge($aArgs, BaseStep::GetDateTimePlaceholders());

				if($oMail !== null) {
					$aArgs = array_merge($aArgs, BaseStep::GetMailPlaceholders($oMail));
				}

			}

			if($oMail !== null && $oMail->GetSender() !== null) {
				$aArgs['sender->object()'] = $oMail->GetSender();
			}

		// - Mailbox.

			$oMailBox = ProcessingHelper::GetMailBoxOrNull();
			if($oMailBox !== null) {
				$aArgs['mailbox->object()'] = $oMailBox;
			}

		// - Message handler and source.

			$oHandler = ProcessingHelper::GetMessageHandlerOrNull();
			if($oHandler !== null) {
				$aArgs['message->index'] = $oHandler->GetOriginalListingIndex();
				$aArgs['message->uid'] = $oHandler->GetUid();
				$aArgs['message->internal_identifier'] = $oHandler->GetInternalIdentifier();
				$aArgs['message->message_id'] = $oHandler->GetMessageId();
				$aArgs['message->timestamp'] = $oHandler->GetTimestamp();
				$aArgs['message->timestamp_full'] = date('Y-m-d H:i:s', $oHandler->GetTimestamp());
			}

			$oSource = ProcessingHelper::GetMailSourceOrNull();
			if($oSource !== null) {
				$aArgs['source->name'] = $oSource->GetName();
				$aArgs['source->mailbox_path'] = $oSource->GetMailbox();
			}

		// - Ticket and outcome of the processing so far.

			$oTicket = ProcessingHelper::GetTicket();
			if($oTicket !== null) {
				$aArgs['ticket->object()'] = $oTicket;
			}

			if($oMail !== null) {
				$aArgs['next_action'] = ProcessingHelper::GetNextAction()->name;
				$aArgs['outcome_summary'] = ProcessingHelper::GetOutcomeSummary();
				$aArgs['executed_steps'] = implode(', ', array_map('basename', ProcessingHelper::GetExecutedSteps()));
			}

		return $aArgs;

	}

	/**
	 * Names of the context arguments that are always part of the details of an event, next to the extra values passed when the event was fired.
	 */
	private const DETAIL_ARGUMENTS = ['next_action', 'outcome_summary', 'executed_steps'];

	/**
	 * Names of the text value groups ("<name>->...") that become one PlaceholderValues object.
	 */
	private const VALUE_GROUPS = ['mail', 'message', 'source'];

	/**
	 * Moves text values to PlaceholderValues objects, so they can be used with a verb, e.g. "$mail->html(subject)$" to escape the value for an HTML body.
	 *
	 * - "mail", "message" and "source": the values with that prefix (e.g. "mail->subject"). The plain arguments are kept as well.
	 * - "details": the outcome of the processing, and the extra values passed when the event was fired (e.g. "reason", "error_message").
	 *
	 * @param array $aArgs Context arguments.
	 * @param array $aExtraArgs Extra context arguments passed when the event was fired. Already part of $aArgs.
	 *
	 * @return array Context arguments.
	 */
	private static function GroupTextValues(array $aArgs, array $aExtraArgs) : array {

		$aGroups = [];
		foreach(static::VALUE_GROUPS as $sGroup) {
			$aGroups[$sGroup] = [];
		}

		// - Groups. Plain arguments are kept.

			foreach($aArgs as $sKey => $mValue) {

				$aParts = explode('->', (string)$sKey, 2);
				if(count($aParts) === 2 && isset($aGroups[$aParts[0]]) && !is_object($mValue)) {
					$aGroups[$aParts[0]][$aParts[1]] = $mValue;
				}

			}

			foreach($aGroups as $sGroup => $aValues) {
				if(count($aValues) > 0) {
					$aArgs[$sGroup.'->object()'] = new PlaceholderValues($aValues);
				}
			}

		// - Details. Moved: these are only available through the object.

			$aDetails = [];
			foreach(array_merge(static::DETAIL_ARGUMENTS, array_keys($aExtraArgs)) as $sKey) {

				$sKey = (string)$sKey;
				if(array_key_exists($sKey, $aArgs) && !str_contains($sKey, '->') && (is_scalar($aArgs[$sKey]) || $aArgs[$sKey] === null)) {
					$aDetails[$sKey] = $aArgs[$sKey];
					unset($aArgs[$sKey]);
				}

			}

			if(count($aDetails) > 0) {
				$aArgs['details->object()'] = new PlaceholderValues($aDetails);
			}

		return $aArgs;

	}

	/**
	 * Activates the triggers subscribed to the event that was fired.
	 *
	 * @param EventData $oEventData Event data.
	 *
	 * @return void
	 */
	public function OnMailProcessingEvent(EventData $oEventData) : void {

		$sEventCode = $oEventData->Get('event');
		if(!is_string($sEventCode) || $sEventCode === '') {
			ProcessingHelper::TraceCron('An event was fired, but it is missing the "event".');
			return;
		}

		$aTriggerIds = static::GetSubscriptions()[$sEventCode] ?? [];
		if(count($aTriggerIds) === 0) {
			return;
		}

		ProcessingHelper::TraceCron('Event: %1$s', $sEventCode);

		// Plain array_merge(): a value passed by the caller must overwrite the base one.
		$aExtraArgs = $oEventData->Get('context_arguments');
		$aContextArgs = static::GroupTextValues(array_merge(static::GetBaseContextArguments($sEventCode), $aExtraArgs), $aExtraArgs);
		$oMailBox = ProcessingHelper::GetMailBoxOrNull();

		foreach($aTriggerIds as $iTriggerId) {

			/** @var TriggerMailToTicketProcessingOnEvent|null $oTrigger */
			$oTrigger = MetaModel::GetObject('TriggerMailToTicketProcessingOnEvent', $iTriggerId, false);
			if($oTrigger === null) {
				continue;
			}

			// - Restricted to a specific mailbox?

				$iTriggerMailBoxId = (int)$oTrigger->Get('mailbox_id');
				if($iTriggerMailBoxId > 0 && ($oMailBox === null || $iTriggerMailBoxId !== (int)$oMailBox->GetKey())) {
					continue;
				}

			$aTriggerArgs = $aContextArgs;

			// 'this->object()' is needed to prevent errors in ActionEmail, which creates a log entry where it assumes a notification is linked to an object.
			// The mailbox is the most relevant object when there is one: unlike TriggerOnMailUpdate, there may not be a related ticket.
			$aTriggerArgs['this->object()'] = $oMailBox ?? $oTrigger;

			if($oTrigger->Get('include_original_message') === 'yes') {

				$oRawEmail = ProcessingHelper::GetMailOrNull()?->oRawEmail;
				if($oRawEmail !== null) {
					$aTriggerArgs['attachments'] = [
						new ormDocument($oRawEmail->GetRawContent(), 'message/rfc822', 'original-message.eml'),
					];
					$sEmlBase64 = base64_encode($oRawEmail->GetRawContent());
					$aTriggerArgs['mail->eml_base64'] = $sEmlBase64;
					$aTriggerArgs['mail->object()'] = ($aTriggerArgs['mail->object()'] ?? new PlaceholderValues([]))->With(['eml_base64' => $sEmlBase64]);
				}

			}

			ProcessingHelper::TraceCron('.. Activating trigger #%1$s ("%2$s").', $oTrigger->GetKey(), $oTrigger->Get('description'));

			try {
				$oTrigger->DoActivate($aTriggerArgs);
			}
			catch(Throwable $e) {
				ProcessingHelper::TraceCron('.. Trigger #%1$s execution error: %2$s', $oTrigger->GetKey(), $e->getMessage());
				utils::EnrichRaisedException($oTrigger, $e);
			}

		}

	}

}

<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.261023
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps;

use JeffreyBostoenExtensions\MailToTicket\{
	eEvent,
	eNextAction,
	Events,
	Helper,
	ProcessingHelper
};

// iTop.
use AttributeDate;
use AttributeDateTime;
use DateTime;
use MetaModel;
use EmailContact;
use EmailMessage;
use EmailProcessor;
use EmailSource;
use MailInboxStandard;
use RawEmailMessage;
use Ticket;


/**
 * Interface iStep defines what the classes implementing steps should look like.
 */
interface iStep {
	
}


/**
 * Enum PolicyBehavior defines the behavior of a policy when it is violated.  
 * Note: Fallbacks are NOT listed here, as they're usually very specific; and are unlikely to be handled in the regular handling.
 */
enum PolicyBehavior : string {
	
	case DELETE = 'delete';
	case DO_NOTHING = 'do_nothing';
	case MARK_AS_UNDESIRED = 'mark_as_undesired';
	case MOVE = 'move';
	case MARK_AS_ERROR = 'mark_as_error';
	case SKIP_FOR_NOW = 'skip_for_now';
	case ABORT_ALL_FURTHER_PROCESSING = 'abort_all_further_processing';

}


/**
 * Class Step. An abstract class defining a step (an action to take in the processing of the e-mail).
 */
abstract class Base implements iStep {

	/**
	 * @var int $iPrecedence It's not necessary that this number is unique; but when all steps are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 */
	public static int $iPrecedence = 20;
	
	/**
	 * @var string $sXMLSettingsPrefix (XML) settings prefix for step.
	 */
	public static string $sXMLSettingsPrefix = 'step_generic';
	
	/**
	 * @var string $sEmailIndex Index in the e-mail source. Note: name is experimental; use methods instead to set/get
	 */
	public static ?string $iEmailIndex = null;
	
	/**
	 * Execution of what needs to happen in this step. This is an individual method; so it can easily be overridden without doing common things defined in the Init() method.
	 *
	 * @return void
	 */
	public static function Execute() : void {
		
	}
	
	/**
	  * Gets the step's XML settings prefix. In the datamodel, especially for policies, settings can be defined. Most common one would be something like 'example_step_behavior'.
	  *
	  * @return string
	 */
	public static function GetXMLSettingsPrefix() : string {
		
		return static::$sXMLSettingsPrefix;
		
	}
	
	/**
	 * Shorthand to obtain a setting configured in the maibox properties for a specific step.
	 *
	 * @return string
	 */
	public static function GetStepSetting(string $sSetting) : string {

		$oMailBox = ProcessingHelper::GetMailBox();
		// - Some settings (e.g. subject, notification) allow null in the datamodel; the column may still hold NULL for existing rows.
		return $oMailBox->Get(static::GetXMLSettingsPrefix().'_'.$sSetting) ?? '';

	}
	
	
	/**
	  * Gets the step's precedence.
	  *
	  * @return int
	 */
	public static function GetPrecedence() : int {
		
		return static::$iPrecedence;
		
	}
    

	/**
	 * @var string[] MAIL_PLACEHOLDER_ATTCODES Attribute codes of the "mail->..." placeholders (see GetMailPlaceholders()).
	 */
	public const MAIL_PLACEHOLDER_ATTCODES = [
		'uidl',
		'message_id',
		'subject',
		'caller_email',
		'caller_email_suffix',
		'reply_address',
		'caller_name',
		'recipient',
		'to_addresses',
		'cc_addresses',
		'date',
		'date_only',
		'time_only',
		'body_text_plain',
		'body_text',
		'body_format',
	];

	/**
	 * Returns the (raw, unescaped) "mail->..." placeholders of the e-mail message.
	 * The e-mail message is not a DBObject, so it can not be passed as "mail->object()"; each property is a scalar placeholder instead.
	 *
	 * @param EmailMessage $oEmail The e-mail message.
	 *
	 * @return array<string, string> Placeholders (e.g. 'mail->subject' => 'Hello world').
	 */
	public static function GetMailPlaceholders(EmailMessage $oEmail) : array {

		// - Date and time parts of the e-mail's own date (formatted as 'Y-m-d H:i:s', see MessageFromMailbox).
		$aDateParts = explode(' ', $oEmail->sDate, 2);

		return [
			'mail->uidl' => $oEmail->sUIDL,
			'mail->message_id' => $oEmail->sMessageId,
			'mail->subject' => $oEmail->sSubject,
			'mail->caller_email' => $oEmail->sCallerEmail,
			'mail->reply_address' => static::GetReplyAddress($oEmail),
			'mail->caller_email_suffix' => explode('@', $oEmail->sCallerEmail, 2)[1] ?? '',
			'mail->caller_name' => $oEmail->sCallerName,
			'mail->recipient' => $oEmail->sRecipient,
			'mail->to_addresses' => static::ImplodeContactAddresses($oEmail->aTos),
			'mail->cc_addresses' => static::ImplodeContactAddresses($oEmail->aCCs),
			'mail->date' => $oEmail->sDate,
			'mail->date_only' => $aDateParts[0],
			'mail->time_only' => $aDateParts[1] ?? '',
			'mail->body_text_plain' => strip_tags($oEmail->sBodyText),
			'mail->body_text' => $oEmail->sBodyText,
			'mail->body_format' => $oEmail->sBodyFormat,
		];

	}

	/**
	 * Gets the address to reply to: the sender's address, or empty when the message was generated automatically (e.g. an out-of-office reply).
	 * Replying to an automated message could start a mail loop. An e-mail action with an empty recipient is not sent.
	 *
	 * @param EmailMessage $oEmail The e-mail message.
	 *
	 * @return string
	 */
	public static function GetReplyAddress(EmailMessage $oEmail) : string {

		if($oEmail->oRawEmail !== null && AutoReply::IsAutoGenerated($oEmail->oRawEmail)) {
			return '';
		}

		return $oEmail->sCallerEmail;

	}

	/**
	 * Gets the e-mail addresses of contacts as a comma-separated string. Contacts without an e-mail address are left out.
	 *
	 * @param EmailContact[] $aContacts Contacts.
	 *
	 * @return string
	 */
	public static function ImplodeContactAddresses(array $aContacts) : string {

		$aAddresses = [];
		foreach($aContacts as $oContact) {
			$sAddress = (string)$oContact->GetEmailAddress();
			if($sAddress !== '') {
				$aAddresses[] = $sAddress;
			}
		}

		return implode(', ', $aAddresses);

	}

	/**
	 * Returns the date/time of processing as placeholders, in iTop's internal formats (so they can be used as values of date/datetime attributes).
	 * Unlike "mail->date", these do not depend on the (sender-controlled) Date header.
	 *
	 * @return array<string, string> Placeholders: 'current_date', 'current_time' and 'current_datetime'.
	 */
	public static function GetDateTimePlaceholders() : array {

		$oNow = new DateTime();

		return [
			'current_date' => $oNow->format(AttributeDate::GetInternalFormat()),
			'current_time' => $oNow->format('H:i:s'),
			'current_datetime' => $oNow->format(AttributeDateTime::GetInternalFormat()),
		];

	}

	/**
	 * Replace email placeholders in a string.
	 *
	 * @param string $sString Input string.
	 * @param array $aExtraPlaceholders Optional: extra place holders.
	 * @param bool $bEscapeHtml Optional: HTML-escape placeholder values (e.g. caller-controlled subject/body) before
	 *   substitution. Required whenever $sString will be sent/rendered as HTML (e.g. the body of an e-mail),
	 *   so attacker-controlled e-mail content can't inject markup. Leave false for non-HTML substitutions, such as
	 *   ticket attribute default values, where the raw value must be preserved.
	 *
	 * @details Also exposes some properties which are not likely to be useful (body_format) at any time, but who knows?
	 *
	 * @return String String where the placeholders are filled in.
	 */
	public static function ReplaceMailPlaceholders(string $sString, array $aExtraPlaceholders = [], bool $bEscapeHtml = false) {

		$oEmail = ProcessingHelper::GetMail();

		$aParams = array_merge(static::GetMailPlaceholders($oEmail), static::GetDateTimePlaceholders());

		if($oEmail->GetSender() !== null) {
			$aParams['sender->object()'] = $oEmail->GetSender();
		}

		$aParams = array_merge($aParams, $aExtraPlaceholders);

		if($bEscapeHtml) {
			foreach($aParams as $sParam => $sValue) {
				if(is_string($sValue)) {
					$aParams[$sParam] = htmlspecialchars($sValue, ENT_QUOTES, 'UTF-8');
				}
			}
		}

		// Extend. This is to allow for both the original parameter and the HTML-encoded version of it.
		$aParamsExtended = [];
		foreach($aParams as $sParam => $sValue) {
			$aParamsExtended[$sParam] = $sValue;
			$aParamsExtended[htmlentities($sParam)] = $sValue;
		}

		return MetaModel::ApplyParams($sString, $aParamsExtended);

	}


	/**
	 * Parses a newline-separated "attcode:value" setting (e.g. a mailbox's default_values field)
	 * into an attcode => value array ready for ProcessingHelper::InitObjectFromDefaultValues(),
	 * applying mail placeholders to each value.
	 *
	 * @param string $sRawValues
	 * @param array $aExtraPlaceholders Optional: extra placeholders, forwarded to ReplaceMailPlaceholders().
	 *
	 * @return array
	 */
	public static function ParseAttributeValues(string $sRawValues, array $aExtraPlaceholders = []) : array {

		$aLines = Helper::SplitByLine($sRawValues);
		$aValues = [];

		foreach($aLines as $sLine) {
			if(preg_match('/^([^:]+):(.*)$/', $sLine, $aMatches)) {
				$sAttCode = trim($aMatches[1]);
				$sValue = trim($aMatches[2]);
				$sValue = static::ReplaceMailPlaceholders($sValue, $aExtraPlaceholders);
				$aValues[$sAttCode] = $sValue;
			}
		}

		return $aValues;

	}


	/**
	 * Trace function used to log information about the e-mail processing.
	 *
	 * @param string $sMessage The message.
	 * @param mixed ...$args
	 *
	 * @return void
	 */
	public static function Trace($sMessage, ...$args) : void {

		$sMessage = (count($args) > 0) ? sprintf($sMessage, ...$args) : $sMessage;
        $oMailBox = ProcessingHelper::GetMailBox();
		$oMailBox->Trace($sMessage);
				
		
	}
	
	
	/**
	 * Returns the e-mail addresses of all recipients in the original e-mail.
	 *
	 * @return string[]
	 */
	public static function GetRecipientAddresses() : array {

		/** @var RawEmailMessage $oRawEmail Raw e-mail message. */
		$oRawEmail = ProcessingHelper::GetRawMail();

		/** @var EmailContact[] $aRecipients An array of recipients. */
		$aRecipients = array_merge($oRawEmail->GetTo(), $oRawEmail->GetCc());

		/** @var string[] $aAddresses An array of e-mail addresses. */
		$aAddresses = [];

		foreach($aRecipients as $aRecipient ) {
			$aAddresses[] = $aRecipient->GetEmailAddress();
		}
		return $aAddresses;

	}
	
	/**
	 * Returns an array containing the e-mail aliases of the mailbox, including the primary e-mail address.
	 *
	 * @return string[]
	 */
	public static function GetMailBoxAliases() : array {
		
		$oMailBox = ProcessingHelper::GetMailBox();
		
		// Retrieve the configured aliases.
		$sMailBoxAliases = $oMailBox->Get('mail_aliases');
		$aMailBoxAliases = (trim($sMailBoxAliases) == '' ? [] : Helper::SplitByLine($sMailBoxAliases));

		// Also retrieve the actual mailbox.
		$aMailBoxAliases[] = $oMailBox->Get('login');
		
		return array_unique($aMailBoxAliases);
		
	}
	
	/**
	 * Gets the event that is fired when this step finds a violation (or, for steps that only recognize a case, a detection).
	 * By convention: "<settings prefix>_violation" or "<settings prefix>_detected".
	 *
	 * The event can be one of this extension (eEvent), or one that another extension provides (see iMailProcessingEventProvider).
	 *
	 * @return string|null Event code. Null when there is no event for this step.
	 */
	public static function GetViolationEvent() : ?string {

		foreach(['_violation', '_detected'] as $sSuffix) {

			$sCode = static::GetXMLSettingsPrefix().$sSuffix;
			if(Events::IsKnownEvent($sCode)) {
				return $sCode;
			}

		}

		return null;

	}

	/**
	 * Gets the context arguments passed to the event of a violation, on top of the general ones.
	 * Steps can extend this with values specific to them.
	 *
	 * @param string $sBehavior The configured behavior.
	 *
	 * @return array
	 */
	protected static function GetViolationContextArguments(string $sBehavior) : array {

		return array_merge(static::GetStepContextArguments(), [
			'behavior' => $sBehavior,
		]);

	}

	/**
	 * Gets the context arguments that identify this step. Passed to every event related to a step.
	 *
	 * @return array
	 */
	public static function GetStepContextArguments() : array {

		return [
			'step_id' => static::GetXMLSettingsPrefix(),
			'step_class' => static::class,
			'step_precedence' => static::$iPrecedence,
		];

	}

	/**
	 * Fires the event of a violation by this step, if there is one.
	 *
	 * @param string $sBehavior The configured behavior.
	 *
	 * @return void
	 */
	protected static function FireViolationEvent(string $sBehavior) : void {

		$sEventCode = static::GetViolationEvent();

		if($sEventCode === null) {
			return;
		}

		Events::FireEvent($sEventCode,static::GetViolationContextArguments($sBehavior));

	}

	/**
	 * Actions executed when the message does not comply with a policy.
	 * Fires the event of the step. To inform the sender or others, link an action to a trigger subscribed to that event (see TriggerMailToTicketProcessingOnEvent).
	 *
	 * @return void
	 */
	public static function HandleViolation() : void {

		// Policy violations have a typical way of handling.
		// The behavior is - besides some feature-specific fallbacks - usually one of the following:
		// - delete -> delete the message
		// - PolicyBehavior::DO_NOTHING->value -> great, lazy. For testing purposes. To actually see if policies are processed or log additional info without doing anything in the end.
		// - mark_as_undesired -> stays in the mailbox for a few days
		// - some sort of fallback -> doesn't matter here

		$sBehavior = static::GetStepSetting('behavior');

		static::Trace('.. Policy violated. Behavior: '.$sBehavior);

		// - Fire the event of this step, regardless of the behavior (e.g. delete) below.

			static::FireViolationEvent($sBehavior);

		// - Set the next action for the mail processor.

			$iNextAction = match($sBehavior) {
				PolicyBehavior::DELETE->value => eNextAction::DELETE_MESSAGE,
				PolicyBehavior::MOVE->value => eNextAction::MOVE_MESSAGE,
				PolicyBehavior::MARK_AS_ERROR->value => eNextAction::MARK_MESSAGE_AS_ERROR,
				PolicyBehavior::MARK_AS_UNDESIRED->value => eNextAction::MARK_MESSAGE_AS_UNDESIRED,
				PolicyBehavior::SKIP_FOR_NOW->value => eNextAction::SKIP_FOR_NOW,
				PolicyBehavior::ABORT_ALL_FURTHER_PROCESSING->value => eNextAction::ABORT_ALL_FURTHER_PROCESSING,
				PolicyBehavior::DO_NOTHING->value => eNextAction::NO_ACTION,
				default => eNextAction::NO_ACTION
			};

			static::Trace('Set next action for EmailProcessor to %1$s', $iNextAction->name);
			ProcessingHelper::SetNextAction($iNextAction);

	}

	/**
	 * Whether the step is applicable. 
	 * Make sure the initiation of the step has already happened!
	 * 
	 * @return bool
	 */
	public static function IsApplicable() : bool {

		return true;

	}

}

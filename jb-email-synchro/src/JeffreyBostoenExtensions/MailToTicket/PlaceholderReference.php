<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.261024
 */

namespace JeffreyBostoenExtensions\MailToTicket;

// iTop internals.
use Dict;

/**
 * Class PlaceholderReference. The placeholders that are available to the actions of a trigger on mail processing events, per event.
 *
 * This is the single source for the reference shown on triggers and actions (see PlaceholderReferenceUIExtension).
 * The descriptions are dictionary entries: "MailProcessingEvent:Placeholder:<Key>".
 *
 * A row is a list of placeholders (without the surrounding "$") with one description key.
 * The values are filled in as far as they are known at that point; the scope of an event decides which groups can be available:
 * - cron: the event and the date and time only.
 * - mailbox: also the mailbox.
 * - message: also the message and the mailbox source. The e-mail itself is not decoded (yet).
 * - mail: everything.
 */
abstract class PlaceholderReference {

	/** @var string[] SCOPES Scopes, from the least to the most information. */
	private const SCOPES = ['cron', 'mailbox', 'message', 'mail'];

	/**
	 * @var array<string, array{scope: string, rows: array<int, array{0: string[], 1: string, 2?: string}>}> GROUPS General placeholders, by group.
	 * The scope is the first scope in which the group is available. A row can have a later scope of its own (third value).
	 */
	private const GROUPS = [
		'Event' => [
			'scope' => 'cron',
			'rows' => [
				[['event->code', 'event->category'], 'EventCode'],
				[['event->label(code)', 'event->label(category)'], 'EventLabel'],
				[['current_date', 'current_time', 'current_datetime'], 'DateTime'],
			],
		],
		'Mail' => [
			'scope' => 'mail',
			'rows' => [
				[['mail->subject'], 'MailSubject'],
				[['mail->caller_email', 'mail->caller_name', 'mail->caller_email_suffix'], 'MailCaller'],
				[['mail->reply_address'], 'MailReplyAddress'],
				[['mail->recipient'], 'MailRecipient'],
				[['mail->to_addresses', 'mail->cc_addresses'], 'MailAddresses'],
				[['mail->date', 'mail->date_only', 'mail->time_only'], 'MailDate'],
				[['mail->body_text', 'mail->body_text_plain', 'mail->body_format'], 'MailBody'],
				[['mail->uidl', 'mail->message_id'], 'MailIdentifiers'],
				[['mail->eml_base64'], 'MailEml'],
			],
		],
		'Message' => [
			'scope' => 'message',
			'rows' => [
				[['message->index'], 'MessageIndex'],
				[['message->uid'], 'MessageUid'],
				[['message->internal_identifier', 'message->message_id'], 'MessageIdentifiers'],
				[['message->timestamp', 'message->timestamp_full'], 'MessageTimestamp'],
				[['source->name', 'source->mailbox_path'], 'SourceName'],
			],
		],
		'Objects' => [
			'scope' => 'mailbox',
			'rows' => [
				[['mailbox->...'], 'Mailbox'],
				[['ticket->...'], 'Ticket', 'mail'],
				[['sender->...'], 'Sender', 'mail'],
			],
		],
		'Details' => [
			'scope' => 'mail',
			'rows' => [
				[['details->next_action'], 'NextAction'],
				[['details->outcome_summary'], 'OutcomeSummary'],
				[['details->executed_steps'], 'ExecutedSteps'],
			],
		],
	];

	/**
	 * @var array<int, array{0: string[], 1: string}> BUILT_IN Placeholders that iTop provides to every action, whatever the trigger.
	 * An action is executed with the trigger and the action itself as context. There is no "this": the object is not related to the trigger.
	 */
	private const BUILT_IN = [
		[['trigger->...'], 'BuiltInTrigger'],
		[['action->...'], 'BuiltInAction'],
		[['current_contact->...', 'current_user->...', 'current_contact_id'], 'BuiltInCurrentUser'],
		[['APP_URL', 'MODULES_URL'], 'BuiltInUrls'],
	];

	/**
	 * Gets the scope of an event.
	 *
	 * @param string $sEventCode Event code.
	 *
	 * @return string One of SCOPES.
	 */
	private static function GetScope(string $sEventCode) : string {

		return match(true) {
			str_starts_with($sEventCode, 'cron_task_') => 'cron',
			str_starts_with($sEventCode, 'mailbox_') => 'mailbox',
			in_array($sEventCode, ['message_started', 'message_decode_failed'], true) => 'message',
			default => 'mail',
		};

	}

	/**
	 * Gets the extra values of an event: the rows on top of the general ones.
	 *
	 * @param string $sEventCode Event code.
	 *
	 * @return array<int, array{0: string[], 1: string}>
	 */
	public static function GetEventRows(string $sEventCode) : array {

		return array_merge(
			static::HasStepValues($sEventCode) ? static::GetStepRows() : [],
			static::IsPolicyEvent($sEventCode) ? static::GetPolicyRows() : [],
			static::GetOwnRows($sEventCode)
		);

	}

	/**
	 * Whether an event is a policy violation. The category is the only thing known about an event of another extension.
	 *
	 * @param string $sEventCode Event code.
	 *
	 * @return bool
	 */
	private static function IsPolicyEvent(string $sEventCode) : bool {

		return (Events::GetKnownEvents()[$sEventCode] ?? '') === 'policy';

	}

	/**
	 * Whether an event tells which step it is about: the events of a step, and the policy events (a step detected the case).
	 *
	 * @param string $sEventCode Event code.
	 *
	 * @return bool
	 */
	private static function HasStepValues(string $sEventCode) : bool {

		return static::IsPolicyEvent($sEventCode) || (Events::GetKnownEvents()[$sEventCode] ?? '') === 'step';

	}

	/**
	 * Gets the extra values that identify the step an event is about.
	 *
	 * @return array<int, array{0: string[], 1: string}>
	 */
	public static function GetStepRows() : array {

		return [
			[['details->step_id', 'details->step_class', 'details->step_precedence'], 'StepInfo'],
		];

	}

	/**
	 * Gets the extra values that every policy event has: what is done about the case, according to the configuration of the mailbox.
	 *
	 * @return array<int, array{0: string[], 1: string}>
	 */
	public static function GetPolicyRows() : array {

		return [
			[['details->behavior'], 'Behavior'],
		];

	}

	/**
	 * Gets the extra values that belong to the event itself: without the ones that every policy event has.
	 *
	 * @param string $sEventCode Event code.
	 *
	 * @return array<int, array{0: string[], 1: string}>
	 */
	private static function GetOwnRows(string $sEventCode) : array {

		return match($sEventCode) {
			eEvent::CRON_TASK_FINISHED->value => [
				[['details->message_count', 'details->processed_count', 'details->deleted_count', 'details->moved_count', 'details->marked_as_error_count', 'details->undesired_count', 'details->skipped_count'], 'CronCounts'],
				[['details->duration_seconds'], 'DurationSeconds'],
			],
			eEvent::MAILBOX_STARTED->value => [
				[['details->message_count'], 'MessageCount'],
			],
			eEvent::MAILBOX_CONNECTION_FAILED->value => [
				[['details->error_message'], 'ErrorMessage'],
			],
			eEvent::MESSAGE_DECODE_FAILED->value => [
				[['details->invalid_reasons'], 'InvalidReasons'],
			],
			eEvent::MESSAGE_DELETED->value,
			eEvent::MESSAGE_MARKED_ERROR->value => [
				[['details->reason'], 'Reason'],
			],
			eEvent::MESSAGE_MOVED->value => [
				[['details->target_folder'], 'TargetFolder'],
			],
			eEvent::MESSAGE_NO_TICKET->value => [
				[['details->reason'], 'Reason'],
			],
			eEvent::CALLER_CREATED->value,
			eEvent::CALLER_INACTIVATED->value => [
				[['caller->...'], 'Caller'],
			],
			eEvent::ERROR_PROCESSING->value => [
				[['details->error_code'], 'ErrorCode'],
				[['details->error_message'], 'ErrorMessage'],
			],
			eEvent::POLICY_SENDER_AUTHENTICATION_VIOLATION->value => [
				[['details->authentication_states'], 'AuthenticationStates'],
			],
			default => [],
		};

	}

	/**
	 * Gets the general placeholders that can be available for an event.
	 *
	 * @param string $sScope One of SCOPES.
	 *
	 * @return array<string, array<int, array{0: string[], 1: string, 2?: string}>> Rows by group.
	 */
	private static function GetGeneralRows(string $sScope) : array {

		$iScope = array_search($sScope, static::SCOPES, true);
		$aGroups = [];

		foreach(static::GROUPS as $sGroup => $aGroup) {

			$aRows = array_values(array_filter(
				$aGroup['rows'],
				fn(array $aRow) => array_search($aRow[2] ?? $aGroup['scope'], static::SCOPES, true) <= $iScope
			));

			if(count($aRows) > 0) {
				$aGroups[$sGroup] = $aRows;
			}

		}

		return $aGroups;

	}

	/**
	 * Translates rows into what is displayed.
	 *
	 * @param array<int, array{0: string[], 1: string}> $aRows
	 *
	 * @return array<int, array{aPlaceholders: string[], sDescription: string}>
	 */
	private static function Translate(array $aRows) : array {

		return array_map(
			fn(array $aRow) => [
				'aPlaceholders' => array_map(fn(string $sPlaceholder) => '$'.$sPlaceholder.'$', $aRow[0]),
				'sDescription' => Dict::S('MailProcessingEvent:Placeholder:'.$aRow[1]),
			],
			$aRows
		);

	}

	/**
	 * Gets the name of an event.
	 *
	 * @param string $sEventCode Event code.
	 *
	 * @return string
	 */
	public static function GetEventLabel(string $sEventCode) : string {

		$sKey = 'Class:MailProcessingEvent/Attribute:code/Value:'.$sEventCode;
		$sLabel = Dict::S($sKey);

		return $sLabel !== $sKey ? $sLabel : $sEventCode;

	}

	/**
	 * Builds the sections of the reference.
	 *
	 * @param string[] $aEventCodes Events to show. Empty: the complete reference.
	 * @param bool $bPerEvent Whether to show everything that is available per event (general and extra values together), instead of the general placeholders once and the extra values per event.
	 *
	 * @return array<int, array{sTitle: string, aRows: array}> Sections.
	 */
	public static function BuildSections(array $aEventCodes, bool $bPerEvent = false) : array {

		// - The built-in placeholders of iTop come first.
		$aSections = [
			[
				'sTitle' => Dict::S('MailProcessingEvent:Placeholder:GroupBuiltIn'),
				'aRows' => static::Translate(static::BUILT_IN),
			],
		];

		// - An event is only mentioned with its extra values.
		$aAllCodes = array_keys(Events::GetKnownEvents());
		$aCodes = count($aEventCodes) > 0 ? array_values(array_intersect($aAllCodes, $aEventCodes)) : $aAllCodes;

		// - One table per event.
		if($bPerEvent) {

			foreach($aCodes as $sCode) {

				$aRows = [];
				foreach(static::GetGeneralRows(static::GetScope($sCode)) as $aGroupRows) {
					$aRows = array_merge($aRows, $aGroupRows);
				}

				$aSections[] = [
					'sTitle' => static::GetEventLabel($sCode),
					'aRows' => static::Translate(array_merge($aRows, static::GetEventRows($sCode))),
				];

			}

			return $aSections;

		}

		// - General placeholders: as far as one of the events can provide them.
		$iMaxScope = 0;
		foreach($aCodes as $sCode) {
			$iMaxScope = max($iMaxScope, (int)array_search(static::GetScope($sCode), static::SCOPES, true));
		}

		foreach(static::GetGeneralRows(static::SCOPES[$iMaxScope]) as $sGroup => $aRows) {
			$aSections[] = [
				'sTitle' => Dict::S('MailProcessingEvent:Placeholder:Group'.$sGroup),
				'aRows' => static::Translate($aRows),
			];
		}

		// - What the events of a step have in common.
		if(count(array_filter($aCodes, fn(string $sCode) => static::HasStepValues($sCode))) > 0) {

			$aSections[] = [
				'sTitle' => Dict::S('MailProcessingEvent:Placeholder:GroupStep'),
				'aRows' => static::Translate(static::GetStepRows()),
			];

		}

		// - What every policy event has in common.
		if(count(array_filter($aCodes, fn(string $sCode) => static::IsPolicyEvent($sCode))) > 0) {

			$aSections[] = [
				'sTitle' => Dict::S('MailProcessingEvent:Placeholder:GroupPolicy'),
				'aRows' => static::Translate(static::GetPolicyRows()),
			];

		}

		// - Extra values: events with the same values share a table.
		$aEventsByRows = [];
		foreach($aCodes as $sCode) {

			$aRows = static::GetOwnRows($sCode);
			if(count($aRows) > 0) {
				$aEventsByRows[serialize($aRows)][] = $sCode;
			}

		}

		foreach($aEventsByRows as $sSerializedRows => $aSharedCodes) {
			$aSections[] = [
				'sTitle' => implode(', ', array_map(fn(string $sCode) => static::GetEventLabel($sCode), $aSharedCodes)),
				'aRows' => static::Translate(unserialize($sSerializedRows)),
			];
		}

		return $aSections;

	}

	/**
	 * Gets the descriptions (dictionary keys) of all rows. Used to check that none is missing.
	 *
	 * @return string[]
	 */
	public static function GetAllDescriptionKeys() : array {

		$aKeys = ['GroupBuiltIn', 'GroupStep', 'GroupPolicy'];

		foreach(static::BUILT_IN as $aRow) {
			$aKeys[] = $aRow[1];
		}

		foreach(array_merge(static::GetStepRows(), static::GetPolicyRows()) as $aRow) {
			$aKeys[] = $aRow[1];
		}

		foreach(static::GROUPS as $sGroup => $aGroup) {

			$aKeys[] = 'Group'.$sGroup;
			foreach($aGroup['rows'] as $aRow) {
				$aKeys[] = $aRow[1];
			}

		}

		foreach(array_keys(Events::GetKnownEvents()) as $sCode) {
			foreach(static::GetEventRows($sCode) as $aRow) {
				$aKeys[] = $aRow[1];
			}
		}

		return array_values(array_unique($aKeys));

	}

}

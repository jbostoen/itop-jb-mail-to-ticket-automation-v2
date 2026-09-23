<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260915
 */


namespace JeffreyBostoenExtensions\MailToTicket;

// iTop internals.
use DBObjectSearch;
use Dict;
use Exception;
use MetaModel;

// iTop events.
use Combodo\iTop\Service\Events\EventData;
use Combodo\iTop\Service\Events\EventService;
use const EVENT_DB_CHECK_TO_WRITE;

// iTop classes.
use MailInboxStandard;
use TriggerOnMailUpdate;

// Module classes.
use JeffreyBostoenExtensions\MailToTicket\Steps\Base as BaseStep;
use JeffreyBostoenExtensions\MailToTicket\Steps\PolicyBehavior;

/**
 * Class EventListener.
 * Registers event listeners for this module. Kept separate from method overloads (such as MailInboxStandard::DoCheckToWrite())
 * defined directly in the datamodel, so both mechanisms can coexist without interfering with each other.
 */
abstract class EventListener {

	/**
	 * Registers the event listeners of this module.
	 *
	 * @return void
	 */
	public static function RegisterListeners() : void {

		EventService::RegisterListener(
			EVENT_DB_CHECK_TO_WRITE,
			[static::class, 'OnMailInboxStandardTargetFolderCheckToWrite'],
			'MailInboxStandard'
		);

		EventService::RegisterListener(
			EVENT_DB_CHECK_TO_WRITE,
			[static::class, 'OnMailInboxStandardAttCodesCheckToWrite'],
			'MailInboxStandard'
		);

		EventService::RegisterListener(
			EVENT_DB_CHECK_TO_WRITE,
			[static::class, 'OnMailInboxStandardCheckToWrite'],
			'MailInboxStandard'
		);

		EventService::RegisterListener(
			EVENT_DB_CHECK_TO_WRITE,
			[static::class, 'OnTriggerOnMailUpdateCheckToWrite'],
			'TriggerOnMailUpdate'
		);

	}

	/**
	 * Validates that the target folder is specified for an active mailbox configured to move e-mails after processing.
	 *
	 * @param EventData $oEventData Event data. Contains the object ('object') being checked.
	 *
	 * @return void
	 */
	public static function OnMailInboxStandardTargetFolderCheckToWrite(EventData $oEventData) : void {

		/** @var MailInboxStandard $oMailInbox */
		$oMailInbox = $oEventData->Get('object');

		if($oMailInbox->Get('active') === 'yes' && $oMailInbox->Get('email_storage') === PolicyBehavior::MOVE->value && trim($oMailInbox->Get('target_folder')) === '') {

			$oMailInbox->AddCheckIssue(Dict::Format('MailInbox:Error:TargetFolderRequired'));

		}

	}

	/**
	 * Validates the description / case log attribute codes, according to the configured behavior.
	 *
	 * @param EventData $oEventData Event data. Contains the object ('object') being checked.
	 *
	 * @return void
	 */
	public static function OnMailInboxStandardAttCodesCheckToWrite(EventData $oEventData) : void {

		/** @var MailInboxStandard $oMailInbox */
		$oMailInbox = $oEventData->Get('object');
		$sTargetClass = $oMailInbox->Get('target_class');

		if(!MetaModel::IsValidClass($sTargetClass)) {
			return;
		}

		$sBehavior = $oMailInbox->Get('behavior');
		$sAttCodeDescription = trim($oMailInbox->Get('attcode_description'));
		$sAttCodeCaseLog = trim($oMailInbox->Get('attcode_caselog'));

		if($sAttCodeDescription !== '' && MetaModel::IsValidAttCode($sTargetClass, $sAttCodeDescription)) {

			if(MetaModel::GetAttributeDef($sTargetClass, $sAttCodeDescription)->GetMaxSize() === null) {

				$oMailInbox->AddCheckIssue(Dict::Format('MailInbox:Error:DescriptionAttCodeMustHaveMaxSize', $sAttCodeDescription, $sTargetClass));

			}

		}

		if($sBehavior === 'both' || $sBehavior === 'update_only') {

			// - An empty or invalid attcode_caselog falls back to 'public_log' at runtime (see GetCaseLogAttCode()),
			//   so this is only an actual problem when the target class has no 'public_log' attribute either.
			$bValidCaseLog = ($sAttCodeCaseLog !== '' && MetaModel::IsValidAttCode($sTargetClass, $sAttCodeCaseLog));
			$bValidFallbackCaseLog = MetaModel::IsValidAttCode($sTargetClass, 'public_log');

			if(!$bValidCaseLog && !$bValidFallbackCaseLog) {

				$oMailInbox->AddCheckIssue(Dict::Format('MailInbox:Error:CaseLogAttCodeRequired', $sTargetClass));

			}

		}
		elseif($sBehavior === 'create_only') {

			$bValidDescription = ($sAttCodeDescription !== '' && MetaModel::IsValidAttCode($sTargetClass, $sAttCodeDescription));
			$bValidCaseLog = ($sAttCodeCaseLog !== '' && MetaModel::IsValidAttCode($sTargetClass, $sAttCodeCaseLog));

			if(!$bValidDescription && !$bValidCaseLog) {

				$oMailInbox->AddCheckIssue(Dict::Format('MailInbox:Error:DescriptionOrCaseLogAttCodeRequired', $sTargetClass));

			}

		}

	}

	/**
	 * Validates that MailInboxStandard::notify_errors_to (an OQL query), when not empty, targets the Contact class or one of its subclasses (e.g. Person, Team).
	 *
	 * @param EventData $oEventData Event data. Contains the object ('object') being checked.
	 *
	 * @return void
	 */
	public static function OnMailInboxStandardCheckToWrite(EventData $oEventData) : void {

		/** @var MailInboxStandard $oMailInbox */
		$oMailInbox = $oEventData->Get('object');
		$sOQL = trim($oMailInbox->Get('notify_errors_to'));

		if($sOQL === '') {
			return;
		}

		try {
			$sTargetClass = DBObjectSearch::FromOQL($sOQL)->GetClass();
		}
		catch(Exception $e) {

			// Malformed OQL is already reported by AttributeOQL's own value check; nothing to add here.
			return;

		}

		if(!MetaModel::IsValidClass($sTargetClass) || !MetaModel::IsParentClass('Contact', $sTargetClass)) {

			$sFieldLabel = MetaModel::GetLabel(get_class($oMailInbox), 'notify_errors_to');
			$oMailInbox->AddCheckIssue(Dict::Format('MailInbox:Error:NotifyErrorsToMustTargetContact', lcfirst($sFieldLabel), $sTargetClass));

		}

	}

	/**
	 * Validates the query arguments used in TriggerOnMailUpdate::filter (an OQL query), when not empty.
	 * Only :this->..., :sender->..., :mail->... and the date/time placeholders (e.g. :current_date) are available when the trigger is activated.
	 * Note: The syntax of the query and its class (target class or a subclass) are already checked by TriggerOnObject::DoCheckToWrite().
	 *
	 * @param EventData $oEventData Event data. Contains the object ('object') being checked.
	 *
	 * @return void
	 */
	public static function OnTriggerOnMailUpdateCheckToWrite(EventData $oEventData) : void {

		/** @var TriggerOnMailUpdate $oTrigger */
		$oTrigger = $oEventData->Get('object');
		$sOQL = trim($oTrigger->Get('filter') ?? '');

		if($sOQL === '') {
			return;
		}

		try {
			$aParams = DBObjectSearch::FromOQL($sOQL)->GetQueryParams();
		}
		catch(Exception $e) {

			// - Malformed OQL is already reported by TriggerOnObject::DoCheckToWrite(); nothing to add here.
			return;

		}

		$sTargetClass = $oTrigger->Get('target_class');
		$bValidTargetClass = MetaModel::IsValidClass($sTargetClass);
		$aDateTimeArgNames = array_keys(BaseStep::GetDateTimePlaceholders());

		foreach(array_keys($aParams) as $sParam) {

			$aParts = explode('->', $sParam, 2);
			$sArgName = $aParts[0];
			$sAttCode = $aParts[1] ?? null;

			$bValid = match(true) {
				$sAttCode === null => in_array($sArgName, $aDateTimeArgNames, true),
				$sArgName === 'this' => ($sAttCode === 'id' || ($bValidTargetClass && MetaModel::IsValidAttCode($sTargetClass, $sAttCode))),
				$sArgName === 'sender' => ($sAttCode === 'id' || MetaModel::IsValidAttCode('Person', $sAttCode)),
				$sArgName === 'mail' => in_array($sAttCode, BaseStep::MAIL_PLACEHOLDER_ATTCODES, true),
				default => false,
			};

			if(!$bValid) {

				$sFieldLabel = MetaModel::GetLabel(get_class($oTrigger), 'filter');
				$oTrigger->AddCheckIssue(Dict::Format('Class:TriggerOnMailUpdate/Error:InvalidFilterArgument',
					$sFieldLabel,
					$sParam,
					implode(', ', BaseStep::MAIL_PLACEHOLDER_ATTCODES),
					implode(', ', array_map(fn($sArgName) => ':'.$sArgName, $aDateTimeArgNames))
				));

			}

		}

	}

}

EventListener::RegisterListeners();

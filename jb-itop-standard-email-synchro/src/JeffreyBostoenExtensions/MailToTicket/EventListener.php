<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260912
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

// iTop classes.
use MailInboxStandard;

// Module classes.
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
			\EVENT_DB_CHECK_TO_WRITE,
			[static::class, 'OnMailInboxStandardTargetFolderCheckToWrite'],
			'MailInboxStandard'
		);

		EventService::RegisterListener(
			\EVENT_DB_CHECK_TO_WRITE,
			[static::class, 'OnMailInboxStandardAttCodesCheckToWrite'],
			'MailInboxStandard'
		);

		EventService::RegisterListener(
			\EVENT_DB_CHECK_TO_WRITE,
			[static::class, 'OnMailInboxStandardCheckToWrite'],
			'MailInboxStandard'
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

}

EventListener::RegisterListeners();

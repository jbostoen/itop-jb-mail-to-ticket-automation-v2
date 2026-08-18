<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260711
 */


namespace JeffreyBostoenExtensions\MailToTicket;

// iTop internals.
use Dict;
use MetaModel;

// iTop events.
use Combodo\iTop\Service\Events\EventData;
use Combodo\iTop\Service\Events\EventService;

// iTop classes.
use MailInboxStandard;

/**
 * Class EventListener.
 * Registers event listeners for this module.
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

	}

	/**
	 * Validates that the target folder is specified for an active mailbox.
	 *
	 * @param EventData $oEventData Event data. Contains the object ('object') being checked.
	 *
	 * @return void
	 */
	public static function OnMailInboxStandardTargetFolderCheckToWrite(EventData $oEventData) : void {

		/** @var MailInboxStandard $oMailInbox */
		$oMailInbox = $oEventData->Get('object');

		if($oMailInbox->Get('active') === 'yes' && trim($oMailInbox->Get('target_folder')) === '') {

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

		if($sBehavior === 'both' || $sBehavior === 'update_only') {

			if($sAttCodeCaseLog === '' || !MetaModel::IsValidAttCode($sTargetClass, $sAttCodeCaseLog)) {

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

}

EventListener::RegisterListeners();

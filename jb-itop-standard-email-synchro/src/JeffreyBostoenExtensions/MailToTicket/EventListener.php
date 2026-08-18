<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260711
 */


namespace JeffreyBostoenExtensions\MailToTicket;

// iTop internals.
use Dict;

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

}

EventListener::RegisterListeners();

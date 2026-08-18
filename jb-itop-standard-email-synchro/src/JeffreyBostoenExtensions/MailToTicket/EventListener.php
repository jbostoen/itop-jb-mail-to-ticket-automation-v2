<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260711
 */


namespace JeffreyBostoenExtensions\MailToTicket;

// iTop internals.
use DBObjectSearch;
use DBObjectSet;
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
			[static::class, 'OnMailInboxStandardCheckToWrite'],
			'MailInboxStandard'
		);

	}

	/**
	 * Validates that the combination of login / server / mailbox (imap) remains unique across mailboxes.
	 *
	 * @param EventData $oEventData Event data. Contains the object ('object') being checked.
	 *
	 * @return void
	 */
	public static function OnMailInboxStandardCheckToWrite(EventData $oEventData) : void {

		/** @var MailInboxStandard $oMailInbox */
		$oMailInbox = $oEventData->Get('object');

		// Note: This MUST be factorized later: declare unique keys (set of columns) in the data model
		$aChanges = $oMailInbox->ListChanges();
		if(!array_key_exists('login', $aChanges) && !array_key_exists('server', $aChanges) && !array_key_exists('mailbox', $aChanges) && !array_key_exists('protocol', $aChanges)) {
			return;
		}

		$sNewLogin = $oMailInbox->Get('login');
		$sNewServer = $oMailInbox->Get('server');
		$sNewMailbox = $oMailInbox->Get('mailbox');

		$oSearch = DBObjectSearch::FromOQL_AllData("SELECT MailInboxBase WHERE login = :newlogin AND server = :newserver AND (protocol = 'imap' AND mailbox = :newmailbox) AND id != :id");
		$oSet = new DBObjectSet($oSearch, [], ['id' => $oMailInbox->GetKey(), 'newlogin' => $sNewLogin, 'newserver' => $sNewServer, 'newmailbox' => $sNewMailbox]);
		if($oSet->Count() > 0) {

			$oMailInbox->AddCheckIssue(Dict::Format('MailInbox:Login/Server/MailboxMustBeUnique', $sNewLogin, $sNewServer, $sNewMailbox));

		}

	}

}

EventListener::RegisterListeners();

<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260711
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps\Core;

use JeffreyBostoenExtensions\MailToTicket\Steps\Base;

use JeffreyBostoenExtensions\MailToTicket\{
	Helper,
	ProcessingHelper
};

// iTop internals.
use CoreCannotSaveObjectException;
use MetaModel;

// Generic.
use Exception;


/**
 * Class SaveReferences. 
 * Creates records to link "In-reply-to" or "References" e-mail headers to the current ticket.  
 * Note that the collecting of this info happens in the MatchByInReplyToOrReferences step, which runs before this one.
 */
abstract class SaveReferences extends Base {
	
	/**
	 * @inheritDoc
	 *
	 * @details This must run after StepCreateOrUpdateTicket.
	 */
	public static int $iPrecedence = 201;
	
	/**
	 * @inheritDoc
	 */
	public static string $sXMLSettingsPrefix = 'step_save_references';
	
	/*
	 * @var string[] $aNewMessageIds Array of previously unknown references in the e-mail message.
	 */
	public static $aNewMessageIds = [];

	/**
	 * @inheritDoc
	 */
	public static function IsApplicable() : bool{

		return true;
		
	}
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oTicket = ProcessingHelper::GetTicket();
		$oMailBox = ProcessingHelper::GetMailBox();
		
		if($oTicket !== null) {
			
			// - Create a record for each reference, so it gets associated with the ticket that was just created/updated.
			foreach(static::$aNewMessageIds as $sMessageId) {
				
				$oLink = MetaModel::NewObject('lnkEmailUidToTicket', [
					'mailbox_id' => $oMailBox->GetKey(),
					'message_uid' => $sMessageId,
					'ticket_id' => $oTicket->GetKey()
				]);
				
				try {
					$oLink->DBInsert();
				}
				catch(CoreCannotSaveObjectException $e) {
					// lnkEmailUidToTicket has a uniqueness rule on (message_uid, mailbox_id, ticket_id);
					// a CheckToWrite() failure here is, in practice, that rule catching an existing link.
					static::Trace('.. Message ID "%1$s" is already linked to ticket ID %2$s (%3$s)', $sMessageId, $oTicket->GetKey(), $e->getMessage());
				}
				catch(Exception $e) {
					// Anything else (DB connectivity, a bad foreign key, ...) is a genuine failure: trace
					// the real cause instead of mislabeling it as "already linked".
					static::Trace('.. Failed to link Message ID "%1$s" to ticket ID %2$s: %3$s', $sMessageId, $oTicket->GetKey(), $e->getMessage());
				}
				
			}
		
		
		}
		
	}
	
}

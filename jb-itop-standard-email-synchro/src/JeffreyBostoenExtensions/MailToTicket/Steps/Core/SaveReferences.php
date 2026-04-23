<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260423
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps\Core;

use JeffreyBostoenExtensions\MailToTicket\Steps\Base;

use JeffreyBostoenExtensions\MailToTicket\{
	Helper,
	ProcessingHelper
};

// iTop internals.
use MetaModel;

// Generic.
use Exception;


/**
 * Class SaveReferences. 
 * Creates records to link "In-reply-to" or "References" e-mail headers to the current ticket.
 */
abstract class SaveReferences extends Base {
	
	/**
	 * @inheritDoc
	 *
	 * @details This must run after StepCreateOrUpdateTicket.
	 */
	public static $iPrecedence = 201;
	
	/**
	 * @inheritDoc
	 */
	public static $sXMLSettingsPrefix = 'step_save_references';
	
	/*
	 * @var string[] $aUIDLs Array of previously unknown references in the e-mail message.
	 */
	public static $aNewUIDLs = [];

	/**
	 * @inheritDoc
	 */
	public static function IsApplicable() : bool{

		// - This step is only applicable if references are being used as UIDs.
		return Helper::UseMessageIdAsUid();
		
	}
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oTicket = ProcessingHelper::GetTicket();
		$oMailBox = ProcessingHelper::GetMailBox();
		
		if($oTicket !== null) {
			
			// Create a record for each reference, so it gets associated with the ticket that was just created/updated.
			foreach(static::$aNewUIDLs as $sUIDL) {
				
				$oLink = MetaModel::NewObject('lnkEmailUidToTicket', [
					'mailbox_id' => $oMailBox->GetKey(),
					'message_uid' => $sUIDL,
					'ticket_id' => $oTicket->GetKey()
				]);
				
				try {
					$oLink->DBInsert();
				}
				catch(Exception $e) {
					static::Trace('.. Message ID "%1$s" is already linked to ticket ID %2$s', $sUIDL, $oTicket->GetKey());
				}
				
			}
		
		
		}
		
	}
	
}

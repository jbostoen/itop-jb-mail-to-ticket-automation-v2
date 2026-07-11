<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260711
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps;

use JeffreyBostoenExtensions\MailToTicket\{
	ProcessingHelper
};


/**
 * Class OtherEmailCallerThanTicketCaller.  
 * Bounce policy in case the email caller is not the ticket caller.
 */
abstract class OtherEmailCallerThanTicketCaller extends Base {
	
	/**
	 * @inheritDoc
	 */
	public static int $iPrecedence = 10;
	
	/**
	 * @inheritDoc
	 */
	public static string $sXMLSettingsPrefix = 'policy_other_email_caller_than_ticket_caller';
		
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		// @todo Accept known contacts (from same org).		
		$oTicket = ProcessingHelper::GetTicket();
		$oEmail = ProcessingHelper::GetMail();
		
		if($oTicket !== null) {
			
			// Other caller?
			$sTicketCallerEmail = $oTicket->Get('caller_id->email');
			if($sTicketCallerEmail != $oEmail->sCallerEmail) {
				
				static::Trace('.. Ticket caller\'s email address is different from the sender\'s email address.');
				static::HandleViolation();
				return;
			
			}
		
		}
		
	}
	
}

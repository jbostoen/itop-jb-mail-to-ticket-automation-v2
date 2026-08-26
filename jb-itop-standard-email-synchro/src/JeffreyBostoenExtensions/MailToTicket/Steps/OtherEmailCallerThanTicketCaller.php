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
			if(strcasecmp($sTicketCallerEmail, $oEmail->sCallerEmail) !== 0) {

				static::Trace('.. Ticket caller\'s email address is different from the sender\'s email address.');
				static::HandleViolation();
				return;

			}

			// A sender address matching the ticket caller's is not sufficient trust: From: is not
			// authenticated by SMTP, so an attacker who knows the caller's address and the ticket
			// reference could otherwise get spoofed content appended to the ticket's public log.
			$oRawEmail = ProcessingHelper::GetRawMail();
			if($oRawEmail->HasFailedAuthentication()) {

				static::Trace(". Ticket caller's email address matches the sender's, but the receiving mail server reported a failed SPF/DKIM check (Authentication-Results) for this message.");
				static::HandleViolation();
				return;

			}

		}
		
	}
	
}

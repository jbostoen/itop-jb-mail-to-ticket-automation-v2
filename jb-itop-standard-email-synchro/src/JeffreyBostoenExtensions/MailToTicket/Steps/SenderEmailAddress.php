<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260423
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps;

use JeffreyBostoenExtensions\MailToTicket\{
	ProcessingHelper
};


/**
 * Class SenderEmailAddress.  
 * A policy that allows accepting only specific email addresses (patterns) for senders.
 */
abstract class SenderEmailAddress extends Base {
	
	/**
	 * @inheritDoc
	 */
	public static $iPrecedence = 10;
	
	/**
	 * @inheritDoc
	 */
	public static $sXMLSettingsPrefix = 'policy_sender_email_address';
		
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oRawEmail = ProcessingHelper::GetRawMail();

		/** @var EmailContact $oSender */
		$sSenderEmail = $oRawEmail->GetSender()[0]->GetEmailAddress();

		
		// Checking if the e-mail address does NOT match the configured pattern.

		$sPatterns = static::GetStepSetting('patterns');

		if(trim($sPatterns) == '') {
			// No patterns specified.
			static::Trace('.. No patterns specified.');
			return;
		}

		$aPatterns = preg_split(static::NEWLINE_REGEX, $sPatterns);
		
		foreach($aPatterns as $sPattern) {

			$oPregMatched = @preg_match($sPattern, $sSenderEmail);
						
			if($oPregMatched === false) {
				static::Trace(". Invalid pattern: '{$sPattern}'");
			}
			elseif(preg_match($sPattern, $sSenderEmail)) {

				static::Trace('.. Undesired: Sender\'s e-mail address.');
				static::HandleViolation();
				return;

			}
			
		}
		
	}
	
}

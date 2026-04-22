<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260421
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps;

use JeffreyBostoenExtensions\MailToTicket\{
	ProcessingHelper
};


/**
 * Class AutoReply.  
 * A policy to handle auto-reply e-mails.
 */
abstract class AutoReply extends Base {
	
	/**
	 * @inheritDoc
	 */
	public static $iPrecedence = 10;
	
	/**
	 * @inheritDoc
	 */
	public static $sXMLSettingsPrefix = 'policy_autoreply';
		
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oRawEmail = ProcessingHelper::GetRawMail();
		
		// Checking if subject is not empty.
		
			$sPolicyBehavior = static::GetStepSetting('behavior');
			
			switch(true) {
				
				case ($oRawEmail->GetHeader('x-autoreply') != ''):
				case ($oRawEmail->GetHeader('x-autorespond') != ''):
				
				case (strtolower($oRawEmail->GetHeader('precedence')) == 'auto_reply'):
				
				// https://www.iana.org/assignments/auto-submitted-keywords/auto-submitted-keywords.xhtml
				// Values for auto-submitted: no, auto-generated, auto-replied, auto-notified
				case (preg_match('/^auto/', strtolower($oRawEmail->GetHeader('auto-submitted')))):
				
					static::Trace('.. The message is an auto-reply.');
					static::HandleViolation();					
					break;
				
				default:
					break;
			
			}
			
		
	}
	
}

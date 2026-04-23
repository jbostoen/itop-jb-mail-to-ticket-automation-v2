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

use utils;

/**
 * Class NoSubject.  
 * A policy to enforce non-empty subjects.
 */
abstract class NoSubject extends Base {
	
	/**
	 * @inheritDoc
	 */
	public static $iPrecedence = 10;
	
	/**
	 * @inheritDoc
	 */
	public static $sXMLSettingsPrefix = 'policy_no_subject';
		
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oEmail = ProcessingHelper::GetMail();
		
		// Checking if subject is not empty.
		
			$sPolicyBehavior = static::GetStepSetting('behavior');
			
			if($oEmail->sSubject == '') {
				
				switch($sPolicyBehavior) {
					 // Will use default subject.
					 case PolicyBehavior::BOUNCE_DELETE->value:
					 case PolicyBehavior::BOUNCE_MARK_AS_UNDESIRED->value:
					 case PolicyBehavior::DELETE->value:
					 case PolicyBehavior::DO_NOTHING->value:
					 case PolicyBehavior::MARK_AS_UNDESIRED->value:

						// No subject (and no fallback)
						static::Trace('.. Undesired: Empty subject.');
						static::HandleViolation();
						
						// No fallback
						
						// Stop processing any further!
						return;
						
						break; // Defensive programming
						
					case 'fallback_default_subject':
					
						// Set ticket title of email message
						// Setting the ticket title on the ticket object happens later and not in this policy!
						$sDefaultTitle = static::GetStepSetting('default_value');
						
						// Inproper configuration
						if(trim($sDefaultTitle) == '') {
							static::Trace('.. Undesired: Empty subject. Fallback to set a default subject failed, because default subject is empty.');
							static::HandleViolation();
							return;
						}
						
						static::Trace('.. Fallback: changing empty subject to "'.$sDefaultTitle.'"');
						$oEmail->sSubject = $sDefaultTitle;
						
						break;
					
					default:
					
						static::Trace('.. Unexpected "behavior"');
						break;
					
				}
			
			}
		
	}
	
}


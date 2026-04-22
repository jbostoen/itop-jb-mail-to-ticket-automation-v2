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
 * Class BounceUndesiredTitlePatterns.  
 * A policy to handle undesired title patterns.
 */
abstract class UndesiredTitlePatterns extends Base {
	
	/**
	 * @inheritDoc
	 */
	public static $iPrecedence = 20;
	
	/**
	 * @inheritDoc
	 */
	public static $sXMLSettingsPrefix = 'policy_undesired_pattern';
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oEmail = ProcessingHelper::GetMail();
		
		// Checking if an undesired title pattern is found

			if(trim(static::GetStepSetting('patterns')) != '' ) { 
			
				// Go over each pattern and check.
				$aPatterns = preg_split(static::NEWLINE_REGEX, static::GetStepSetting('patterns')); 
				$sMailSubject = $oEmail->sSubject;
				
				foreach($aPatterns as $sPattern) {
					if(trim($sPattern) != '') {
							
						$oPregMatched = @preg_match($sPattern, $sMailSubject);
						
						if($oPregMatched === false) {
							static::Trace(".. Invalid pattern: '{$sPattern}'");
						}
						elseif(preg_match($sPattern, $sMailSubject)) {
							
							switch(static::GetStepSetting('behavior')) { 
								case PolicyBehavior::BOUNCE_DELETE->value: 
								case PolicyBehavior::BOUNCE_MARK_AS_UNDESIRED->value:
								case PolicyBehavior::DELETE->value:
								case PolicyBehavior::DO_NOTHING->value:
								case PolicyBehavior::MARK_AS_UNDESIRED->value:
								
									static::Trace(".. The message '{$sMailSubject}' is considered as undesired, since it matches {$sPattern}.");
									static::HandleViolation();
									return;

									break;
									
								default:
									// Should not happen.
									static::Trace(".. Unknown action for closed tickets.");
									break; 
								
							}
							
						}
						else {
							static::Trace(".. Pattern '{$sPattern}' not matched");
						}
					}
				}
			}
		
	}
	
}
<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260711
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps;

use JeffreyBostoenExtensions\MailToTicket\{
	Helper,
	ProcessingHelper
};


/**
 * Class RemoveTitlePatterns. 
 * A policy to remove patterns in titles (in message subject and later ticket title).
 */
abstract class RemoveTitlePatterns extends Base {
	
	/**
	 * @inheritDoc
	 * Must be executed before StepCreateOrUpdateTicket ( precedence = 200 ).
	 */
	public static int $iPrecedence = 110;
	
	/**
	 * @inheritDoc
	 */
	public static string $sXMLSettingsPrefix = 'policy_remove_pattern';
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		// Checking if an undesired title pattern is found
			$oEmail = ProcessingHelper::GetMail();
			
			$sPatterns = static::GetStepSetting('patterns');

			if($sPatterns !== '' ) {
			
				// Go over each pattern and check.
				$aPatterns = Helper::SplitByLine($sPatterns);
				$sMailSubject = $oEmail->sSubject;
				
				foreach($aPatterns as $sPattern) {
					if(trim($sPattern) !== '') {
							
						$oPregMatched = @preg_match($sPattern, $sMailSubject);
						
						if($oPregMatched === false) {
							static::Trace("Invalid pattern: '{$sPattern}'");
						}
						elseif(preg_match($sPattern, $sMailSubject)) {
							
							switch(static::GetStepSetting('behavior')) {
								
								case 'fallback_remove':
								
									$sNewMailSubject = preg_replace($sPattern, '', $sMailSubject);

									if($sMailSubject === $sNewMailSubject) {
										static::Trace("Found pattern to remove: {$sPattern}. Nothing to remove.");
									}
									else {
										static::Trace("Found pattern to remove: {$sPattern}. Removing it.");
									}

									// Update the working copy too, so subsequent patterns match/replace against
									// the already-cleaned subject instead of undoing this removal.
									$sMailSubject = $sNewMailSubject;
									$oEmail->sSubject = $sNewMailSubject;

									break; // Defensive programming
									
								case PolicyBehavior::DO_NOTHING->value:
									// Should not happen.
									static::Trace("Found pattern to remove: {$sPattern}. Doing nothing.");
									break; 
									
								default:
									// Should not happen.
									static::Trace("Unexpected 'behavior' for removing title patterns.");
									break; 
								
							}
							
						}
						else {
							static::Trace(". Pattern '{$sPattern}' not matched");
						}
					}
				}
			}
		
	}
	
}

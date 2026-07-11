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
 * Class TicketResolved.  
 * A policy to handle replies to resolved tickets.
 */
abstract class TicketResolved extends Base {
	
	/**
	 * @inheritDoc
	 */
	public static int $iPrecedence = 20;
	
	/**
	 * @inheritDoc
	 */
	public static string $sXMLSettingsPrefix = 'policy_ticket_resolved';
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oTicket = ProcessingHelper::GetTicket();
		
		// Checking if a previous ticket was found
			if($oTicket !== null && $oTicket->Get('status') == 'resolved') {
					
				static::Trace(". Ticket was marked as resolved before.");
						
				switch(static::GetStepSetting('behavior')) { 
					case PolicyBehavior::BOUNCE_DELETE->value: 
					case PolicyBehavior::BOUNCE_MARK_AS_UNDESIRED->value:
					case PolicyBehavior::DELETE->value:
					case PolicyBehavior::DO_NOTHING->value:
					case PolicyBehavior::MARK_AS_UNDESIRED->value:
					
						static::HandleViolation();
						break;
						 
					case 'fallback_reopen': 
					
						// Reopen ticket
						static::Trace("Fallback: reopen resolved ticket.");
						
						$bRet = $oTicket->ApplyStimulus('ev_reopen');
						
						if($bRet == false) {
							static::Trace('... Stimulus ev_reopen is not possible for this ticket. Hint: the stimulus may not be defined or not allowed in the current ticket state: '.$oTicket->GetState());
						}
						break; 
						
					default:
						// Should not happen.
						static::Trace("Unknown action for resolved tickets.");
						break; 
					
				}
				
			}
		
	}
	
}

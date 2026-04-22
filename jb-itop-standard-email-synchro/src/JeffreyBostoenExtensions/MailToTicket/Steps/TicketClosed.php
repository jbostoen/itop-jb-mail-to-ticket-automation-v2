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
 * Class TicketClosed. A policy to handle replies to closed tickets.
 */
abstract class TicketClosed extends Base {
	
	/**
	 * @inheritDoc
	 */
	public static $iPrecedence = 20;
	
	/**
	 * @inheritDoc
	 */
	public static $sXMLSettingsPrefix = 'policy_ticket_closed';
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oTicket = ProcessingHelper::GetTicket();
		$oMailBox = ProcessingHelper::GetMailBox();
		
		// Checking if a previous ticket was found
			if($oTicket !== null && $oTicket->Get('status') == 'closed') {
						
				switch(static::GetStepSetting('behavior')) { 
					case PolicyBehavior::BOUNCE_DELETE->value: 
					case PolicyBehavior::BOUNCE_MARK_AS_UNDESIRED->value:
					case PolicyBehavior::DELETE->value:
					case PolicyBehavior::DO_NOTHING->value:
					case PolicyBehavior::MARK_AS_UNDESIRED->value:
					
						static::Trace(".. Undesired: ticket was marked as closed before.");
						static::HandleViolation();
						return;

						break; // Defensive programming
						 
					case 'fallback_reopen': 
						// Reopen ticket
						static::Trace("... Fallback: reopen closed ticket."); 
						$bRet = $oTicket->ApplyStimulus('ev_reopen');
						
						if($bRet == false) {
							static::Trace('... Stimulus ev_reopen is not possible for this ticket. Hint: the stimulus may not be defined or not allowed in the current ticket state: '.$oTicket->GetState());
						}
						break; 
						
					default:
						// Should not happen.
						static::Trace("... Unknown action for closed tickets: ".$oMailBox->Get('behavior'));
						break; 
					
					
				}
			}
		
	}
	
}
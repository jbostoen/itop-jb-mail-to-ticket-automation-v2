<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260521
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps;

use JeffreyBostoenExtensions\MailToTicket\{
	eNextAction,
	ProcessingHelper
};

// iTop classes.
use Ticket;

/**
 * Class UnknownTicketReference.  
 * A policy to handle unknown ticket references. Also see GetRelatedTicket().
 */
abstract class UnknownTicketReference extends Base {
	
	/**
	 * @inheritDoc
	 */
	public static $iPrecedence = 10;
	
	/**
	 * @inheritDoc
	 */
	public static $sXMLSettingsPrefix = 'policy_ticket_unknown';
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oMailBox = ProcessingHelper::GetMailBox();
		$oEmail = ProcessingHelper::GetMail();
		$oTicket = ProcessingHelper::GetTicket();
		
		// Is the ticket valid in the iTop database or does the number NOT match?
		// Checking if ticket reference is invalid
		// Due to an earlier GetRelatedTicket() call in MailInboxStandard, 
		// the related ticket should NOT have been null if there was a valid reference to it.
		if($oTicket === null) {
		
			// This could be a new ticket. Then it's logical the Ticket object is null. 
			// So check if there was something (header or pattern in subject) which would have lead the system to believe there was a ticket. 
			
			// Are there patterns which should be ignored/removed from the title? 
			// To find the reference, let's remove it from our temp variable. 
			$sSubject = $oEmail->sSubject;
			
			// Here the removal/ignoring of patterns happens; on a copy of the subject string; only to find related tickets.
			// The only purpose of this is to add some extra debug info.
			foreach(['policy_remove_pattern_patterns', 'title_pattern_ignore_patterns'] as $sAttCode) {

				$sPatterns = $oMailBox->Get($sAttCode);
				
				if(trim($sPatterns) != '') {
					
					$aPatterns = preg_split(static::NEWLINE_REGEX, $sPatterns);
					
					static::Trace("Ignoring patterns (defined in {$sAttCode}): {$sPatterns}");
					
					foreach($aPatterns as $sPattern) {
						if(trim($sPattern) != '') {
							$oPregMatch = @preg_match($sPattern, $sSubject);
							
							if($oPregMatch === false) {
								static::Trace("Invalid pattern: '{$sPattern}'");
							}
							elseif(preg_match($sPattern, $sSubject)) {
								static::Trace("Removing: '{$sPattern}'");
								$sSubject = preg_replace($sPattern, '', $sSubject);
							}
							else {
								// Just not matching
							}
						}
					}
				}
			}
			
			$sPattern = $oMailBox->Get('title_pattern');
			if(($sPattern != '') && (preg_match($sPattern, $sSubject, $aMatches))) {
				static::Trace("ndesired: unable to find any prior ticket despite a matching ticket reference pattern in the subject ('{$sPattern}'). ".http_build_query($aMatches));
				ProcessingHelper::SetNextAction(eNextAction::MARK_MESSAGE_AS_UNDESIRED);
				return;
			}
			elseif($oEmail->oRelatedObject !== null && !($oTicket instanceof Ticket)) {
				static::Trace("Warning: email header references a (valid) non-ticket object ({$oEmail->oRelatedObject}).");
			}
			else {
				static::Trace("Not undesired? Pattern = ".$sPattern." - subject: ".$sSubject);
			}
		
		}
		else {
			static::Trace("Already linked to a Ticket");
		}
		
	}
	
}

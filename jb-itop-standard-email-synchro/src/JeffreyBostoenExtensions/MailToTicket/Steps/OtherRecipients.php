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
 * Class OtherRecipients.  
 * A policy to ensure that the service desk mailbox is the sole recipient (no other recipients in To:, CC:).
 * 
 * @details Does NOT change "related contacts" or create new ones!
 * This step only checks whether the e-mail needs to be bounced.
 * For the actual creation/linking of contacts, see FindAdditionalContacts.
 * 
 * Reason: The need to 'bounce' just comes way earlier than the handling of iTop person objects.
 */
abstract class OtherRecipients extends Base {
	
	/**
	 * @inheritDoc
	 */
	public static int $iPrecedence = 20;
	
	/**
	 * @inheritDoc
	 */
	public static string $sXMLSettingsPrefix = 'policy_other_recipients';
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oEmail = ProcessingHelper::GetMail();
		
		// Checking if there are no other recipients mentioned.
		
			// Take both the To: and CC:
			/** @var EmailContact[] $aAllContacts All contacts (except BCC). */
			$aAllContacts = array_merge($oEmail->aTos, $oEmail->aCCs);
			
			// Mailbox aliases
			$aMailBoxAliases = static::GetMailBoxAliases();
			
			// Ignore sender; helpdesk mailbox; any helpdesk mailbox aliases
			$aAllowedContacts = array_merge([ $oEmail->sCallerEmail ], $aMailBoxAliases);
			$aAllowedContacts = array_unique($aAllowedContacts);

			$sPolicyBehavior = static::GetStepSetting('behavior');
			
			switch($sPolicyBehavior) {

				// Perform action if:
				case PolicyBehavior::BOUNCE_DELETE->value:
				case PolicyBehavior::BOUNCE_MARK_AS_UNDESIRED->value:
				case PolicyBehavior::DELETE->value:
				case PolicyBehavior::MARK_AS_UNDESIRED->value:
				
					foreach($aAllContacts as $oRecipient) {
						
						$sCurrentEmail = $oRecipient->GetEmailAddress();
						
						foreach($aAllowedContacts as $sPattern) {
							
							// Regular e-mail address? Make case insensitive pattern
							if(filter_var($sPattern, FILTER_VALIDATE_EMAIL) == true) {
								$sPattern = '/^'.preg_quote($sPattern).'$/i';
							}
							$oPregMatch = @preg_match($sPattern, $sCurrentEmail);
							
							if($oPregMatch === false) {

								// Pattern mistake: skip this pattern only, still check the remaining allowed patterns.
								static::Trace(". Invalid pattern: '{$sPattern}'");
								continue;
							}
							elseif(preg_match($sPattern, $sCurrentEmail)) {
								
								// This e-mail (from:) is allowed; it's within the allowed contacts.
								continue 2;
								
							}
							
						}
						
						// Did not match caller e-mail or any alias of this helpdesk mailbox
						static::Trace(". Undesired: at least one other recipient (missing alias or unwanted other recipient): {$oRecipient->GetEmailAddress()}");
						static::HandleViolation();
						return;
						
					}

					break; // Defensive programming
				
				// These behaviors will be handled in PolicyFindAdditionalContacts:
				case PolicyBehavior::DO_NOTHING->value:
				case 'fallback_ignore_other_contacts':
				case 'fallback_add_other_contacts':
				case 'fallback_add_existing_other_contacts':
					
					break;
				
				default:
					static::Trace(". Unexpected 'behavior'");
					break;
				
			}
		
	}
	
}

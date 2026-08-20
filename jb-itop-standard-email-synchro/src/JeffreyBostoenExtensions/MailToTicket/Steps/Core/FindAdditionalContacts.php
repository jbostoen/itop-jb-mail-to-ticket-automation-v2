<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260711
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps\Core;

use JeffreyBostoenExtensions\MailToTicket\Steps\{
	Base,
	PolicyBehavior
};

use JeffreyBostoenExtensions\MailToTicket\{
	ProcessingHelper
};

// iTop internals.
use DBObjectSearch;
use DBObjectSet;

// iTop classes.
use Person;

// Generic.
use Exception;

/**
 * Class FindAdditionalContacts. A policy to add "related contacts" to a Ticket. These are people in To: or CC: of processed e-mails that are NOT the original caller.
 */
abstract class FindAdditionalContacts extends Base {
	
	/**
	 * @inheritDoc
	 */
	public static int $iPrecedence = 115;

	/**
	 * @inheritDoc
	 */
	public static string $sXMLSettingsPrefix = 'policy_other_recipients';
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oEmail = ProcessingHelper::GetMail();
		$oMailBox = ProcessingHelper::GetMailBox();
		$oTicket = ProcessingHelper::GetTicket();
		$sPolicyBehavior = static::GetStepSetting('behavior');
		
		$sCallerEmail = $oEmail->sCallerEmail;
							
		// Take both the To: and CC:
		/** @var EmailContact[] All e-mail recipients in To: and CC: */
		$aAllContacts = array_merge($oEmail->aTos, $oEmail->aCCs);
		
		// Mailbox aliases
		$aExcludedAddresses = static::GetMailBoxAliases();
		
		// Ignore helpdesk mailbox; any helpdesk mailbox aliases, original caller's email address
		if($oTicket !== null) {
			// For existing tickets: other people might reply.
			// So only exclude mailbox aliases and the original caller (who may be different from the current e-mail sender).
			// If it's someone else replying, it should be seen as a new contact.
			$aExcludedAddresses[] = $oTicket->Get('caller_id->email');
			
		}
		else {
			$aExcludedAddresses[] = $sCallerEmail;
		}
		
		$aExcludedAddresses = array_map('mb_strtolower', $aExcludedAddresses);
		$aExcludedAddresses = array_unique($aExcludedAddresses);
		
		static::Trace(". E-mail addresses to exclude: ".implode(', ', $aExcludedAddresses));

		// While the mailbox may be configured NOT to *create* or *link* related contacts,
		// It may still be good to match them to iTop objects if they do exist.
		
		switch($sPolicyBehavior) {
			
			case 'fallback_add_existing_other_contacts':
			case 'fallback_add_other_contacts':
		
				// Loop over all contacts again to have access to the 'email' and 'name'.
				foreach($aAllContacts as $oRecipient) {
					
					$sRecipientEmail = $oRecipient->GetEmailAddress();
					$sRecipientName = $oRecipient->GetName();
					
					// Is this an e-mail address that should be ignored?
					if(in_array(mb_strtolower($sRecipientEmail), $aExcludedAddresses) == true) {
						static::Trace('.. Ignore '.$sRecipientEmail.' ('.$sRecipientName.')');
						continue;
					}
					
					// Found other contacts in To: or CC:
							
					// Check if this contact exists.
					// Non-existing contacts must be created.
					// Actual linking of contacts happens after policies have been processed.
					$sContactQuery = 'SELECT Person WHERE email = :email';
					$oSet_Person = new DBObjectSet(DBObjectSearch::FromOQL($sContactQuery), [], [
						'email' => $sRecipientEmail
					]);
					
					static::Trace(". Results for Person with email address '{$sRecipientEmail}: {$oSet_Person->Count()}");
					
					if($oSet_Person->Count() == 0) {
						
						if($sPolicyBehavior != 'fallback_add_other_contacts') {
							
							// No existing contacts were found; and no new person should be created.

						}
						elseif($sPolicyBehavior == 'fallback_add_other_contacts') {
							
							// Create
							static::Trace(". Creating a new Person with email address '{$sRecipientEmail}'");
							$oContact = new Person();
							$oContact->Set('email', $sRecipientEmail);
							$sDefaultValues = static::GetStepSetting('default_values');
							$aDefaultValues = static::ParseAttributeValues($sDefaultValues, [
								'recipient->name' => $sRecipientName,
								'recipient->email' => $sRecipientEmail,
							]);

							ProcessingHelper::InitObjectFromDefaultValues($oContact, $aDefaultValues);
							try {
								static::Trace("Try to create user with default values");
								$oContact->DBInsert();

								// Add Person to list of additional Contacts (handled in PolicyCreateOrUpdateTicket)
								$oEmail->AddRelatedContact($oContact);
								
							}
							catch(Exception $e) {
								// This is an actual error.
								static::Trace("Failed to create a Person for the email address '{$sCallerEmail}'.");
								static::Trace($e->getMessage());
								ProcessingHelper::HandleError('failed_to_create_contact');
								return;
							}

						}
						
					}
					elseif($oSet_Person->Count() == 1) {
						// Add Person to list of additional Contacts (handled in PolicyCreateOrUpdateTicket)
						$oContact = $oSet_Person->Fetch();
						$oEmail->AddRelatedContact($oContact);
					}
					else {
						// More than one Person returned. Inconclusive. Ignore?
						static::Trace(". Multiple Persons found. Returning the first one.");
						$oContact = $oSet_Person->Fetch();
						$oEmail->AddRelatedContact($oContact);
						
					}
					
					
				}
				break;
				
			case PolicyBehavior::BOUNCE_DELETE->value:
			case PolicyBehavior::BOUNCE_MARK_AS_UNDESIRED->value:
			case PolicyBehavior::DELETE->value:
			case PolicyBehavior::DO_NOTHING->value:
			case PolicyBehavior::MARK_AS_UNDESIRED->value:
			
				// Will be added automatically later
				break;
				
			case 'fallback_ignore_other_contacts':
			
				// Make sure these contacts are not processed in any further processing?
				$oEmail->aTos = [];
				$oEmail->aCCs = [];
			
				static::Trace(". Ignoring other contacts");
				break;
			
			default:
				static::Trace(". Unexpected 'behavior'");
				break;
			
		}
		
	}
	
}

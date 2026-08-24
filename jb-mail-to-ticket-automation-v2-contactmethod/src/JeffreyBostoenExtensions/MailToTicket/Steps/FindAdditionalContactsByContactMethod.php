<?php

/**
 * @copyright   Copyright (c) 2019-2026 Jeffrey Bostoen
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     3.2.260711
 *
 * 
 */
 
namespace JeffreyBostoenExtensions\MailToTicket\Steps;

use JeffreyBostoenExtensions\MailToTicket\ProcessingHelper;

// iTop internals.
use MetaModel;
use Person;
use Ticket;

// Mail.
use EmailMessage;
use RawEmailMessage;

/**
 * Class FindAdditionalContactsByContactMethod. Step to find the additional recipients (Person) based on ContactMethod.
 *
 * Keep in mind: e-mail address might be shared by multiple people. This is only a basic implementation.
 *
 */
abstract class FindAdditionalContactsByContactMethod extends Base {
	
	/**
	 * @inheritDoc
	 *
	 */
	// Should be executed before StepFindAdditionalContacts;
	// therefore $iPrecedence should be lower than that of StepFindAdditionalContacts (115).
	public static int $iPrecedence = 114;

	/**
	 * @var string[] Lowercased e-mail addresses that were resolved to an existing Person by this step,
	 * for the current e-mail being processed. Read by the core FindAdditionalContacts step (precedence
	 * 115) so it doesn't re-process (and potentially create a duplicate Person for) the same recipient
	 * via its own, primary-email-only lookup.
	 */
	public static array $aResolvedRecipientEmails = [];

	/**
	 * @inheritDoc
	 * @details Shares the core FindAdditionalContacts step's setting, so both matching mechanisms
	 * honor the same admin-facing "other recipients" policy.
	 */
	public static string $sXMLSettingsPrefix = 'policy_other_recipients';

	/**
	 * @inheritDoc
	 * @details Checks if all information within the e-mail is compliant with the steps defined for this mailbox
	 *
	 */
	public static function Execute() : void {

		// Reset before processing each mail.
		static::$aResolvedRecipientEmails = [];

		// Checking if there's an unknown caller

			// Don't even bother if jb-contactmethod is not enabled as an extension.
			if(!MetaModel::IsValidClass('ContactMethod') && !MetaModel::IsValidClass('EmailAlias')) {
				static::Trace(". Step not relevant: No relevant classes exist (ContactMethod, EmailAlias).");
				return;
			}

			$sPolicyBehavior = static::GetStepSetting('behavior');
			if(!in_array($sPolicyBehavior, ['fallback_add_existing_other_contacts', 'fallback_add_other_contacts'], true)) {
				static::Trace(". Step not relevant: policy_other_recipients_behavior is '{$sPolicyBehavior}', not one of the 'add' behaviors.");
				return;
			}

			/** @var EmailMessage $oEmail E-mail message. */
			$oEmail = ProcessingHelper::GetMail();
			
			/** @var RawEmailMessage $oRawEmail Raw e-mail message. */
			$oRawEmail = ProcessingHelper::GetRawMail();

			/** @var Ticket $oTicket The ticket. */
			$oTicket = ProcessingHelper::GetTicket();
			
			$sSenderEmail = $oRawEmail->GetSender()[0]->GetEmailAddress();
			
			$aRecipients = static::GetRecipientAddresses();
			$aMailBoxAliases = static::GetMailBoxAliases();

			// Ignore e-mail addresses of:
			// - Primary e-mail address of this mailbox; and its aliases.
			// - The original caller's e-mail address.
			
			// For existing tickets: Other people might reply. 
			// So only exclude mailbox aliases and the original caller.
			// If it's someone else replying, it should be seen as a new contact.

			// For new tickets: exclude the current sender.

			$sOriginalCallerEmail = ($oTicket !== null ? $oTicket->Get('caller_id->email') : $sSenderEmail);
			$aRemainingContacts = array_udiff($aRecipients, array_merge([ $sOriginalCallerEmail ], $aMailBoxAliases), 'strcasecmp');

			// Make sure there are no duplicates now.
			$aRemainingContacts = array_unique($aRemainingContacts);
			
			// For each recipient: Try to find the person object.
			foreach($aRemainingContacts as $sCurrentEmail) {
			
				/** @var Person|null $oCaller The related person. */
				$oPerson = StepFindCallerByContactMethod::FindContactByEmail($sCurrentEmail);

				if(StepFindCallerByContactMethod::$bLastMatchAmbiguous) {
					static::Trace(". Ambiguous match for '{$sCurrentEmail}': multiple people share this contact detail, linking the first one found.");
				}

				// Only if there is a match.
				if($oPerson !== null) {

					static::$aResolvedRecipientEmails[] = mb_strtolower($sCurrentEmail);

					// Add to related contacts.
					$oEmail->AddRelatedContact($oPerson);
					
					// Don't update the primary e-mail address.
					// Only do so if the e-mail is sent by the person!
					// static::Trace(". Update person {$oPerson->Get('friendlyname')} - Set primary e-mail to {$sCurrentEmail}");
					// $oPerson->Set('email', $sCurrentEmail);

				}
				
			}
			
		
		
	}
	
}



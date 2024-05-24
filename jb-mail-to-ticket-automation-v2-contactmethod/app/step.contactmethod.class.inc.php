<?php

/**
 * @copyright   Copyright (c) 2019-2024 Jeffrey Bostoen
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     2.7.240524
 *
 * 
 */
 
namespace jb_itop_extensions\mail_to_ticket;

// iTop internals
use \DBObjectSet;
use \DBObjectSearch;
use \MetaModel;
use \Person;

// Generic
use \Exception;
 
/**
 * Class StepFindCallerByContactMethod. Step to find the caller (Person) based on ContactMethod.
 *
 * Keep in mind: e-mail address might be shared by multiple people. This is only a basic implementation.
 *
 */
abstract class StepFindCallerByContactMethod extends Step {
	
	/**
	 * @inheritDoc
	 *
	 */ 
	// Should be executed before StepFindCaller;
	// therefore $iPrecedence should be lower than that of StepFindCaller (110).
	public static $iPrecedence = 109;
	
	/**
	 * Finds contacts by contact method (ContactMethod) or e-mail alias (Combodo's EmailAlias).
	 * 
	 * @param \String $sEmail E-mail address.
	 *
	 * @return \Person|null
	 */
	public static function FindContactByEmail(String $sEmail) : ?Person {

		/** @var \Person $oPerson|null A person object in iTop. */
		$oPerson = null;

		foreach([
			'ContactMethod' => 'SELECT Person AS p JOIN ContactMethod AS c ON c.person_id = p.id WHERE c.contact_method = "email" AND c.contact_detail LIKE :email',
			'EmailAlias' => 'SELECT Person AS p JOIN EmailAlias AS a ON a.contact_id = p.id WHERE a.email = :email'
		] as $sClass => $sOQL) {

			// The class must exist.
			if(MetaModel::IsValidClass($sClass) == true) {
				
				// Find person objects; oldest first.
				$oFilter_Person = DBObjectSearch::FromOQL_AllData($sOQL);
				$oSet_Person = new DBObjectSet($oFilter_Person, ['id' => true], [
					'email' => $sEmail	
				]);
				$oPerson = $oSet_Person->Fetch();

				static::Trace('... OQL '.$sClass.' returned '.$oSet_Person->Count().' results.');

				// If one of the queries finds a person: exit.
				if($oPerson !== null) {
					break;
				}

			}

		}

		return $oPerson;

	}
	
	/**
	 * @inheritDoc 
	 * @details Checks if all information within the e-mail is compliant with the steps defined for this mailbox
	 */
	public static function Execute() {
		

		/** @var \EmailMessage $oEmail E-mail message. */
		$oEmail = static::GetMail();

		/** @var \RawEmailMessage $oRawEmail Raw e-mail message. */
		$oRawEmail = static::GetRawMail();
	
		// Don't even bother if jb-contactmethod is not enabled as an extension.
		if(MetaModel::IsValidClass('ContactMethod') == false && MetaModel::IsValidClass('EmailAlias') == false) {
			static::Trace(".. Step not relevant: No relevant classes exist (ContactMethod, EmailAlias).");
			return;
		}

		/** @var \Person $oCaller Person. */
		$oCaller = $oEmail->GetSender();
		
		// Don't bother if the caller is already determined.
		if($oCaller !== null) {
			static::Trace("... Caller already determined by previous step. Skip.");
		}

		$sCallerEmail = $oRawEmail->GetSender()[0]->GetEmailAddress();

		/** @var \Person|null $oCaller The related person. */
		$oPerson = StepFindCallerByContactMethod::FindContactByEmail($sCallerEmail);

		if($oPerson === null) {
			return;
		}

		// Update the e-mail address (on the person object) to the one which was used last by the caller.
		static::Trace(".. Update person {$oPerson->Get('friendlyname')} - Set primary e-mail to {$sCallerEmail}");
		$oPerson->Set('email', $sCallerEmail);
		$oPerson->DBUpdate();
		
		// Set caller for email
		$oEmail->SetSender($oPerson);
	
	}
	
}


/**
 * Class StepFindAdditionalContactsByContactMethod. Step to find the additional recipients (Person) based on ContactMethod.
 *
 * Keep in mind: e-mail address might be shared by multiple people. This is only a basic implementation.
 *
 */
abstract class StepFindAdditionalContactsByContactMethod extends Step {
	
	/**
	 * @inheritDoc
	 *
	 */
	// Should be executed before StepFindAdditionalContacts; 
	// therefore $iPrecedence should be lower than that of StepFindAdditionalContacts (115).
	public static $iPrecedence = 114;
	
	/**
	 * @inheritDoc
	 * @details Checks if all information within the e-mail is compliant with the steps defined for this mailbox
	 *
	 */
	public static function Execute() {
		
		// Checking if there's an unknown caller
		
			// Don't even bother if jb-contactmethod is not enabled as an extension.
			if(MetaModel::IsValidClass('ContactMethod') == false && MetaModel::IsValidClass('EmailAlias') == false) {
				static::Trace(".. Step not relevant: No relevant classes exist (ContactMethod, EmailAlias).");
				return;
			}
			
			/** @var \EmailMessage $oEmail E-mail message. */
			$oEmail = static::GetMail();
			
			/** @var \RawEmailMessage $oRawEmail Raw e-mail message. */
			$oRawEmail = static::GetRawMail();

			/** @var \Ticket $oTicket The ticket. */
			$oTicket = static::GetTicket();
			
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
			
				/** @var \Person|null $oCaller The related person. */	
				$oPerson = StepFindCallerByContactMethod::FindContactByEmail($sCurrentEmail);
				
				// Only if there is a match.
				if($oPerson !== null) {

					// Add to related contacts.
					$oEmail->AddRelatedContact($oPerson);
					
					// Don't update the primary e-mail address.
					// Only do so if the e-mail is sent by the person!
					// static::Trace(".. Update person {$oPerson->Get('friendlyname')} - Set primary e-mail to {$sCurrentEmail}");
					// $oPerson->Set('email', $sCurrentEmail);
					$oPerson->DBUpdate();
					
				
				}
				
			}
			
		
		
	}
	
}



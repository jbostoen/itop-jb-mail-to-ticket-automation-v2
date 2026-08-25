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
use DBObjectSet;
use DBObjectSearch;
use MetaModel;
use Person;

// Mail.
use EmailMessage;
use RawEmailMessage;
 
/**
 * Class StepFindCallerByContactMethod. Step to find the caller (Person) based on ContactMethod.
 *
 * Keep in mind: e-mail address might be shared by multiple people. This is only a basic implementation.
 *
 */
abstract class StepFindCallerByContactMethod extends Base {
	
	/**
	 * @inheritDoc
	 *
	 */ 
	// Should be executed before StepFindCaller;
	// therefore $iPrecedence should be lower than that of StepFindCaller (110).
	public static int $iPrecedence = 109;

	/** @var bool $bLastMatchAmbiguous Whether the last FindContactByEmail() call matched more than one Person. */
	public static bool $bLastMatchAmbiguous = false;

	/**
	 * Finds contacts by contact method (ContactMethod) or e-mail alias (Combodo's EmailAlias).
	 *
	 * @param string $sEmail E-mail address.
	 *
	 * @return Person|null
	 */
	public static function FindContactByEmail(String $sEmail) : ?Person {

		static::$bLastMatchAmbiguous = false;

		/** @var Person $oPerson|null A person object in iTop. */
		$oPerson = null;

		foreach([
			'ContactMethod' => 'SELECT Person AS p JOIN ContactMethod AS c ON c.person_id = p.id WHERE c.contact_method = "email" AND c.contact_detail = :email',
			'EmailAlias' => 'SELECT Person AS p JOIN EmailAlias AS a ON a.contact_id = p.id WHERE a.email = :email'
		] as $sClass => $sOQL) {

			// The class must exist.
			if(MetaModel::IsValidClass($sClass) == true) {

				// Find person objects; oldest first.
				$oFilter_Person = DBObjectSearch::FromOQL_AllData($sOQL);
				$oSet_Person = new DBObjectSet($oFilter_Person, ['id' => true], [
					'email' => $sEmail
				]);

				static::Trace(sprintf('... OQL %1$s returned %2$s results.', $sClass, $oSet_Person->Count()));

				if($oSet_Person->Count() > 1) {
					static::Trace("Found {$oSet_Person->Count()} people matching '{$sEmail}' via {$sClass}, the first one will be used...");
					static::$bLastMatchAmbiguous = true;
				}

				$oPerson = $oSet_Person->Fetch();

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
	public static function Execute() : void {
		

		/** @var\EmailMessage $oEmail E-mail message. */
		$oEmail = ProcessingHelper::GetMail();

		/** @var RawEmailMessage $oRawEmail Raw e-mail message. */
		$oRawEmail = ProcessingHelper::GetRawMail();
	
		// Don't even bother if jb-contactmethod is not enabled as an extension.
		if(MetaModel::IsValidClass('ContactMethod') == false && MetaModel::IsValidClass('EmailAlias') == false) {
			static::Trace(". Step not relevant: No relevant classes exist (ContactMethod, EmailAlias).");
			return;
		}

		/** @var Person $oCaller Person. */
		$oCaller = $oEmail->GetSender();
		
		// Don't bother if the caller is already determined.
		if($oCaller !== null) {
			static::Trace("Caller already determined by previous step. Skip.");
			return;
		}

		$sCallerEmail = $oRawEmail->GetSender()[0]->GetEmailAddress();

		if(preg_match('/\b(spf|dkim)=fail\b/i', $oRawEmail->GetHeader('authentication-results'))) {
			static::Trace("Refusing to trust '{$sCallerEmail}' as a contact method match: the receiving mail server reported a failed SPF/DKIM check (Authentication-Results) for this message.");
			return;
		}

		/** @var Person|null $oCaller The related person. */
		$oPerson = StepFindCallerByContactMethod::FindContactByEmail($sCallerEmail);

		if($oPerson === null) {
			return;
		}

		// A match via a secondary/alternate contact method is weaker evidence than a match on the primary
		// email address itself; only sync it as the person's primary e-mail when the match was unambiguous,
		// to avoid overwriting the wrong person's contact data when a contact detail is shared (e.g. a team mailbox).
		if(static::$bLastMatchAmbiguous) {
			static::Trace(". Ambiguous match for '{$sCallerEmail}': not updating {$oPerson->Get('friendlyname')}'s primary e-mail.");
		}
		else {
			static::Trace(". Update person {$oPerson->Get('friendlyname')} - Set primary e-mail to {$sCallerEmail}");
			$oPerson->Set('email', $sCallerEmail);
			$oPerson->DBUpdate();
		}

		// Set caller for email
		$oEmail->SetSender($oPerson);

	}

}


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
 * Class FindCaller.  
 * A policy to find the caller and create a Person with default values if the caller appears to be unknown.
 */
abstract class FindCaller extends Base {
	
	/**
	 * @inheritDoc
	 */
	public static int $iPrecedence = 110;
	
	/**
	 * @inheritDoc
	 * @details Note: different short name because of (legacy) attribute fields!
	 */
	public static string $sXMLSettingsPrefix = 'policy_unknown_caller';
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		$oMailBox = ProcessingHelper::GetMailBox();
		$oEmail = ProcessingHelper::GetMail();
		$oRawEmail = ProcessingHelper::GetRawMail();
		$oCaller = $oEmail->GetSender();
		
		// Checking if there's an unknown caller
		
			if($oCaller === null) {
				
				$sCallerEmail = $oRawEmail->GetSender()[0]->GetEmailAddress();
				static::Trace("Determine caller: Person with email '{$sCallerEmail}'");
				
				$oCaller = null;
				$sContactQuery = 'SELECT Person WHERE email = :email';
				
				$oSet_Person = new DBObjectSet(DBObjectSearch::FromOQL($sContactQuery), [], ['email' => $sCallerEmail]);
				
				switch($oSet_Person->Count()) {
					
					case 1:
						// Ok, the caller was found in iTop
						static::Trace("Found person.");
						$oCaller = $oSet_Person->Fetch();
						break;
						
					case 0:

						// Caller was not found.
						switch(static::GetStepSetting('behavior')) {
							
							case PolicyBehavior::BOUNCE_DELETE->value:
							case PolicyBehavior::BOUNCE_MARK_AS_UNDESIRED->value:
							case PolicyBehavior::DELETE->value:
							case PolicyBehavior::MARK_AS_UNDESIRED->value:
							
								static::Trace("The message '{$oEmail->sSubject}' is considered as undesired, the caller was not found.");
								static::HandleViolation();
								return;
								break;

							case 'fallback_create_person':

								if(preg_match('/\b(spf|dkim)=fail\b/i', $oRawEmail->GetHeader('authentication-results'))) {

									static::Trace("Refusing to create a Person for '{$sCallerEmail}': the receiving mail server reported a failed SPF/DKIM check (Authentication-Results) for this message.");
									static::HandleViolation();
									return;

								}

								static::Trace("Creating a new Person for the email: {$sCallerEmail}");
								$oCaller = new Person();
								$oCaller->Set('email', $oEmail->sCallerEmail);
								$sDefaultValues = static::GetStepSetting('default_values');
								
								if(trim($sDefaultValues) != '') {
									
									try {

										$aDefaultValues = static::ParseAttributeValues($sDefaultValues);

										static::Trace('... Default values: '.http_build_query($aDefaultValues));
										ProcessingHelper::InitObjectFromDefaultValues($oCaller, $aDefaultValues);
										
										static::Trace("Create user with default values");
										$oCaller->DBInsert();					
									}
									catch(Exception $e) {
										// This is an actual error.
										static::Trace("Failed to create a Person for the email address '{$sCallerEmail}'.");
										static::Trace($e->getMessage());
										ProcessingHelper::HandleError('failed_to_create_contact');
										return;
									}

								}
								else {
									static::Trace('... Default values are missing. Can not create contact.');
									ProcessingHelper::HandleError('failed_to_create_contact');
									return;
								}
								
								break;
								
							default:
								break;
								
						}
						break;
						
					default:
						static::Trace("Found ".$oSet_Person->Count()." callers with the same email address '{$sCallerEmail}', the first one will be used...");
						// Multiple callers with the same email address!
						$oCaller = $oSet_Person->Fetch();
						
				}
				
				// Set caller for e-mail.
				$oEmail->SetSender($oCaller);
				
			}
			else {
				static::Trace("Caller already determined by previous policy. Skip.");
			}
		
	}
	
}


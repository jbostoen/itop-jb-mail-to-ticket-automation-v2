<?php

/**
 * @copyright   Copyright (c) 2019-2022 Jeffrey Bostoen
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     2.7.220523
 *
 * Policy interface definition and some classes implementing it.
 * 
 * Additional notes:
 * - do not alter ticket contents here, such as subject. That's done at a later phase. For this particular case: change EmailMessage's subject.
 */
 
// @todo Revise PolicyNoOtherRecipients: should this one be creating contacts as a fallback?

namespace jb_itop_extensions\mail_to_ticket;

use \Exception;
use \ReflectionClass;

// jb-framework
use \jb_itop_extensions\components\ormCustomCaseLog;

// iTop internals
use \Attachment;
use \AttributeHTML;
use \AttributeText;
use \CMDBChangeOpPlugin;
use \CMDBObject;
use \CMDBSource;
use \DBObjectSearch;
use \DBObjectSet;
use \Dict;
use \InlineImage;
use \IssueLog;
use \MetaModel;
use \ormDocument;
use \SetupUtils;
use \utils;
use \UserRights;

// iTop email processing
use \EmailMessage;
use \EmailProcessor;
use \EmailSource;
use \MailInboxBase;
use \MailInboxStandard;


// iTop classes
use \lnkContactToTicket;
use \Person;
use \Ticket;

const NEWLINE_REGEX = '/\r\n|\r|\n/';

/**
 * Interface iPolicy defines what the classes implementing policies should look like.
 */
interface iPolicy {
	
	/**
	 *
	 * @return boolean Whether this is compliant with a specified policy. Returning 'false' blocks further processing. In this case, it's expected a SetNextAction has been called.
	 */
	public static function IsCompliant();
	
	/**
	 * Runs some default functionality BEFORE checking the policy. Use case: logging some information.
	 * Can be cascaded to subclasses.
	 *
	 * @return void
	 */
	public static function BeforeComplianceCheck();
	
	/**
	 * Runs some default functionality AFTER checking the policy. Use case: logging some information.
	 * Can be cascaded to subclasses.
	 *
	 * @return void
	 */
	public static function AfterPassedComplianceCheck();
	
	/**
	 * Actions executed when the message does not comply with a policy.
	 * The default method informs the caller that the email was rejected.
	 *
	 * @return void
	 */
	public static function HandleViolation();
	
}

abstract class Policy implements iPolicy {
	
	/**
	 * @var \String[] $aPreviouslyExecutedPolicies Array of policy (class) names which have been executed already.
	 */
	public static $aPreviouslyExecutedPolicies = [];
	
	/**
	 * @var \Integer $iPrecedence It's not necessary that this number is unique; but when all policies are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 */
	public static $iPrecedence = 20;
	
	/**
	 * @var \String $sPolicyId Shortname for policy
	 */
	public static $sPolicyId = 'policy_generic';
	
	/**
	 * @var \EmailMessage $oEmail E-mail message
	 */
	public static $oEmail = null;
	
	/**
	 * @var \MailInboxStandard $oMailBox Mailbox
	 */
	public static $oMailBox = null;
	
	/**
	 * @var \EmailSource $oSource E-mail source (e.g. IMAPEmailSource)
	 */
	public static $oSource = null;
	
	/**
	 * @var \Ticket $oTicket Ticket object (in iTop)
	 */
	public static $oTicket = null;
	
	/**
	 * @var \String $sEmailIndex Index in the e-mail source. Note: name is experimental; use methods instead to set/get
	 */
	public static $iEmailIndex = null;
	
	/**
	 * Initiator. Sets some widely used property values.
	 *
	 * @param \MailInboxStandard $oMailBox Mailbox
	 * @param \EmailSource $oSource E-mail source
	 * @param \Integer|\String $index Index of the e-mail in the source
	 * @param \EmailMessage $oEmail E-mail message
	 * @param \Ticket|null $oTicket Ticket found based on ticket reference (or null if not found)
	 * @param \String[] $aPreviouslyExecutedPolicies Array of policy (class) names which have been processed already.
	 *
	 */
	public static function Init(MailInboxStandard $oMailBox, EmailSource $oSource, $index, EmailMessage $oEmail, ?Ticket $oTicket, $aPreviouslyExecutedPolicies) {
		
		static::SetMailBox($oMailBox);
		static::SetMailSource($oSource);
		static::SetMailIndex($index);
		static::SetMail($oEmail);
		static::SetTicket($oTicket);
		static::SetExecutedPolicies($aPreviouslyExecutedPolicies);
	
	}
	
	/**
	  * Gets the policy ID
	  *
	  * @return \String
	 */
	public static function GetPolicyId() {
		
		return static::$sPolicyId;
		
	}
	
	/**
	  * Gets the policy precedence
	  *
	  * @return \Integer
	 */
	public static function GetPolicyPrecedence() {
		
		return static::$iPrecedence;
		
	}
	
	/**
	 * Gets the mailbox
	 *
	 * @return \MailInboxStandard
	 */
	public static function GetMailBox() {
		
		return static::$oMailBox;
		
	}
	
	/**
	 * Sets the mailbox
	 *
	 * @param \MailInboxStandard $oMailBox Mailbox
	 *
	 * @return void
	 */
	public static function SetMailBox(MailInboxStandard $oMailBox) {
		
		static::$oMailBox = $oMailBox;
		
	}
	
	
	/**
	 * Gets the e-mail
	 *
	 * @return \EmailMessage
	 */
	public static function GetMail() {
		
		return static::$oEmail;
		
	}
	
	/**
	 * Sets the e-mail
	 *
	 * @param \EmailMessage $oMessage E-mail message
	 *
	 * @return void
	 */
	public static function SetMail(EmailMessage $oEmail) {
		
		static::$oEmail = $oEmail;
		
	}
	
	/**
	 * Gets the e-mail source
	 *
	 * @return \MailInboxStandard
	 */
	public static function GetMailSource() {
		
		return static::$oSource;
		
	}
	
	/**
	 * Sets the e-mail source
	 *
	 * @param \EmailSource $oSource E-mail source
	 *
	 * @return void
	 */
	public static function SetMailSource(EmailSource $oSource) {
		
		static::$oSource = $oSource;
		
	}
	
	/**
	 * Gets index of e-mail message
	 *
	 * @return \Integer
	 */
	public static function GetMailIndex() {
		
		return static::$iEmailIndex;
		
	}
	
	/**
	 * Sets index of e-mail message
	 *
	 * @param \Integer $index For now, expect an integer for the e-mail index.
	 *
	 * @return void
	 */
	public static function SetMailIndex($index) {
		
		static::$iEmailIndex = $index;
		
	}
	
	/**
	 * Gets ticket
	 *
	 * @return \Ticket
	 */
	public static function GetTicket() {
		
		return static::$oTicket;
		
	}
	
	/**
	 * Sets ticket
	 *
	 * @param \Ticket $oTicket Ticket
	 *
	 * @return void
	 */
	public static function SetTicket($oTicket) {
		
		static::$oTicket = $oTicket;
		
	}
	
	/**
	 * Gets executed policies
	 *
	 * @return \String[]
	 */
	public static function GetExecutedPolicies() {
		
		return static::$aPreviouslyExecutedPolicies;
		
	}
	
	/**
	 * Sets executed policies
	 *
	 * @param \String[] $aPreviouslyExecutedPolicies Names of previously executed policies
	 *
	 * @return void
	 */
	public static function SetExecutedPolicies($aPreviouslyExecutedPolicies) {
		
		static::$aPreviouslyExecutedPolicies = $aPreviouslyExecutedPolicies;
		
	}
	
	/**
	 * Checks if mailbox, email, ticket information is compliant with a certain policy.
	 *
	 * @return boolean Whether this is compliant with a specified policy. Returning 'false' blocks further processing. In this case, it's expected a SetNextAction has been called.
	 */
	public static function IsCompliant() {
		
		return true;
		
	}
	
	/**
	 * Runs some default functionality BEFORE checking the policy. Use case: logging some information.
	 * Can be cascaded to subclasses.
	 *
	 * @return void
	 */
	public static function BeforeComplianceCheck() {
	
		$sCalledClass = get_called_class();
		$sUnqualifiedName = (new ReflectionClass($sCalledClass))->getShortName();
		if($sUnqualifiedName != 'Policy') {
			
			$sLog = '. Check #'.(count(static::GetExecutedPolicies())+1).' (precedence: '.$sCalledClass::$iPrecedence.'): '.$sUnqualifiedName;
			
			// Some classes fake their $sPolicyId to recycle settings
			$sAttCode = $sCalledClass::$sPolicyId.'_behavior';
			if(MetaModel::IsValidAttCode(get_class($sCalledClass::$oMailBox), $sAttCode) == true) {
				$sLog .= ' - Behavior: '.$sCalledClass::$oMailBox->Get($sAttCode);
			}
			
			static::Trace($sLog);
			
		}
	}
	
	/**
	 * Runs some default functionality AFTER checking the policy IF IsCompliant() returned 'true'. Use case: logging some information.
	 * Can be cascaded to subclasses.
	 *
	 * @return void
	 */
	public static function AfterPassedComplianceCheck() {
	
		$sUnqualifiedName = (new ReflectionClass(get_called_class()))->getShortName();
		if($sUnqualifiedName  != 'Policy') {
			static::Trace('.. Complete: '.$sUnqualifiedName);
		}
	}
	
	/**
	 * Actions executed when the message does not comply with a policy.
	 * The default method informs the caller that the email was rejected.
	 *
	 * @return void
	 */
	public static function HandleViolation() {
		
		$oEmail = static::GetMail();
		$oMailBox = static::GetMailBox();
		
		$oRawEmail = $oEmail->oRawEmail;
	
		// Inform the caller who doesn't follow guidelines.		
		// User education and communicating the guideliens is great; but sometimes policies need to be enforced.
		$sTo = $oEmail->sCallerEmail;
		$sFrom = $oMailBox->Get('notify_from'); 
	
		// Policy violations have a typical way of handling.
		// The behavior is - besides some fallbacks - usually one of the following:
		// - bounce_delete -> bounce and delete the message
		// - bounce_mark_as_undesired -> bounce and marks the message as undesired
		// - delete -> delete the message
		// - do_nothing -> great, lazy. For testing purposes. To actually see if policies are processed or log additional info without doing anything in the end.
		// - mark_as_undesired -> stays in the mailbox for a few days
		// - some sort of fallback -> doesn't matter here
		
		$sPolicyId = get_called_class()::$sPolicyId;
		
		$sBehavior = $oMailBox->Get($sPolicyId.'_behavior');
		static::Trace('. Policy violated. Behavior: '.$sBehavior);
		
		// First check if email notification must be sent to caller (bounce message)
		switch($sBehavior) {
		
			// Generic cases
			case 'bounce_delete':
			case 'bounce_mark_as_undesired':
			
				static::Trace('Bounce message: '.$sPolicyId);
				
				$sSubject = $oMailBox->Get($sPolicyId.'_subject');
				$sBody = $oMailBox->Get($sPolicyId.'_notification'); 
				
				// Return to sender
				if($sTo == '') { 
					static::Trace('.. No "to" defined, skipping bounce message.');
				}
				elseif($sFrom == '') { 
					static::Trace('.. No "from" defined, skipping bounce message.');
				}
				elseif($oRawEmail) {
					
					// Allow some customization in the bounce message
					$sSubject = static::ReplaceMailPlaceholders($sSubject);
					$sBody = static::ReplaceMailPlaceholders($sBody);
					
					if($sSubject == '') {
						$sSubject = 'Message bounced - not compliant with an enforced policy.';
					}

					static::Trace('Sending bounce message "'.$sSubject.'" to "'.$sTo.'"');
					$oRawEmail->SendAsAttachment($sTo, $sFrom, $sSubject, $sBody);
				}
				
				break;
				
		}
		
				
		switch($sBehavior) {
				
			case 'bounce_delete':
			case 'delete': 
				static::Trace('Set next action for EmailProcessor to DELETE_MESSAGE');
				$oMailBox->SetNextAction(EmailProcessor::DELETE_MESSAGE); // Remove the message from the mailbox
				break;
				
			case 'move':
				// Remove the processed message from the mailbox
				static::Trace('Set next action for EmailProcessor to MOVE_MESSAGE');
				$oMailBox->SetNextAction(EmailProcessor::MOVE_MESSAGE);
				break;
				
			case 'mark_as_error': 
				// Mark as error should be irrelevant now. Keeping it just in case.
				static::Trace('Set next action for EmailProcessor to MARK_MESSAGE_AS_ERROR');
				$oMailBox->SetNextAction(EmailProcessor::MARK_MESSAGE_AS_ERROR); // Keep the message in the mailbox, but marked as error
				break;
				 
			case 'bounce_mark_as_undesired':
			case 'mark_as_undesired':
				static::Trace('Set next action for EmailProcessor to MARK_MESSAGE_AS_UNDESIRED');
				$oMailBox->SetNextAction(EmailProcessor::MARK_MESSAGE_AS_UNDESIRED); // Keep the message temporarily in the mailbox, but marked as undesired
				break;
				
			// Any other action
			case 'do_nothing':
			default:
				static::Trace('Set next action for EmailProcessor to NO_ACTION');
				$oMailBox->SetNextAction(EmailProcessor::NO_ACTION);
				break;
				
		}
		
	}
	
	/**
	 * Replace email placeholders in a string.
	 * 
	 * @param \String $sString Input string
	 *
	 * @details Also exposes some properties which are not likely to be useful (body_format) at any time, but who knows?
	 *
	 * @return String String where the placeholders are filled in
	 */
	public static function ReplaceMailPlaceholders($sString) {
		
		$oEmail = static::GetMail();
		
		$aParams = [
			'mail->uidl' => $oEmail->sUIDL,
			'mail->message_id' => $oEmail->sMessageId,
			'mail->subject' => $oEmail->sSubject,
			'mail->caller_email' => $oEmail->sCallerEmail,
			'mail->caller_name' => $oEmail->sCallerName,
			'mail->recipient' => $oEmail->sRecipient,
			'mail->date' => $oEmail->sDate,
			'mail->body_text_plain' => strip_tags($oEmail->sBodyText),
			'mail->body_text'  => $oEmail->sBodyText,
			'mail->body_format' => $oEmail->sBodyFormat
		];
		
		// Extend
		$aParamsExtended = [];
		foreach($aParams as $sParam => $sValue) {
			$aParamsExtended[$sParam] = $sValue;
			$aParamsExtended[htmlentities($sParam)] = $sValue;
		}
		
		return MetaModel::ApplyParams($sString, $aParamsExtended);
		
	}
	
	
	/**
	 * For logging information about the processing of emails.
	 *
	 * @param \String $sString Input string
	 *
	 * @return void
	 */
	public static function Trace($sString) {
		static::$oMailBox->Trace($sString);
	}
	
	/**
	 * For logging information about the processing of emails.
	 *
	 * @param \Array $aRecipients Hash table of recipients ('name', 'email')
	 *
	 * @return \String[]
	 *
	 * @todo move this to helper
	 */
	public static function GetAddressesFromRecipients($aRecipients) {
		$aAddresses = [];
		foreach($aRecipients as $aRecipient) {
			$aAddresses[] = $aRecipient['email'];
		}
		return $aAddresses;
	}
	
}


/**
 * Class PolicyCreateOrUpdateTicket Special policy; at this point the Ticket is created or updated.
 * @details Replaces Combodo's MailInboxStandard way of handling incoming emails (creating or updating ticket).
 * This is a fork, a lot of code has been polished but is also still the same or heavily inspired by the original Mail to Ticket Automation.
 * Hence, this (sub)class contains methods with the same name to make it easy to keep retrofitting bug fixes etc.
 */
abstract class PolicyCreateOrUpdateTicket extends Policy implements iPolicy {
	
	/**
	 * @var \Integer $iPrecedence It's not necessary that this number is unique; but when all policies are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 * 
	 * @details This is a special policy which takes care of only basic Ticket creation or update. 
	 * Any real checks that block Ticket creation or update, should have been run by now. 
	 * Any policies following this one, should not be blocking!
	 */
	public static $iPrecedence = 200;
	
	/**
	 * @var \String $sPolicyId Shortname for policy
	 */
	public static $sPolicyId = 'policy_create_or_update_ticket';
	
	/*
	 * @var \Array $aAddedAttachments Array containing info on any attachments in the email
	 */
	public static $aAddedAttachments = [];
	
	/**
	 * Initiator. Sets some widely used property values.
	 *
	 * @param \MailInboxStandard $oMailBox Mailbox
	 * @param \EmailSource $oSource E-mail source
	 * @param \Integer|\String $index Index of the e-mail in the source
	 * @param \EmailMessage $oEmail E-mail message
	 * @param \Ticket|null $oTicket Ticket found based on ticket reference (or null if not found)
	 * @param \String[] $aPreviouslyExecutedPolicies Array of policy (class) names which have been processed already.
	 *
	 */
	public static function Init(MailInboxStandard $oMailBox, EmailSource $oSource, $index, EmailMessage $oEmail, ?Ticket $oTicket, $aPreviouslyExecutedPolicies) {
	
		parent::Init($oMailBox, $oSource, $index, $oEmail, $oTicket, $aPreviouslyExecutedPolicies);
	
		// Reset for each email that is processed
		// @todo Review if this should be in the initialization or moved to another method.
		static::$aAddedAttachments = [];
		
	}
	
	/**
	 * @inheritDoc
	 */
	public static function IsCompliant() {
		
		// Generic 'before' actions
		parent::BeforeComplianceCheck();
		
		$oMailBox = static::GetMailBox();
		$oEmail = static::GetMail();
		$oTicket = static::GetTicket();
		
		$sBehavior = $oMailBox->Get('behavior');
		
		try {
				
			switch($sBehavior)
			{
				case 'create_only':
					static::CreateTicketFromEmail();
					break;
				
				case 'update_only':
					if(is_object($oTicket) == false) {
						// No ticket associated with the incoming email, nothing to update, reject the email
						$oMailBox->HandleError($oEmail, 'nothing_to_update', $oEmail->oRawEmail);
					}
					else {
						// Update the ticket with the incoming email
						static::UpdateTicketFromEmail();
					}
					break;
				
				default: // both: update or create as needed
					if(is_object($oTicket) == false) {
						// Create a new ticket
						static::CreateTicketFromEmail();
					}
					else {
						// Update the ticket with the incoming email
						static::UpdateTicketFromEmail();
					}
					break;			
			}
			
		}
		catch(Exception $e) {
		
			switch($e->GetMessage()) {
				// Match descriptions in CreateTicketFromEmail(), UpdateTicketFromEmail()
				case 'Ticket creation failed':
				case 'Ticket update failed':
					
					// Stop further processing (will delete or mark as error, based on user's settings)
					return false;
					
			}
			
		}
			
		// Generic 'after' actions
		parent::AfterPassedComplianceCheck();
		
		return true;
		
	}
	
	/**
	 * Function inspired by Combodo's MailInboxStandard::CreateTicketFromEmail()
	 * Creates a new Ticket from an email
	 *
	 * @return void
	 *
	 * @throws \Exception
	 */
	public static function CreateTicketFromEmail() {
		
		$oEmail = static::GetMail();
		$oMailBox = static::GetMailBox();
		
		// In case of error (exception...) set the behavior
		// Upon success, this will be overruled again.
		if($oMailBox->Get('error_behavior') == 'delete') {
			 // Remove the message from the mailbox
			$oMailBox->SetNextAction(EmailProcessor::DELETE_MESSAGE);
		}
		elseif($oMailBox->Get('error_behavior') == 'mark_as_error') {
			 // Keep the message in the mailbox, but marked as error
			$oMailBox->SetNextAction(EmailProcessor::MARK_MESSAGE_AS_ERROR);
		}
		
		static::Trace(".. Creating a new Ticket from email '{$oEmail->sSubject}'");
		$sTargetClass = $oMailBox->Get('target_class');
			
		if(MetaModel::IsValidClass($sTargetClass) == false) {
			$sErrorMessage = "... Invalid 'ticket_class' configured: {$sTargetClass} is not a valid class. Cannot create such an object.";
			static::Trace($sErrorMessage);
			throw new Exception($sErrorMessage);
		}
		
		if($oEmail->oInternal_Contact === null || get_class($oEmail->oInternal_Contact) != 'Person') {
			$sErrorMessage = "... Invalid caller specified: Cannot create Ticket without valid Person.";
			static::Trace($sErrorMessage);
			throw new Exception($sErrorMessage);			
		}
		
		$oTicket = MetaModel::NewObject($sTargetClass);
		static::SetTicket($oTicket);
		
		$oTicket->Set('org_id', $oEmail->oInternal_Contact->Get('org_id'));
		if(MetaModel::IsValidAttCode($sTargetClass, 'caller_id')) {
			$oTicket->Set('caller_id', $oEmail->oInternal_Contact->GetKey());
		}
		if(MetaModel::IsValidAttCode($sTargetClass, 'origin')) {
			$oTicket->Set('origin', 'mail');
		}
		
		// Max length for title
		$oTicketTitleAttDef = MetaModel::GetAttributeDef($sTargetClass, 'title');
		$iTitleMaxSize = $oTicketTitleAttDef->GetMaxSize();
		$sSubject = $oEmail->sSubject;
		$oTicket->Set('title', substr($sSubject, 0, $iTitleMaxSize));
		
		// Insert the remaining attachments so that their ID is known and the attachments can be referenced in the message's body
		// Cannot insert them for real since the Ticket is not saved yet (so Ticket id is unknown)
		// UpdateAttachments() will be called once the ticket is properly saved
		static::AddAttachments(true);
		
		// Seems to be for backward compatibility / plain text.
		$oTicketDescriptionAttDef = MetaModel::GetAttributeDef($sTargetClass, 'description');
		$bForPlainText = true; // Target format is plain text (by default)
		if ($oTicketDescriptionAttDef instanceof AttributeHTML) {
			// Target format is HTML
			$bForPlainText = false;
		}
		elseif($oTicketDescriptionAttDef instanceof AttributeText) {
			$aParams = $oTicketDescriptionAttDef->GetParams();
			if(array_key_exists('format', $aParams) && ($aParams['format'] == 'html')) {
				// Target format is HTML
				$bForPlainText = false;
			}
		}
		
		static::Trace("... Email body format: ".$oEmail->sBodyFormat);
		static::Trace("... Target format for 'description': ".($bForPlainText ? 'text/plain' : 'text/html'));
		
		$sTicketDescription = static::BuildDescription($bForPlainText);

		$iDescriptionMaxSize = $oTicketDescriptionAttDef->GetMaxSize() - 1000; // Keep some room just in case...
		
		if(strlen($sTicketDescription) > $iDescriptionMaxSize) {
			
			$sMsg = "CreateTicketFromEmail: Truncated description for [{$oTicket->Get('title')}] actual length: ".strlen($sTicketDescription)." maximum: $iDescriptionMaxSize";
		    IssueLog::Error($sMsg);
			static::Trace($sMsg);
			
			
		 
			// Add the original e-mail message as an attachment. 
			// Newer comment by Combodo notes: this has no effect, attachments have already been processed
			$oEmail->aAttachments[] = [
				'content' => $sTicketDescription, 
				'filename' => ($bForPlainText == true ? 'original message.txt' : 'original message.html'), 
				'mimeType' => ($bForPlainText == true ? 'text/plain' : 'text/html')
			];
		}
		
		// Keep some room just in case... (in case of what?)
		$oTicket->Set('description', static::FitTextIn($sTicketDescription, $iDescriptionMaxSize));
		
		// Default values
		$sDefaultValues = $oMailBox->Get('ticket_default_values');
		$aDefaults = preg_split(NEWLINE_REGEX, $sDefaultValues);
		$aDefaultValues = [];
		foreach($aDefaults as $sLine) {
			if (preg_match('/^([^:]+):(.*)$/', $sLine, $aMatches)) {
				$sAttCode = trim($aMatches[1]);
				$sValue = trim($aMatches[2]);
				$sValue = static::ReplaceMailPlaceholders($sValue);
				$aDefaultValues[$sAttCode] = $sValue;
			}
		}
		$oMailBox->InitObjectFromDefaultValues($oTicket, $aDefaultValues);
		
		static::AddAdditionalContacts();
		
		static::BeforeInsertTicket();
		
		try {
			$oTicket->DBInsert();
			static::Trace(".. Ticket ".$oTicket->GetName()." created.");
		}
		catch(Exception $e) {
			static::Trace("... Ticket {$oTicket->GetName()} might not be properly created or something else went wrong (for instance: notifications).");
			static::Trace($e->GetMessage()); // Add actual error message (if available)
			throw new Exception('Ticket creation failed'); // Match in IsCompliant()
		}
			
		static::AfterInsertTicket();
		
	}
	
	/**
	 * Function inspired by Combodo's MailInboxStandard::UpdateTicketFromEmail()
	 * Updates an existing Ticket from an email
	 *
	 * @return void
	 *
	 * @throws \Exception
	 */
	public static function UpdateTicketFromEmail() {
		
		$oMailBox = static::GetMailBox();
		$oEmail = static::GetMail();
		$oTicket = static::GetTicket();
		$oCaller = $oEmail->oInternal_Contact;
		
		// In case of error (exception...) set the behavior
		// Upon success, this will be overruled again.
		if($oMailBox->Get('error_behavior') == 'delete') {
			$oMailBox->SetNextAction(EmailProcessor::DELETE_MESSAGE); // Remove the message from the mailbox
		}
		elseif($oMailBox->Get('error_behavior') == 'mark_as_error') {
			$oMailBox->SetNextAction(EmailProcessor::MARK_MESSAGE_AS_ERROR); // Keep the message in the mailbox, but marked as error
		}		
		
		// Check that the ticket is of the expected class
		$sTargetClass = $oMailBox->Get('target_class');
		if(is_a($oTicket, $sTargetClass) == false) {
			$sClass = get_class($oTicket);
			static::Trace("... Error: the incoming email refers to the ticket '{$oTicket->GetName()}' of class '{$sClass}', but this mailbox is configured to process only tickets of class '{$sTargetClass}'");
			$oMailBox->SetNextAction(EmailProcessor::MARK_MESSAGE_AS_ERROR); // Keep the message in the mailbox, but marked as error
			return;
		}
		
		// Try to extract what's new from the message's body
		static::Trace("... Updating Ticket '{$oTicket->GetName()}' from email '{$oEmail->sSubject}'");
		
		$iCurrentUserId = UserRights::GetUserId();
		$bInsertChangeUserId = false;
		// Set author of change operation to mail author (from:) and create a new Change
		// Method only exist in iTop 3.0
		if(method_exists('UserRights', 'GetUserFromPerson') && $oCaller !== null){
			$oUser = UserRights::GetUserFromPerson($oCaller, true);
			if($oUser !== null){
				CMDBObject::SetTrackUserId($oUser->GetKey());
				$oCMDBChange = CMDBObject::GetCurrentChange();
				CMDBObject::SetCurrentChange(null);
				$bInsertChangeUserId = true;
			}
		}

		// Process attachments
		static::AddAttachments(true);
		
		$sCaseLogEntry = static::BuildCaseLogEntry();
		
		if($oEmail->sTrace == '') {
			static::Trace("... No email trace available.");
		}
		else {
			static::Trace("... Email trace: ".$oEmail->sTrace);
		}
		
		// Write the log on behalf of the caller.
		// Fallback to e-mail address if name is unknown.
		$sCallerName = $oEmail->sCallerName;
		$iCallerUserId = null;
		if($oEmail->oInternal_Contact === null) {
			$sCallerName = $oEmail->sCallerEmail;
			// $iCallerUserId remains null
		}
		else {
			$sCallerName = $oEmail->oInternal_Contact->GetName(); // Derive name from Person
			$sOQL = 'SELECT User AS u JOIN Person AS p ON u.contactid = p.id WHERE p.id = :id';
			$oUsers = new DBObjectSet(DBObjectSearch::FromOQL_AllData($sOQL), [], ['id' => $oEmail->oInternal_Contact->GetKey()]);
			
			// Only first user will be matched. Multiple accounts could theoretically be linked to 1 Person.
			$oUser = $oUsers->Fetch();
			
			if($oUser !== null) {
				$iCallerUserId = $oUser->GetKey();
			}
			// else: $iCallerUserId remains null
		}
					
		// Determine which field to update
		$sAttCode = 'public_log';
		$aAttCodes = MetaModel::GetModuleSetting('jb-itop-standard-email-synchro', 'ticket_log', [
			'UserRequest' => 'public_log', 
			'Incident' => 'public_log'
		]);
		
		if(array_key_exists(get_class($oTicket), $aAttCodes) == true) {
			$sAttCode = $aAttCodes[get_class($oTicket)];
		}
		
		$oCaseLog = $oTicket->Get($sAttCode);
		$oAttributeValue = new ormCustomCaseLog();
		$oAttributeValue->AddLogEntriesFromCaseLog($oCaseLog);
		
		// New entry from current email
		if($bInsertChangeUserId === true){
			$oAttributeValue->AddLogEntry($sCaseLogEntry, $sCallerName, $oUser->GetKey());
		}
		else{
			$oAttributeValue->AddLogEntry($sCaseLogEntry, $sCallerName, $iCallerUserId, '');
		}
		
		// Sort chronologically: ascending (true), descending (false = most recent on top)!
		// This ensures that log entries are ordered chronologically, even if the e-mails were processed in the wrong order.
		// However, it can not prevent a ticket (with description) being initially created from the chronologically speaking 'second' e-mail.
		// That issue should be fixed now in newer versions of this extension which order the incoming e-mails first.
		// This line still matters though, because agents might also have added logs in between.
		$oAttributeValue = $oAttributeValue->ToSortedCaseLog(false);
		
		$oTicket->Set($sAttCode, $oAttributeValue);
		
		// Policy has already removed unwanted contacts
		static::AddAdditionalContacts();
		
		static::BeforeUpdateTicket();
		
		try {
			$oTicket->DBUpdate();			
			static::Trace("... Ticket '{$oTicket->GetName()}' has been updated.");
		}
		catch(Exception $e) {
			static::Trace("... Ticket {$oTicket->GetName()} might not be properly updated or something else went wrong (for instance: notifications).");
			static::Trace($e->GetMessage()); // Add actual error message (if available)
			throw new Exception('Ticket update failed'); // Match in IsCompliant()
		}
		
		static::AfterUpdateTicket();

		// Restore previous change as current change
		if($bInsertChangeUserId === true){
		  CMDBObject::SetTrackUserId($iCurrentUserId);
		  CMDBObject::SetCurrentChange($oCMDBChange);
		}
		
	}
	
	/**
	 * Function inspired by Combodo's MailInboxStandard::BeforeInsertTicket().
	 * Called right before a Ticket is created.
	 *
	 * @return void
	 */
	public static function BeforeInsertTicket() {
		 
		// Do nothing
	
	}
	
	/**
	 * Function inspired by Combodo's MailInboxStandard::AfterInsertTicket().
	 * Called right after a Ticket is created.
	 *
	 * @return void
	 */
	public static function AfterInsertTicket() {
		 
		// Process attachments now the ID is known
		static::UpdateAttachments();
		
		$oMailBox = static::GetMailBox();
		
		// Shall we delete the source email immediately?
		if($oMailBox->Get('email_storage') == 'delete') {
			// Remove the processed message from the mailbox
			static::Trace(".. Deleting the source email");
			$oMailBox->SetNextAction(EmailProcessor::DELETE_MESSAGE);		
		}
		elseif($oMailBox->Get('email_storage') == 'move' && $oMailBox->Get('target_folder') != '') {
			// Move the processed message to another folder
			static::Trace(".. Moving the source email");
			$oMailBox->SetNextAction(EmailProcessor::MOVE_MESSAGE);	
		}
		else {
			// Keep the message in the mailbox
			static::Trace(".. Keeping the source email");
			$oMailBox->SetNextAction(EmailProcessor::NO_ACTION);		
		}	
	
	}
	 
	/**
	 * Function inspired by Combodo's MailInboxStandard::BuildDescription().
	 * Returns a description for a new Ticket.
	 *
	 * @param \Boolean $bForPlainText True if the desired output format is plain text, false if HTML
	 * @return \String Ticket description
	 */
	public static function BuildDescription($bForPlainText) {
		
		$sTicketDescription = '';
		$oEmail = static::GetMail();
		
		if($oEmail->sBodyFormat == 'text/html') {
			// Original message is in HTML
			static::Trace("... Managing inline images...");
			$sTicketDescription = static::ManageInlineImages($oEmail->sBodyText, $bForPlainText);
			if($bForPlainText == true) {
				static::Trace("... Converting HTML to text using utils::HtmlToText...");
				$sTicketDescription = utils::HtmlToText($oEmail->sBodyText);
			}
		}
		else {
			// Original message is in plain text
			$sTicketDescription = utils::TextToHtml($oEmail->sBodyText);
			if($bForPlainText == false) {
				static::Trace("... Converting text to HTML using utils::TextToHtml...");
				$sTicketDescription = utils::TextToHtml($oEmail->sBodyText);
			}
		}

		if(empty($sTicketDescription) == true) {
			// Will use language of the user under which the cron job is being executed.
			$sTicketDescription = Dict::S('MailPolicy:CreateOrUpdateTicket:NoDescriptionProvided');
		}
		
		return $sTicketDescription;
		
	}
	
	/**
	 * Function inspired by Combodo's MailInboxStandard::ManageInlineImages().
	 *
	 * @param \String $sBodyText Body text
	 * @param \Boolean $bForPlainText Plain text (true) or HTML (false)
	 * 
	 * @return \String Body text
	 */
	public static function ManageInlineImages($sBodyText, $bForPlainText) {
		 
		// Search for inline images: i.e. <img tags containing an src="cid:...." or without double quotes e.g. src=cid:xyzxyzx
		// Note: (?: ... ) is used for grouping the alternative without creating a "matching group"
		if(preg_match_all('/<img[^>]+src=(?:"cid:([^"]+)"|cid:([^ >]+))[^>]*>/i', $sBodyText, $aMatches, PREG_OFFSET_CAPTURE)) {
			$aInlineImages = [];
			foreach ($aMatches[0] as $idx => $aInfo) {
				$aInlineImages[$idx] = array(
					'position' => $aInfo[1]
				);
			}
			foreach ($aMatches[1] as $idx => $aInfo) {
				$sCID = $aInfo[0];
				if(array_key_exists($sCID, static::$aAddedAttachments) == false) {
					static::Trace(".... Info: inline image: {$sCID} not found as an attachment. Ignored.");
				}
				elseif(array_key_exists($sCID, static::$aAddedAttachments)) {
					$aInlineImages[$idx]['cid'] = $sCID;
					static::Trace(".... Inline image cid:$sCID stored as ".get_class(static::$aAddedAttachments[$sCID])."::".static::$aAddedAttachments[$sCID]->GetKey());
				}
			}
			if(defined('ATTACHMENT_DOWNLOAD_URL') == false) {
				define('ATTACHMENT_DOWNLOAD_URL', 'pages/ajax.render.php?operation=download_document&class=Attachment&field=contents&id=');
			}
			if($bForPlainText == true) {
				// The target form is text/plain, so the HTML tags will be stripped
				// Insert the URLs to the attachments, just before the <img tag so that the hyperlink remains (as plain text) at the right position
				// when the HTML tags will be stripped
				// Start from the end of the text to preserve the positions of the <img tags AFTER the insertion
				$sWholeText = $sBodyText;
				$idx = count($aInlineImages);
				while ($idx > 0) {
					$idx --;
					if (array_key_exists('cid', $aInlineImages[$idx]))
					{
						$sBefore = substr($sWholeText, 0, $aInlineImages[$idx]['position']);
						$sAfter = substr($sWholeText, $aInlineImages[$idx]['position']);
						$oAttachment = static::$aAddedAttachments[$aInlineImages[$idx]['cid']];
						$sUrl = utils::GetAbsoluteUrlAppRoot().ATTACHMENT_DOWNLOAD_URL.$oAttachment->GetKey();
						$sWholeText = $sBefore.' '.$sUrl.' '. $sAfter;
					}
				}
			}
			else {
				// The target format is text/html, keep the formatting, but just change the URLs
				$aSearches = [];
				$aReplacements = [];
				foreach(static::$aAddedAttachments as $sCID => $oAttachment)
				{
					$aSearches[] = 'src="cid:'.$sCID.'"';
					if(class_exists('InlineImage') == true && $oAttachment instanceof InlineImage) {
						// Inline images have a special download URL requiring the 'secret' token
						$aReplacements[] = 'src="'.utils::GetAbsoluteUrlAppRoot().INLINEIMAGE_DOWNLOAD_URL.$oAttachment->GetKey().'&s='.$oAttachment->Get('secret').'"';
					}
					else {
						$aReplacements[] = 'src="'.utils::GetAbsoluteUrlAppRoot().ATTACHMENT_DOWNLOAD_URL.$oAttachment->GetKey().'"';
					}
					
					$aSearches[] = 'src=cid:'.$sCID; // Same without quotes
					if (class_exists('InlineImage') == true && ($oAttachment instanceof InlineImage)) {
						// Inline images have a special download URL requiring the 'secret' token
						$aReplacements[] = 'src="'.utils::GetAbsoluteUrlAppRoot().INLINEIMAGE_DOWNLOAD_URL.$oAttachment->GetKey().'&s='.$oAttachment->Get('secret').'" '; // Beware: add a space at the end
					}
					else {
						$aReplacements[] = 'src="'.utils::GetAbsoluteUrlAppRoot().ATTACHMENT_DOWNLOAD_URL.$oAttachment->GetKey().'" '; // Beware: add a space at the end
					}
				}
				$sWholeText = str_replace($aSearches, $aReplacements, $sBodyText);
			}
			$sBodyText = $sWholeText;
		}
		else {
			static::Trace("... Inline Images: no inline-image found in the message");
		}
		return $sBodyText;
		
	}
	 
	/**
	 * Function inspired by Combodo's MailInboxStandard::AddAdditionalContacts()
	 * Adds additional contacts in the email as related contacts in the Ticket.
	 *
	 * @return void
	 */
	public static function AddAdditionalContacts() {
		
		$oTicket = static::GetTicket();
		$oEmail = static::GetMail();
		
		$sTargetClass = get_class($oTicket);
		if(MetaModel::IsValidAttCode($sTargetClass, 'contacts_list') == false) {
			return;
		}
		
		$oContactsSet = $oTicket->Get('contacts_list');
		$aExistingContacts = [];
		
		while($oLnk = $oContactsSet->Fetch()) {
			$aExistingContacts[$oLnk->Get('contact_id')] = true;
		}

		// (Different) policies may have accidentally added contacts multiple times, even if they are supposed to check this.
		$oEmail->aInternal_Additional_Contacts = array_unique($oEmail->aInternal_Additional_Contacts, SORT_REGULAR);

		foreach($oEmail->aInternal_Additional_Contacts as $oContact) {
			
			if(MetaModel::IsValidAttCode($sTargetClass, 'caller_id') == true && $oContact->GetKey() != $oTicket->Get('caller_id')) {
				$oLnk = new lnkContactToTicket();
				$oLnk->Set('contact_id', $oContact->GetKey());
				$oContactsSet->AddObject($oLnk);
			}
			else {
				$sContactName = $oContact->GetName();
				static::Trace(".... Skipping '{$sContactName}' as additional contact since it is the caller.");
			}
			
		}
		$oTicket->Set('contacts_list', $oContactsSet);
		
	}
	
	/**
	 * Function inspired by Combodo's MailInboxStandard::BeforeUpdateTicket()
	 *
	 * @return void
	 */
	public static function BeforeUpdateTicket() {
		
		// Do nothing
		
	}
	
	/**
	 * Function inspired by Combodo's MailInboxStandard::AfterUpdateTicket()
	 * Hook to run function after a Tickt has been updated.
	 * In this case, it activates the trigger "TriggerOnMailUpdate"
	 *
	 * @return void
	 */
	public static function AfterUpdateTicket() {
		
		$oMailBox = static::GetMailBox();
		$oEmail = static::GetMail();
		$oTicket = static::GetTicket();
		
		// If there are any TriggerOnMailUpdate defined, let's activate them
		$aClasses = MetaModel::EnumParentClasses(get_class($oTicket), ENUM_PARENT_CLASSES_ALL);
		$sClassList = implode(', ', CMDBSource::Quote($aClasses));
		$oSet_TriggerMailUpdate = new DBObjectSet(DBObjectSearch::FromOQL_AllData("SELECT TriggerOnMailUpdate AS t WHERE t.target_class IN ($sClassList)"));
		while($oTrigger = $oSet_TriggerMailUpdate->Fetch()) {
			$oTrigger->DoActivate($oTicket->ToArgs('this'));
		}

		// Apply a stimulus if needed, will write the ticket to the database, may launch triggers, etc...
		static::ApplyConfiguredStimulus($oTicket);
		
		// Delete the email immediately or keep it stored
		if($oMailBox->Get('email_storage') == 'delete') {
			// Remove the processed message from the mailbox
			static::Trace(".. Deleting the source email");
			$oMailBox->SetNextAction(EmailProcessor::DELETE_MESSAGE);		
		}
		elseif($oMailBox->Get('email_storage') == 'move') {
			// Move the message to another folder
			static::Trace(".. Moving the source email");
			$oMailBox->SetNextAction(EmailProcessor::MOVE_MESSAGE);
		}
		else {
			// Keep the message in the mailbox
			static::Trace(".. Keeping the source email");
			$oMailBox->SetNextAction(EmailProcessor::NO_ACTION);		
		}
		
	}
	
	/**
	 * Function inspired by Combodo's MailInboxStandard::ApplyConfiguredStimulus()
	 *
	 * Read the configuration in the 'stimuli' field (format: <state_code>:<stimulus_code>, one per line)
	 * and apply the corresponding stimulus according to the current state of the ticket
	 *
	 * @param ticket $oTicket
	 *
	 * @return void
	 */
	public static function ApplyConfiguredStimulus() {
		
		$oMailBox = static::GetMailBox();
		$oTicket = static::GetTicket();
		$sConf = $oMailBox->Get('stimuli');
		
		// In Combodo's version, this resulted in a warning?
		// Reopen ticket elsewhere if needed.
		if(trim($sConf) == '') {
			return;
		}
	
		$aConf = preg_split(NEWLINE_REGEX, $sConf);
		$aStateToStimulus = [];
		foreach($aConf as $sLine) {
			if (preg_match('/^([^:]+):(.*)$/', $sLine, $aMatches))
			{
				$sState = trim($aMatches[1]);
				$sStimulus = trim($aMatches[2]);
				$aStateToStimulus[$sState] = $sStimulus;
			}
			elseif(empty($sLine) == false) {
				static::Trace("... Invalid line in the 'stimuli' configuration: '{$sLine}'. The expected format for each line is <state_code>:<stimulus_code>");
			}
		}
		if (array_key_exists($oTicket->GetState(), $aStateToStimulus))
		{
			$sStimulusCode = $aStateToStimulus[$oTicket->GetState()];
			static::Trace("... About to apply the stimulus: ".$sStimulusCode." for the ticket in state: ".$oTicket->GetState());
			
			// Check that applying the stimulus will not break the data integrity constaints (mandatory, must change)
			$aTransitions = $oTicket->EnumTransitions();
			$bCanApplyStimulus = true;
			if (!isset($aTransitions[$sStimulusCode])) {
				$bCanApplyStimulus = false;
				static::Trace("... The Stimulus {$sStimulusCode} for ".get_class($oTicket)." in state {$oTicket->GetState()} has no effect (no transition). Ignored.");
			}
			else {
				
				$aTransitionDef = $aTransitions[$sStimulusCode];
				$sTargetState = $aTransitionDef['target_state'];
				$aTargetStates = MetaModel::EnumStates(get_class($oTicket));
				$aTargetStateDef = $aTargetStates[$sTargetState];
				$aExpectedAttributes = $aTargetStateDef['attribute_list'];
				foreach($aExpectedAttributes as $sAttCode => $iExpectCode) {
					$oAttDef = MetaModel::GetAttributeDef(get_class($oTicket), $sAttCode);
					if(($iExpectCode & OPT_ATT_MANDATORY) && ($oAttDef->IsNull($oTicket->Get($sAttCode)))) {
						// Check if there is just one possible value, in which case, use it				
						$aArgs = array('this' => $oTicket);
						// If the field is mandatory, set it to the only possible value
						if ($oAttDef->IsExternalKey())
						{
							$oAllowedValues = MetaModel::GetAllowedValuesAsObjectSet(get_class($oTicket), $sAttCode, $aArgs);
							if ($oAllowedValues->Count() == 1) 	{
								$oRemoteObj = $oAllowedValues->Fetch();
								$oTicket->Set($sAttCode, $oRemoteObj->GetKey());
								static::race("... Setting the mandatory External Key {$sAttCode} to the only allowed value: {$oRemoteObj->GetKey()}");
							}
							else {
								static::Trace("... Cannot apply the stimulus since the attribute {$sAttCode} is mandatory in the target state {$sTargetState} and is neither currently set nor has just one allowed value.");
								$bCanApplyStimulus = false;
							}
						}
						else {
							$aAllowedValues = MetaModel::GetAllowedValues_att(get_class($oTicket), $sAttCode, $aArgs);
							if (count($aAllowedValues) == 1) {
								$aValues = array_keys($aAllowedValues);
								$oTicket->Set($sAttCode, $aValues[0]);
								static::Trace("... Setting the mandatory attribute {$sAttCode} to the only allowed value: ".(string)$aValues[0]);
							}
							else {
								static::Trace("... Cannot apply the stimulus since the attribute {$sAttCode} is mandatory in the target state {$sTargetState} and is neither currently set nor has just one allowed value.");
								$bCanApplyStimulus = false;
							}
						}
					}
					if ($iExpectCode & OPT_ATT_MUSTCHANGE) {
						static::Trace("... Cannot apply the stimulus since the value of the attribute {$sAttCode} must be modified (manually) during the transition to the target state {$sTargetState}.");
						$bCanApplyStimulus = false;
					}
				}
			}
			
			if($bCanApplyStimulus == true) {
				try {
					static::Trace("... Actually applying the stimulus: {$sStimulusCode} for the ticket in state: {$oTicket->GetState()}");
					$oTicket->ApplyStimulus($sStimulusCode);
				}
				catch(Exception $e) {
					static::Trace("... ApplyStimulus failed: {$e->getMessage()}");
				}
			}
			else {
				static::Trace("... ApplyStimulus ignored.");
			}
		}
		
	}
	
	/**
	 * Function inspired by Combodo's MailInboxStandard::BuildCaseLogEntry()
	 * Build the text/html to be inserted in the case log when the ticket is updated
	 * Starting with iTop 2.3.0, the format is always HTML
	 *
	 * @return \String The HTML text to be inserted in the case log
	 */
	 public static function BuildCaseLogEntry() {
		 
		$oEmail = static::GetMail();
		$sCaseLogEntry = '';
		
		static::Trace("... Email body format: ".$oEmail->sBodyFormat);
		if ($oEmail->sBodyFormat == 'text/html') {
			static::Trace("... Extracting the new part using GetNewPartHTML()...");
			$sCaseLogEntry = $oEmail->GetNewPartHTML($oEmail->sBodyText);
			if (strip_tags($sCaseLogEntry) == '')
			{
				// No new part (only blank tags)...
				// It's better use the whole text of the message
				$sCaseLogEntry = $oEmail->sBodyText;
			}
			static::Trace("... Managing inline images...");
			$sCaseLogEntry = static::ManageInlineImages($sCaseLogEntry, false /* $bForPlainText */);
		}
		else {
			static::Trace("... Extracting the new part using GetNewPart()...");
			$sCaseLogEntry = $oEmail->GetNewPart($oEmail->sBodyText, $oEmail->sBodyFormat); // GetNewPart always returns a plain text version of the message
			$sCaseLogEntry = utils::TextToHtml($sCaseLogEntry);
		}
		
		return $sCaseLogEntry;
		
	 }
	 
	/**
	 * Function inspired by Combodo's MailInboxBase::AddAttachments()
	 * @param bool $bNoDuplicates If true, don't add attachment that seem already attached to the ticket (same type, same name, same size, same md5 checksum)
	 *
	 * @return array array of cid => attachment_id
	 * @throws \CoreException
	 * @throws \CoreUnexpectedValue
	 * @throws \CoreWarning
	 * @throws \Exception
	 * @throws \OQLException
	 *
	 * @return void
	 *
	 * @todo Test what happens if the same file is attached twice to an e-mail?
	 */
	public static function AddAttachments($bNoDuplicates = true) {
		
		$oEmail = static::GetMail();
		$oTicket = static::GetTicket();
		
		// Process attachments (if any)
		$aPreviousAttachments = []; // Attachments already linked to this Ticket
		
		// Build a list of Attachments already present in this ticket.
		// This includes both Attachment and InlineImage classes.
		if($bNoDuplicates == true) {
			
			$sOQL = 'SELECT Attachment WHERE item_class = :class AND item_id = :id';
			$oAttachments = new DBObjectSet(DBObjectSearch::FromOQL_AllData($sOQL), [], ['class' => get_class($oTicket), 'id' => $oTicket->GetKey()]);
			while($oPrevAttachment = $oAttachments->Fetch()) {
				$oDoc = $oPrevAttachment->Get('contents');
				$data = $oDoc->GetData();
				$aPreviousAttachments[] = [
					'class' => 'Attachment',
					'filename' => $oDoc->GetFileName(),
					'mimeType' => $oDoc->GetMimeType(),
					'size' => strlen($data),
					'md5' => md5($data),
					'object' => $oPrevAttachment,
				];
			}
			
			// same processing for InlineImages
			if(class_exists('InlineImage') == true) {
				$sOQL = 'SELECT InlineImage WHERE item_class = :class AND item_id = :id';
				$oAttachments = new DBObjectSet(DBObjectSearch::FromOQL_AllData($sOQL), [], ['class' => get_class($oTicket), 'id' => $oTicket->GetKey()]);
				while($oPrevAttachment = $oAttachments->Fetch()) {
					$oDoc = $oPrevAttachment->Get('contents');
					$data = $oDoc->GetData();
					$aPreviousAttachments[] = [
						'class' => 'InlineImage',
						'filename' => $oDoc->GetFileName(),
						'mimeType' => $oDoc->GetMimeType(),
						'size' => strlen($data),
						'md5' => md5($data),
						'object' => $oPrevAttachment,
					];
				}
			}
		}
		
		foreach($oEmail->aAttachments as $aAttachment) {
			
			$bIgnoreAttachment = false;
			
			if($bIgnoreAttachment == false && $bNoDuplicates == true) {
				
				// Check if an attachment with the same name/type/size/md5 already exists
				$iSize = strlen($aAttachment['content']);
				$sMd5 = md5($aAttachment['content']);
				foreach($aPreviousAttachments as $aPrevious) {
					if (
						($aAttachment['filename'] == $aPrevious['filename']) &&
						($aAttachment['mimeType'] == $aPrevious['mimeType']) &&
						($iSize == $aPrevious['size']) &&
						($sMd5 == $aPrevious['md5']) )
					{
						// Skip this attachment
						static::Trace("... Attachment {$aAttachment['filename']} skipped, already attached to the ticket.");
						static::$aAddedAttachments[$aAttachment['content-id']] = $aPrevious['object']; // Still remember it for processing inline images
						$bIgnoreAttachment = true;
						break;
					}
				}
				
				if($bIgnoreAttachment == false) {
					
					if(static::IsImage($aAttachment['mimeType']) && class_exists('InlineImage') && $aAttachment['inline']) {
						$oAttachment = new InlineImage();
						static::Trace("... Attachment {$aAttachment['filename']} will be stored as an InlineImage.");
						$oAttachment->Set('secret', sprintf ('%06x', mt_rand(0, 0xFFFFFF))); // something not easy to guess
					}
					else {
						static::Trace("... Attachment {$aAttachment['filename']} will be stored as an Attachment.");
						$oAttachment = new Attachment();
					}
					if ($oTicket->IsNew()) {
						$oAttachment->Set('item_class', get_class($oTicket));
					}
					else {
						$oAttachment->SetItem($oTicket);
					}
					
					$oBlob = new ormDocument($aAttachment['content'], $aAttachment['mimeType'], $aAttachment['filename']);
					$oAttachment->Set('contents', $oBlob);
					$oAttachment->DBInsert();
					$oMyChangeOp = MetaModel::NewObject('CMDBChangeOpPlugin');
					$oMyChange = CMDBObject::GetCurrentChange();
					$oMyChangeOp->Set('change', $oMyChange->GetKey());
					$oMyChangeOp->Set('objclass', get_class($oTicket));
					$oMyChangeOp->Set('objkey', $oTicket->GetKey());
					$oMyChangeOp->Set('description', Dict::Format('Attachments:History_File_Added', $aAttachment['filename']));
					$iId = $oMyChangeOp->DBInsertNoReload();
					static::Trace("... Attachment {$aAttachment['filename']} added to the ticket.");
					static::$aAddedAttachments[$aAttachment['content-id']] = $oAttachment;
				}
			}
			else {
				static::Trace("... The attachment {$aAttachment['filename']} was NOT added to the ticket because its type '{$aAttachment['mimeType']}' is excluded according to the configuration");
			}
		}
		
		$iCount = count(static::$aAddedAttachments);
		static::Trace(".. Added {$iCount} attachments".($iCount > 0 ? " ".implode(', ', array_keys(static::$aAddedAttachments)) : ""));
		
	 }
	 
	/**
	 * Function inspired by Combodo's MailInboxBase::UpdateAttachments()
	 * Links a collection of attachments to a newly created ticket.
	 *
	 * @uses PolicyCreateOrUpdateTicket::$aAddedAttachments
	 *
	 * @return void
	 */
	public static function UpdateAttachments() {
		
		$iNumAttachments = count(static::$aAddedAttachments);
		if($iNumAttachments > 0) {
			static::Trace("... Linking {$iNumAttachments} attachments...");
		}
			
		foreach(static::$aAddedAttachments as $oAttachment) {
			$oTicket = static::GetTicket();
			$oAttachment->SetItem($oTicket);
			$oAttachment->DBUpdate();
		}
		
	}
	 
	/**
	 * Function inspired by Combodo's MailInboxBase::IsImage()
	 * Checks whether a MimeType is an image which can be processed by iTop (PHP GD)
	 *
	 * @param \String $sMimeType
	 *
	 * @return \Boolean
	 */
	public static function IsImage($sMimeType) {
				
		if(function_exists('gd_info') == false) {
			return false; // no image processing capability on this system
		}
		
		$bRet = false;
		$aInfo = gd_info(); // What are the capabilities
		switch($sMimeType)
		{
			case 'image/gif':
				return $aInfo['GIF Read Support'];
				break;
			
			case 'image/jpeg':
				return $aInfo['JPEG Support'];
				break;
			
			case 'image/png':
				return $aInfo['PNG Support'];
				break;

		}
		
		return $bRet;
	}
	
	/**
	 * Function inspired by Combodo's MailInboxBase::FitTextIn()
	 * Truncates the text, if needed, to fit into the given the maximum length and:
	 * 1) Takes care of replacing line endings by \r\n since the browser produces this kind of line endings inside a TEXTAREA
	 * 2) Trims the result to emulate the behavior of iTop's inputs
	 *
	 * @param \String $sInputText
	 * @param \Int $iMaxLength
	 *
	 * @return \String The fitted text
	 */
	public static function FitTextIn($sInputText, $iMaxLength) {
		$sInputText = trim($sInputText);
		$sInputText = str_replace("\r\n", "\r", $sInputText);
		$sInputText = str_replace("\n", "\r", $sInputText);
		$sInputText = str_replace("\r", "\r\n", $sInputText);
		if (strlen($sInputText) > $iMaxLength) {
			$sInputText = trim(substr($sInputText, 0, $iMaxLength-3)).'...';
		}
		return $sInputText;
	}
	 
}


/**
 * Class PolicyBounceOtherEmailCallerThanTicketCaller Bounce policy in case the email caller is not the ticket caller
 */
abstract class PolicyBounceOtherEmailCallerThanTicketCaller extends Policy implements iPolicy {
	
	/**
	 * @var \Integer $iPrecedence It's not necessary that this number is unique; but when all policies are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 */
	public static $iPrecedence = 10;
	
	/**
	 * @var \String $sPolicyId Shortname for policy
	 */
	public static $sPolicyId = 'policy_other_email_caller_than_ticket_caller';
		
	/**
	 * @inheritDoc
	 */
	public static function IsCompliant() {
		
		// @todo Accept known contacts (from same org)
		
		$oTicket = static::GetTicket();
		$oEmail = static::GetMail();
		$oMailBox = static::GetMailBox();
		
		// Generic 'before' actions
		parent::BeforeComplianceCheck();
		
		if($oTicket !== null) {
				
			// Checking if attachments are in line with configured policies.
			switch($oMailBox->Get(static::GetPolicyId().'_behavior')) {
			
				case 'bounce_delete':
				case 'bounce_mark_as_undesired':
				case 'delete':
				case 'mark_as_undesired':
				case 'do_nothing':
					
					// Other caller?
					$sTicketCallerEmail = $oTicket->Get('caller_id->email');
					if($sTicketCallerEmail != $oEmail->sCallerEmail) {
						
						static::Trace('.. Ticket caller\'s email address is different from the email caller\'s email address.');
						static::HandleViolation();
						return false;
					
					}
				
					break;
					
				default:
					static::Trace('.. Unexpected "behavior"');
					break;
			}
		
		}
			
		// Generic 'after' actions
		parent::AfterPassedComplianceCheck();
		
		return true;
		
	}
	
}

/**
 * Class PolicyBounceAttachmentForbiddenMimeType Offers a policy to enforce some rules on the attachment.
 */
abstract class PolicyBounceAttachmentForbiddenMimeType extends Policy implements iPolicy {
	
	/**
	 * @var \Integer $iPrecedence It's not necessary that this number is unique; but when all policies are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 */
	public static $iPrecedence = 10;
	
	/**
	 * @var \String $sPolicyId Shortname for policy
	 */
	public static $sPolicyId = 'policy_attachment_forbidden_mimetype';
		
	/**
	 * @inheritDoc
	 */
	public static function IsCompliant() {
		
		$oMailBox = static::GetMailBox();
		$oEmail = static::GetMail();
		
		// Generic 'before' actions
		parent::BeforeComplianceCheck();
		
		// Checking if attachments are in line with configured policies.
		
			$sForbiddenMimeTypes = $oMailBox->Get(static::GetPolicyId().'_mimetypes');
			
			if(trim($sForbiddenMimeTypes) == '') {
				static::Trace('.. No forbidden MimeTypes specified.');
			}
			else {
				
				$aForbiddenMimeTypes = preg_split(NEWLINE_REGEX, $sForbiddenMimeTypes);
			
				static::Trace('.. Forbidden MimeTypes: '. implode(' - ', $aForbiddenMimeTypes));
				static::Trace('.. # Attachments: '. count($oEmail->aAttachments));
				
				switch($oMailBox->Get(static::GetPolicyId().'_behavior')) {
					
					case 'bounce_delete':
					case 'bounce_mark_as_undesired':
					case 'delete':
					case 'mark_as_undesired':
					case 'do_nothing':
						
						// Forbidden attachments? 
						foreach($oEmail->aAttachments as $aAttachment) { 
							static::Trace('.. Attachment MimeType: '.$aAttachment['mimeType']);
							
							if(in_array($aAttachment['mimeType'], $aForbiddenMimeTypes) == true) {
								
								static::Trace('... Found attachment with forbidden MimeType "'.$aAttachment['mimeType'].'"');
								static::HandleViolation();
								
								// No specific fallback								
								// Stop processing any further!
								return false;
							}
						}
					
						break; // Defensive programming
					
					case 'fallback_ignore_forbidden_attachments':
					
						// Ticket will be processed. Forbidden attachments will be removed here.
						foreach($oEmail->aAttachments as $index => $aAttachment) { 
							if(in_array($aAttachment['mimeType'], $aForbiddenMimeTypes) == true) {
								static::Trace("... Attachment Content-Id ". $aAttachment['content-id'] . " - Mime Type: {$aAttachment['mimeType']} = forbidden.");
								// Removing attachment
								unset($oEmail->aAttachments[$index]);
							}
							else {
								static::Trace("... Attachment Content-Id ". $aAttachment['content-id'] . " - Mime Type: {$aAttachment['mimeType']} = allowed.");
							}
						}
						break;
						
					
					default:
						static::Trace('.. Unexpected "behavior"');
						break;
				}
		
			}
			
		// Generic 'after' actions
		parent::AfterPassedComplianceCheck();
		
		return true;
		
	}
	
}

/**
 * Class PolicyBounceLimitMailSize Offers a policy to prevent big email messages from being processed
 */
abstract class PolicyBounceLimitMailSize extends Policy implements iPolicy {
	
	/**
	 * @var \Integer $iPrecedence It's not necessary that this number is unique; but when all policies are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 */
	public static $iPrecedence = 10;
	
	/**
	 * @var \String $sPolicyId Shortname for policy
	 */
	public static $sPolicyId = 'policy_mail_size_too_big';
		
	/**
	 * @inheritDoc
	 */
	public static function IsCompliant() {
		
		// Generic 'before' actions
		parent::BeforeComplianceCheck();
		
		$oMailBox = static::GetMailBox();
		$oEmail = static::GetMail();
		
		// Checking if mail size is not too big
		$iMaxSizeMB = $oMailBox->Get(static::GetPolicyId().'_max_size_MB');
		
		if($iMaxSizeMB > 0) {
		
			$iMailSize = $oEmail->oRawEmail->GetSize();
			$iLimitMailSize = utils::ConvertToBytes($iMaxSizeMB.'M');
			
			if($iMailSize > $iLimitMailSize) {
				
				// Mail size too big
				static::Trace('.. Undesired: mail size too big: '.$iMailSize.' bytes, limit is '.$iLimitMailSize.' bytes ('.$iMaxSizeMB.' M).');
				static::HandleViolation();
				
				// No fallback
				
				// Stop processing any further!
				return false;
				
			}
			
		}
			
		// Generic 'after' actions
		parent::AfterPassedComplianceCheck();
		
		return true;
		
	}
	
}

/**
 * Class PolicyBounceNoSubject Offers a policy to enforce non-empty subjects
 */
abstract class PolicyBounceNoSubject extends Policy implements iPolicy {
	
	/**
	 * @var \Integer $iPrecedence It's not necessary that this number is unique; but when all policies are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 */
	public static $iPrecedence = 10;
	
	/**
	 * @var \String $sPolicyId Shortname for policy
	 */
	public static $sPolicyId = 'policy_no_subject';
		
	/**
	 * @inheritDoc
	 */
	public static function IsCompliant() {
		
		// Generic 'before' actions
		parent::BeforeComplianceCheck();
		
		$oMailBox = static::GetMailBox();
		$oEmail = static::GetMail();
		
		// Checking if subject is not empty.
		
			$sPolicyBehavior = $oMailBox->Get(static::GetPolicyId().'_behavior');
			
			if($oEmail->sSubject == '') {
				
				switch($sPolicyBehavior) {
					 // Will use default subject.
					 case 'bounce_delete':
					 case 'bounce_mark_as_undesired':
					 case 'delete':
					 case 'do_nothing':
					 case 'mark_as_undesired':

						// No subject (and no fallback)
						static::Trace('.. Undesired: Empty subject.');
						static::HandleViolation();
						
						// No fallback
						
						// Stop processing any further!
						return false;
						
						break; // Defensive programming
						
					case 'fallback_default_subject':
					
						// Set ticket title of email message
						// Setting the ticket title on the ticket object happens later and not in this policy!
						$sDefaultTitle = $oMailBox->Get(static::GetPolicyId().'_default_value');
						
						// Inproper configuration
						if(trim($sDefaultTitle) == '') {
							static::Trace('.. Undesired: Empty subject. Fallback to set a default subject failed, because default subject is empty.');
							static::HandleViolation();
							return false;
						}
						
						static::Trace('.. Fallback: changing empty subject to "'.$sDefaultTitle.'"');
						$oEmail->sSubject = $sDefaultTitle;
						break;
					
					default:
						static::Trace('.. Unexpected "behavior"');
						break;
					
				}
			
			}
			
		// Generic 'after' actions
		parent::AfterPassedComplianceCheck();
		
		return true;
		
	}
	
}

/**
 * Class PolicyBounceOtherRecipients Offers a policy to ensure that the service desk mailbox is the sole recipient (no other recipients in To:, CC:)
 * Does NOT change "related contacts" or create new ones!
 */
abstract class PolicyBounceOtherRecipients extends Policy implements iPolicy {
	
	/**
	 * @var \Integer $iPrecedence It's not necessary that this number is unique; but when all policies are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 */
	public static $iPrecedence = 20;
	
	/**
	 * @var \String $sPolicyId Shortname for policy
	 */
	public static $sPolicyId = 'policy_other_recipients';
	
	/**
	 * @inheritDoc
	 */
	public static function IsCompliant() {
		
		// Generic 'before' actions
		parent::BeforeComplianceCheck();
		
		$oEmail = static::GetMail();
		$oMailBox = static::GetMailBox();
		
		
		// Checking if there are no other recipients mentioned.
		
			$sCallerEmail = $oEmail->sCallerEmail;
								
			// Take both the To: and CC:
			$aAllContacts = array_merge($oEmail->aTos, $oEmail->aCCs);
			
			// Mailbox aliases
			$sMailBoxAliases = $oMailBox->Get('mail_aliases');
			$aMailBoxAliases = (trim($sMailBoxAliases) == '' ? [] : preg_split(NEWLINE_REGEX, $sMailBoxAliases));
			
			// Ignore sender; helpdesk mailbox; any helpdesk mailbox aliases
			$aAllowedContacts = array_merge([$oEmail->sCallerEmail, $oMailBox->Get('login')], $aMailBoxAliases);
			$aAllowedContacts = array_unique($aAllowedContacts);

			$sPolicyBehavior = $oMailBox->Get(static::GetPolicyId().'_behavior');
			
			switch($sPolicyBehavior) {
				 case 'bounce_delete':
				 case 'bounce_mark_as_undesired':
				 case 'delete':
				 case 'mark_as_undesired':
				
					foreach($aAllContacts as $aContactInfo) {
						$sCurrentEmail = $aContactInfo['email'];
						
						foreach($aAllowedContacts as $sPattern) {
							
							// Regular e-mail address? Make case insensitive pattern
							if(filter_var($sPattern, FILTER_VALIDATE_EMAIL) == true) {
								$sPattern = '/^'.preg_quote($sPattern).'$/i';
							}
							$oPregMatch = @preg_match($sPattern, $sCurrentEmail);
							
							if($oPregMatch === false) {
								
								// Pattern mistake
								static::Trace(".. Invalid pattern: '{$sPattern}'");
								continue 2;
							}
							elseif(preg_match($sPattern, $sCurrentEmail)) {
								
								// This e-mail (from:) is allowed
								continue 2;
								
							}
							
						}
						
						// Did not match caller e-mail or any alias of this helpdesk mailbox
						static::Trace(".. Undesired: at least one other recipient (missing alias or unwanted other recipient): {$aContactInfo['email']}");
						static::HandleViolation();
						return false;
						
					}

					break; // Defensive programming
					
				case 'do_nothing':
				case 'fallback_ignore_other_contacts':
				
					// Will be handled later (by default in PolicyFindAdditionalContacts)
					static::Trace(".. Other contacts in To: or CC: will be ignored for this email in further processing.");
					break;
					
				case 'fallback_add_other_contacts':
				case 'fallback_add_existing_other_contacts':
				
					// Will be handled later (by default in PolicyFindAdditionalContacts)
					static::Trace(".. Other contacts in To: or CC: will be handled in PolicyFindAdditionalContacts.");
					break;
				
				default:
					static::Trace(".. Unexpected 'behavior'");
					break;
				
			}
			
		// Generic 'after' actions
		parent::AfterPassedComplianceCheck();
		
		return true;
		
	}
	
}

/**
 * Class PolicyBounceUnknownTicketReference Offers a policy to handle unknown ticket references. Also see MailInboxStandard::GetRelatedTicket()
 * @todo Check if this works properly
 */
abstract class PolicyBounceUnknownTicketReference extends Policy implements iPolicy {
	
	/**
	 * @var \Integer $iPrecedence It's not necessary that this number is unique; but when all policies are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 */
	public static $iPrecedence = 10;
	
	/**
	 * @var \String $sPolicyId Shortname for policy
	 */
	public static $sPolicyId = 'policy_ticket_unknown';
	
	/**
	 * @inheritDoc
	 */
	public static function IsCompliant() {

		// Generic 'before' actions
		parent::BeforeComplianceCheck();
		
		$oMailBox = static::GetMailBox();
		$oEmail = static::GetMail();
		$oTicket = static::GetTicket();
		
		// Is the ticket valid in the iTop database or does the number NOT match?
		// Checking if ticket reference is invalid
		// Due to an earlier GetRelatedTicket() call in MailInboxStandard, Ticket would NOT have been null if there was a valid reference.
		if($oTicket === null) {
		
			// This could be a new ticket. Then it's logical the Ticket object is null. 
			// So check if there was something (header or pattern in subject) which would have lead the system to believe there was a ticket. 
			
			// Are there patterns which should be ignored/removed from the title? 
			// To find the reference, let's remove it from our temp variable. 
			$sSubject = $oEmail->sSubject;
			
			// Here the removal/ignoring of patterns happens; on a copy of the subject string; only to find related tickets.
			foreach(['policy_remove_pattern_patterns', 'title_pattern_ignore_patterns'] as $sAttCode) {
				$sPatterns = $oMailBox->Get($sAttCode);
				
				if(trim($sPatterns) != '') {
					
					$aPatterns = preg_split(NEWLINE_REGEX, $sPatterns);
					
					static::Trace(".. Ignoring patterns (defined in {$sAttCode}): {$sPatterns}");
					
					foreach($aPatterns as $sPattern) {
						if(trim($sPattern) != '') {
							$oPregMatch = @preg_match($sPattern, $sSubject);
							
							if($oPregMatch === false) {
								static::Trace("... Invalid pattern: '{$sPattern}'");
							}
							elseif(preg_match($sPattern, $sSubject)) {
								static::Trace("... Removing: '{$sPattern}'");
								$sSubject = preg_replace($sPattern, '', $sSubject);
							}
							else {
								// Just not matching
							}
						}
					}
				}
			}
			
			$sPattern = $oMailBox->FixPattern($oMailBox->Get('title_pattern'));
			if(($sPattern != '') && (preg_match($sPattern, $sSubject, $aMatches))) {
				static::Trace(".. Undesired: unable to find any prior ticket despite a matching ticket reference pattern in the subject ('{$sPattern}'). ".http_build_query($aMatches));
				return false;
			}
			elseif($oEmail->oRelatedObject != null ) {
				static::Trace(".. Undesired: unable to find any prior ticket despite an email header ({$oEmail->oRelatedObject}).");
				return false;
			}
			else {
				static::Trace(".. Not undesired? Pattern = ".$sPattern." - subject: ".$sSubject);
			}
		
		}
		else {
			static::Trace(".. Already linked to a Ticket");
		}
			
		// Generic 'after' actions
		parent::AfterPassedComplianceCheck();
		
		return true;
		
	}
	
}

/**
 * Class PolicyTicketResolved Offers a policy to handle replies to resolved tickets.
 */
abstract class PolicyTicketResolved extends Policy implements iPolicy {
	
	/**
	 * @var \Integer $iPrecedence It's not necessary that this number is unique; but when all policies are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 */
	public static $iPrecedence = 20;
	
	/**
	 * @var \String $sPolicyId Shortname for policy
	 */
	public static $sPolicyId = 'policy_ticket_resolved';
	
	/**
	 * @inheritDoc
	 */
	public static function IsCompliant() {
		
		// Generic 'before' actions
		parent::BeforeComplianceCheck();
		
		$oTicket = static::GetTicket();
		$oMailBox = static::GetMailBox();
		
		// Checking if a previous ticket was found
			if($oTicket !== null) {
				if($oTicket->Get('status') == 'resolved') {
					
					static::Trace(".. Ticket was marked as resolved before.");
							
					switch($oMailBox->Get(static::GetPolicyId().'_behavior')) { 
						case 'bounce_delete': 
						case 'bounce_mark_as_undesired':
						case 'delete':
						case 'do_nothing':
						case 'mark_as_undesired':
						
							static::HandleViolation();
							
							// No fallback
							
							// Stop processing any further!
							return false;

							break; // Defensive programming
							 
						case 'fallback_reopen': 
							// Reopen ticket
							static::Trace("... Fallback: reopen resolved ticket."); 
							$oTicket->ApplyStimulus('ev_reopen');
							break; 
							
						default:
							// Should not happen.
							static::Trace("... Unknown action for resolved tickets.");
							break; 
						
					}
				}
			}
			
		// Generic 'after' actions
		parent::AfterPassedComplianceCheck();
		
		return true;
		
	}
	
}

/**
 * Class PolicyTicketClosed Offers a policy to handle replies to closed tickets.
 */
abstract class PolicyTicketClosed extends Policy implements iPolicy {
	
	/**
	 * @var \Integer $iPrecedence It's not necessary that this number is unique; but when all policies are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 */
	public static $iPrecedence = 20;
	
	/**
	 * @var \String $sPolicyId Shortname for policy
	 */
	public static $sPolicyId = 'policy_ticket_closed';
	
	/**
	 * @inheritDoc
	 */
	public static function IsCompliant() {
		
		// Generic 'before' actions
		parent::BeforeComplianceCheck();
		
		$oTicket = static::GetTicket();
		$oMailBox = static::GetMailBox();
		
		// Checking if a previous ticket was found
			if($oTicket !== null) {
				if($oTicket->Get('status') == 'closed') {
						
					switch($oMailBox->Get(static::GetPolicyId().'_behavior')) { 
						case 'bounce_delete': 
						case 'bounce_mark_as_undesired':
						case 'delete':
						case 'do_nothing':
						case 'mark_as_undesired':
						
							static::Trace(".. Undesired: ticket was marked as closed before.");
							static::HandleViolation();
							
							// No fallback
							
							// Stop processing any further!
							return false;

							break; // Defensive programming
							 
						case 'fallback_reopen': 
							// Reopen ticket
							static::Trace("... Fallback: reopen closed ticket."); 
							$oTicket->ApplyStimulus('ev_reopen');
							break; 
							
						default:
							// Should not happen.
							static::Trace("... Unknown action for closed tickets.");
							break; 
						
					}
				}
			}
			
		// Generic 'after' actions
		parent::AfterPassedComplianceCheck();
		
		return true;
		
	}
	
}

/**
 * Class PolicyBounceUndesiredTitlePatterns Offers a policy to handle undesired title patterns.
 */
abstract class PolicyBounceUndesiredTitlePatterns extends Policy implements iPolicy {
	
	/**
	 * @var \Integer $iPrecedence It's not necessary that this number is unique; but when all policies are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 */
	public static $iPrecedence = 20;
	
	/**
	 * @var \String $sPolicyId Shortname for policy
	 */
	public static $sPolicyId = 'policy_undesired_pattern';
	
	/**
	 * @inheritDoc
	 */
	public static function IsCompliant() {
		
		// Generic 'before' actions
		parent::BeforeComplianceCheck();
		
		$oMailBox = static::GetMailBox();
		$oEmail = static::GetMail();
		
		// Checking if an undesired title pattern is found

			if(trim($oMailBox->Get(static::GetPolicyId().'_patterns')) != '' ) { 
			
				// Go over each pattern and check.
				$aPatterns = preg_split(NEWLINE_REGEX, $oMailBox->Get(static::GetPolicyId().'_patterns')); 
				$sMailSubject = $oEmail->sSubject;
				
				foreach($aPatterns as $sPattern) {
					if(trim($sPattern) != '') {
							
						$oPregMatched = @preg_match($sPattern, $sMailSubject);
						
						if($oPregMatched === false) {
							static::Trace(".. Invalid pattern: '{$sPattern}'");
						}
						elseif(preg_match($sPattern, $sMailSubject)) {
							
							switch($oMailBox->Get(static::GetPolicyId().'_behavior')) { 
								case 'bounce_delete': 
								case 'bounce_mark_as_undesired':
								case 'delete':
								case 'do_nothing':
								case 'mark_as_undesired':
								
									static::Trace(".. The message '{$sMailSubject}' is considered as undesired, since it matches {$sPattern}.");
									static::HandleViolation();
									
									// No fallback
									
									// Stop processing any further!
									return false;

									break; // Defensive programming
									
								default:
									// Should not happen.
									static::Trace(".. Unknown action for closed tickets.");
									break; 
								
							}
							
						}
						else {
							static::Trace(".. Pattern '{$sPattern}' not matched");
						}
					}
				}
			}
			
		// Generic 'after' actions
		parent::AfterPassedComplianceCheck();
		
		return true;
		
	}
	
}

/**
 * Class PolicyFindCaller Offers a policy to find the caller and create a Person with default values if the caller appears to be unknown.
 */
abstract class PolicyFindCaller extends Policy implements iPolicy {
	
	/**
	 * @var \Integer $iPrecedence It's not necessary that this number is unique; but when all policies are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 */
	public static $iPrecedence = 110;
	
	/**
	 * @var \String $sPolicyId Shortname for policy
	 * @details Note: different short name because of (legacy) attribute fields!
	 */
	public static $sPolicyId = 'policy_unknown_caller';
	
	/**
	 * @inheritDoc
	 *
	 * @todo Test if default values properly support mail placeholders
	 */
	public static function IsCompliant() {
		
		// Generic 'before' actions
		parent::BeforeComplianceCheck();
		
		$oMailBox = static::GetMailBox();
		$oEmail = static::GetMail();
		
		// Checking if there's an unknown caller
		
			if(isset($oEmail->oInternal_Contact) == false || $oEmail->oInternal_Contact === null) {
				
				$sCallerEmail = $oEmail->sCallerEmail;
				static::Trace("... Determine caller: Person with email '{$sCallerEmail}'");
				
				$oCaller = null;
				$sContactQuery = 'SELECT Person WHERE email = :email';
				
				$oSet_Person = new DBObjectSet(DBObjectSearch::FromOQL($sContactQuery), [], ['email' => $sCallerEmail]);
				
				switch($oSet_Person->Count()) {
					
					case 1:
						// Ok, the caller was found in iTop
						static::Trace("... Found person.");
						$oCaller = $oSet_Person->Fetch();
						break;
						
					case 0:

						// Caller was not found.
						switch($oMailBox->Get(static::GetPolicyId().'_behavior')) {
							
							case 'bounce_delete':
							case 'bounce_mark_as_undesired':
							case 'delete':
							case 'mark_as_undesired':
							
								static::Trace("... The message '{$oEmail->sSubject}' is considered as undesired, the caller was not found.");
								static::HandleViolation();
								
								// No fallback
								return false;

								break; // Defensive programming

							case 'fallback_create_person':
								
								static::Trace("... Creating a new Person for the email: {$sCallerEmail}");
								$oCaller = new Person();
								$oCaller->Set('email', $oEmail->sCallerEmail);
								$sDefaultValues = $oMailBox->Get(static::GetPolicyId().'_default_values');
								
								if(trim($sDefaultValues) != '') {
									
									try {
									
										$aDefaults = preg_split(NEWLINE_REGEX, $sDefaultValues);
										$aDefaultValues = [];
										
										foreach($aDefaults as $sLine) {
											if(preg_match('/^([^:]+):(.*)$/', $sLine, $aMatches)) {
												$sAttCode = trim($aMatches[1]);
												$sValue = trim($aMatches[2]);
												$sValue = static::ReplaceMailPlaceholders($sValue);
												$aDefaultValues[$sAttCode] = $sValue;
											}
										}
										
										static::Trace('... Default values: '.http_build_query($aDefaultValues));
										$oMailBox->InitObjectFromDefaultValues($oCaller, $aDefaultValues);
										
										static::Trace("... Create user with default values");
										$oCaller->DBInsert();					
									}
									catch(Exception $e) {
										// This is an actual error.
										static::Trace("... Failed to create a Person for the email address '{$sCallerEmail}'.");
										static::Trace($e->getMessage());
										$oMailBox->HandleError($oEmail, 'failed_to_create_contact', $oEmail->oRawEmail);
										return false;
									}

								}
								else {
									static::Trace('... Default values are missing. Can not create contact.');
									$oMailBox->HandleError($oEmail, 'failed_to_create_contact', $oEmail->oRawEmail);
									return false;
								}
								
								break;
								
							default:
								break;
								
						}
						break;
						
					default:
						static::Trace("... Found ".$oSet_Person->Count()." callers with the same email address '{$sCallerEmail}', the first one will be used...");
						// Multiple callers with the same email address!
						$oCaller = $oSet_Person->Fetch();
						
				}
				
				// Set caller for email
				$oEmail->oInternal_Contact = $oCaller;
				
			}
			else {
				static::Trace("... Caller already determined by previous policy. Skip.");
			}
		
		// Generic 'after' actions
		parent::AfterPassedComplianceCheck();
		
		return true;
		
	}
	
}


/**
 * Class PolicyRemoveTitlePatterns Offers a policy to remove patterns in titles (in message subject and later ticket title)
 * @todo Check if this works properly
 */
abstract class PolicyRemoveTitlePatterns extends Policy implements iPolicy {
	
	/**
	 * @var \Integer $iPrecedence It's not necessary that this number is unique; but when all policies are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 */
	public static $iPrecedence = 110;
	
	/**
	 * @var \String $sPolicyId Shortname for policy
	 */
	public static $sPolicyId = 'policy_remove_pattern';
	
	/**
	 * @inheritDoc
	 */
	public static function IsCompliant() {
		
		// Generic 'before' actions
		parent::BeforeComplianceCheck();
		
		// Checking if an undesired title pattern is found
		
			$oMailBox = static::GetMailBox();
			$oEmail = static::GetMail();
			
			$sPatterns = $oMailBox->Get(static::GetPolicyId().'_patterns');

			if($sPatterns != '' ) { 
			
				// Go over each pattern and check.
				$aPatterns = preg_split(NEWLINE_REGEX, $sPatterns); 
				$sMailSubject = $oEmail->sSubject;
				
				foreach($aPatterns as $sPattern) {
					if(trim($sPattern) != '') {
							
						$oPregMatched = @preg_match($sPattern, $sMailSubject);
						
						if($oPregMatched === false) {
							static::Trace("... Invalid pattern: '{$sPattern}'");
						}
						elseif(preg_match($sPattern, $sMailSubject)) {
							
							switch($oMailBox->Get(static::GetPolicyId().'_behavior')) { 
								case 'fallback_remove':
								
									$sNewMailSubject = preg_replace($sPattern, '', $sMailSubject);
									
									if($sMailSubject == $sNewMailSubject) {
										static::Trace("... Found pattern to remove: {$sPattern}. Nothing to remove.");
									}
									else {
										static::Trace("... Found pattern to remove: {$sPattern}. Removing it.");
									}
									
									$oEmail->sSubject = $sNewMailSubject;
									
									break; // Defensive programming
									
								case 'do_nothing':
									// Should not happen.
									static::Trace("... Found pattern to remove: {$sPattern}. Doing nothing.");
									break; 
									
								default:
									// Should not happen.
									static::Trace("... Unknown action for closed tickets.");
									break; 
								
							}
							
						}
						else {
							static::Trace(".. Pattern '{$sPattern}' not matched");
						}
					}
				}
			}
			
		// Generic 'after' actions
		parent::AfterPassedComplianceCheck();
		
		return true;
		
	}
	
}


/**
 * Class PolicyFindAdditionalContacts Offers a policy to add "related contacts" to a Ticket. These are people in To: or CC: of processed e-mails that are NOT the original caller.
 */
abstract class PolicyFindAdditionalContacts extends Policy implements iPolicy {
	
	/**
	 * @var \Integer $iPrecedence It's not necessary that this number is unique; but when all policies are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 */
	public static $iPrecedence = 115;
	
	/**
	 * @var \String $sPolicyId Shortname for policy
	 * @details Deliberately re-uses this $sPolicyId
	 */
	public static $sPolicyId = 'policy_other_recipients';
	
	/**
	 * @inheritDoc
	 *
	 * @todo Test if mail placeholders work
	 */
	public static function IsCompliant() {
		
		// Generic 'before' actions
		parent::BeforeComplianceCheck();
		
		$oEmail = static::GetMail();
		$oMailBox = static::GetMailBox();
		$oTicket = static::GetTicket();
		
		$sCallerEmail = $oEmail->sCallerEmail;
							
		// Take both the To: and CC:
		$aAllContacts = array_merge($oEmail->aTos, $oEmail->aCCs);
		$aAllContacts = static::GetAddressesFromRecipients($aAllContacts);
		
		// Mailbox aliases
		$sMailBoxAliases = $oMailBox->Get('mail_aliases');
		$aMailBoxAliases = (trim($sMailBoxAliases) == '' ? [] : preg_split(NEWLINE_REGEX, $sMailBoxAliases));
		
		// Ignore helpdesk mailbox; any helpdesk mailbox aliases, original caller's email address
		if($oTicket !== null) {
			// For existing tickets: other people might reply. So only exclude mailbox aliases and the original caller.
			// If it's someone else replying, it should be seen as a new contact.
			$sOriginalCallerEmail = $oTicket->Get('caller_id->email');
			$aAllOtherContacts = array_udiff($aAllContacts, [$sOriginalCallerEmail, $oMailBox->Get('login')], $aMailBoxAliases, 'strcasecmp');
		}
		else {
			$aAllOtherContacts = array_udiff($aAllContacts, [$sCallerEmail, $oMailBox->Get('login')], $aMailBoxAliases, 'strcasecmp');
		}
		$aAllOtherContacts = array_unique($aAllOtherContacts);

		$sPolicyBehavior = $oMailBox->Get(static::GetPolicyId().'_behavior');
		
		switch($sPolicyBehavior) {
			
			case 'fallback_add_existing_other_contacts':
			case 'fallback_add_other_contacts':
		
				foreach($aAllOtherContacts as $sCurrentEmail) {
					
					// Found other contacts in To: or CC:
					static::Trace(".. Looking up Person with email address '{$sCurrentEmail}'");
							
					// Check if this contact exists.
					// Non-existing contacts must be created.
					// Actual linking of contacts happens after policies have been processed.
					$sContactQuery = 'SELECT Person WHERE email = :email';
					$oSet_Person = new DBObjectSet(DBObjectSearch::FromOQL($sContactQuery), [], [
						'email' => $sCurrentEmail
					]);
					
					if($oSet_Person->Count() == 0) {
						
						// Create
						static::Trace(".. Creating a new Person with email address '{$sCurrentEmail}'");
						$oContact = new Person();
						$oContact->Set('email', $sCurrentEmail);
						$sDefaultValues = $oMailBox->Get(static::GetPolicyId().'_default_values');
						$aDefaults = preg_split(NEWLINE_REGEX, $sDefaultValues);
						$aDefaultValues = array();
						foreach($aDefaults as $sLine) {
							if (preg_match('/^([^:]+):(.*)$/', $sLine, $aMatches)) {
								$sAttCode = trim($aMatches[1]);
								$sValue = trim($aMatches[2]);
								$sValue = static::ReplaceMailPlaceholders($sValue);
								$aDefaultValues[$sAttCode] = $sValue;
							}
						}
						
						$oMailBox->InitObjectFromDefaultValues($oContact, $aDefaultValues);
						try {
							static::Trace("... Try to create user with default values");
							$oContact->DBInsert();

							// Add Person to list of additional Contacts (handled in PolicyCreateOrUpdateTicket)
							$oEmail->aInternal_Additional_Contacts[] = $oContact;
							
						}
						catch(Exception $e) {
							// This is an actual error.
							static::Trace("... Failed to create a Person for the email address '{$sCallerEmail}'.");
							static::Trace($e->getMessage());
							$oMailBox->HandleError($oEmail, 'failed_to_create_contact', $oEmail->oRawEmail);
							return false;
						}									
						
					}
					elseif($oSet_Person->Count() == 1) {
						// Add Person to list of additional Contacts (handled in PolicyCreateOrUpdateTicket)
						$oContact = $oSet_Person->Fetch();
						$oEmail->aInternal_Additional_Contacts[] = $oContact;
					}
					else {
						// More than one Person returned. Inconclusive. Ignore?
					}
					
					
				}
				break;
				
			case 'bounce_delete':
			case 'bounce_mark_as_undesired':
			case 'delete':
			case 'do_nothing':
			case 'mark_as_undesired':
			
				// Will be added automatically later
				break;
				
			case 'fallback_ignore_other_contacts':
			
				// Make sure these contacts are not processed in any further processing?
				$oEmail->aTos = [];
				$oEmail->aCCs = [];
			
				static::Trace(".. Ignoring other contacts");
				break;
			
			default:
				static::Trace(".. Unexpected 'behavior'");
				break;
			
		}
			
		// Generic 'after' actions
		parent::AfterPassedComplianceCheck();
		
		return true;
		
	}
	
}

/**
 * Class PolicyAttachmentImageDimensions Offers a policy to ignore small image sizes (likely elements of an email signature) or resize larger images.
 */
abstract class PolicyAttachmentImageDimensions extends Policy implements iPolicy {
	
	/**
	 * @var \Integer $iPrecedence It's not necessary that this number is unique; but when all policies are listed; they will be sorted ascending (intended to make sure some checks run first; before others).
	 */
	public static $iPrecedence = 20;
	
	/**
	 * @var \String $sPolicyId Shortname for policy
	 */
	public static $sPolicyId = 'policy_attachment_image_dimensions';
	
	/**
	 * @inheritDoc
	 *
	 * @details
	 * Function inspired by Combodo's MailInboxBase::AddAttachments()
	 * Removes image attachments which are too small and also resizes images which are too large using php-gd
	 */
	public static function IsCompliant() {
		
		// Generic 'before' actions
		parent::BeforeComplianceCheck();
		
		// Checking if an undesired title pattern is found
		
			$oMailBox = static::GetMailBox();
			$oEmail = static::GetMail();
			
			// Ignore attachment or downsize?
			$iMinWidth = $oMailBox->Get(static::GetPolicyId().'_min_width');
			$iMaxWidth = $oMailBox->Get(static::GetPolicyId().'_max_width');
			$iMinHeight = $oMailBox->Get(static::GetPolicyId().'_min_height');
			$iMaxHeight = $oMailBox->Get(static::GetPolicyId().'_max_height');
			
			static::Trace(".. Min/max dimensions: {$iMinWidth}x{$iMinHeight} / {$iMaxWidth}x{$iMaxHeight}");
						
			$bCheckImageDimensionTooSmall = true;
			$bCheckImageDimensionTooLarge = true;
			
			// Remove images which are too small
			if($iMinWidth < 1 || $iMinWidth < 1) {
				static::Trace(".. Min dimensions can not be negative and should be at least 1x1 px.");
				$bCheckTooSmall = false;
			}
			
			if($iMaxWidth < 0 || $iMaxHeight < 0) {
				static::Trace(".. Max dimensions can not be negative.");
				$bCheckImageDimensionTooLarge = false;
			}
			
			if(function_exists('imagecopyresampled') == false) {
				static::Trace(".. php-gd seems to be missing. Resizing is not possible.");
				$bCheckImageDimensionTooLarge = false;
			}

			foreach($oEmail->aAttachments as $sAttachmentRef => &$aAttachment) {
				
				if(static::IsImage($aAttachment['mimeType']) == true) {
					
					$aImgInfo = static::GetImageSize($aAttachment['content']);
					if($aImgInfo !== false) {
						
						$iWidth = $aImgInfo[0];
						$iHeight = $aImgInfo[1];
						
						// Image too small?
						if($bCheckImageDimensionTooSmall == true && ($iWidth < $iMinWidth || $iHeight < $iMinHeight)) {
							
							// Unset
							static::Trace("... Image too small; unsetting {$sAttachmentRef}");
							unset($oEmail->aAttachments[$sAttachmentRef]);
							continue;
							
						}
						else {
							static::Trace("... Image not too small.");
						}
						
						// Image too large?
						if($bCheckImageDimensionTooLarge == true && ($iWidth > $iMaxWidth || $iHeight > $iMaxHeight)) {
							
							// Resize
							static::Trace("... Image too large; resizing {$sAttachmentRef}");
							$aAttachment = static::ResizeImageToFit($aAttachment, $iWidth, $iHeight, $iMaxWidth, $iMaxHeight);
							
						}
						else {
							static::Trace("... Image not too large.");
						}
					
					}
					else {
						static::Trace("... Could not determine dimensiosn of {$aAttachment['filename']}");
					}
					
				}
				else {
					static::Trace("... Attachment {$aAttachment['filename']} is not an image.");
				}
				
			}
			
		// Generic 'after' actions
		parent::AfterPassedComplianceCheck();
		
		return true;
		
	}
	
	/**
	 * Function inspired by Combodo's MailInboxBase::IsImage()
	 * Checks whether a MimeType is an image which can be processed by iTop (PHP GD)
	 *
	 * @param \String $sMimeType
	 *
	 * @return \Boolean
	 */
	public static function IsImage($sMimeType) {
				
		if(function_exists('gd_info') == false) {
			return false; // no image processing capability on this system
		}
		
		$bRet = false;
		$aInfo = gd_info(); // What are the capabilities
		switch($sMimeType)
		{
			case 'image/gif':
				return $aInfo['GIF Read Support'];
				break;
			
			case 'image/jpeg':
				return $aInfo['JPEG Support'];
				break;
			
			case 'image/png':
				return $aInfo['PNG Support'];
				break;

		}
		
		return $bRet;
	}
	
	/*
	 * Function inspired by Combodo's MailInboxBase::ResizeImageToFit()
	 * Resize an image attachment so that it fits in the given dimensions.
	 *
	 * @param \Array $aAttachment The original image stored as an attached array (content / mimetype / filename)
	 * @param \Int $iWidth image's original width
	 * @param \Int $iHeight image's original height
	 * @param \Int $iMaxImageWidth Maximum width for the resized image
	 * @param \Int $iMaxImageHeight Maximum height for the resized image
	 *
	 * @return \Array The modified attachment array with the resized image in the 'content'
	 */
	public static function ResizeImageToFit($aAttachment, $iWidth, $iHeight, $iMaxImageWidth, $iMaxImageHeight)
	{
		$img = false;
		switch($aAttachment['mimeType']) {
			case 'image/gif':
			case 'image/jpeg':
			case 'image/png':
				$img = @imagecreatefromstring($aAttachment['content']);
				break;
			
			default:
				// Unsupported image type, return the image as-is
				static::Trace("... Warning: unsupported image type: '{$aAttachment['mimeType']}'. Cannot resize the image, original image will be used.");
				return $aAttachment;
		}
		if ($img === false) {
			static::Trace("... Warning: corrupted image: '{$aAttachment['filename']} / {$aAttachment['mimeType']}'. Cannot resize the image, original image will be used.");
			return $aAttachment;
		}
		else {
			// Let's scale the image, preserving the transparency for GIFs and PNGs
			$fScale = min($iMaxImageWidth / $iWidth, $iMaxImageHeight / $iHeight);

			$iNewWidth = $iWidth * $fScale;
			$iNewHeight = $iHeight * $fScale;
			
			static::Trace("... Resizing image from ($iWidth x $iHeight) to ($iNewWidth x $iNewHeight) px");
			$new = imagecreatetruecolor($iNewWidth, $iNewHeight);
			
			// Preserve transparency
			if(($aAttachment['mimeType'] == 'image/gif') || ($aAttachment['mimeType'] == 'image/png'))
			{
				imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
				imagealphablending($new, false);
				imagesavealpha($new, true);
			}
			
			imagecopyresampled($new, $img, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $iWidth, $iHeight);
			
			ob_start();
			switch ($aAttachment['mimeType']) {
				case 'image/gif':
					imagegif($new); // send image to output buffer
					break;
				
				case 'image/jpeg':
					imagejpeg($new, null, 80); // null = send image to output buffer, 80 = good quality
					break;
				 
				case 'image/png':
					imagepng($new, null, 5); // null = send image to output buffer, 5 = medium compression
					break;
			}
			$aAttachment['content'] = ob_get_contents();
			@ob_end_clean();
			
			imagedestroy($img);
			imagedestroy($new);
			
			static::Trace("... Resized image is ".strlen($aAttachment['content'])." bytes long.");
				
			return $aAttachment;
		}
				
	}
		
	
	/*
	 * Function inspired by Combodo's MailInboxBase::GetImageSize()
	 * Resize an image attachment so that it fits in the given dimensions.
	 *
	 * @param \String $sImageData Image data
	 *
	 * @return \Array Array with image dimensions
	 */
	public static function GetImageSize($sImageData) {
		
		if(function_exists('getimagesizefromstring') == true ) {
			// PHP 5.4.0 or higher
			$aRet = @getimagesizefromstring($sImageData);
		}
		elseif(ini_get('allow_url_fopen')) {
			// work around to avoid creating a tmp file
			$sUri = 'data://application/octet-stream;base64,'.base64_encode($sImageData);
			$aRet = @getimagesize($sUri);
		}
		else {
			// Damned, need to create a tmp file
			$sTempFile = tempnam(SetupUtils::GetTmpDir(), 'img-');
			@file_put_contents($sTempFile, $sImageData);
			$aRet = @getimagesize($sTempFile);
			@unlink($sTempFile);
		}
		return $aRet;
		
	}
	
}




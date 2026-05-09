<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260426
 */
 

namespace JeffreyBostoenExtensions\MailToTicket\Steps\Core;

use JeffreyBostoenExtensions\MailToTicket\Steps\{
	PolicyBehavior,
	Base
};
use JeffreyBostoenExtensions\MailToTicket\{
	eNextAction,
	ProcessingHelper
};
use jb_itop_extensions\components\ormCustomCaseLog;

// iTop.
use Attachment;
use AttributeExternalKey;
use AttributeHTML;
use AttributeText;
use CMDBChange;
use CMDBObject;
use CMDBSource;
use DBObjectSearch;
use DBObjectSet;
use Dict;
use InlineImage;
use IssueLog;
use lnkContactToTicket;
use MetaModel;
use ormDocument;
use Person;
use Ticket;
use Trigger;
use User;
use UserRights;
use utils;

// Generic.
use Exception;

/**
 * Class CreateOrUpdateTicket. Special step; at this point the Ticket is created or updated.
 * @details Replaces Combodo's MailInboxStandard way of handling incoming emails (creating or updating ticket).
 * This is a fork, a lot of code has been polished but is also still the same or heavily inspired by the original Mail to Ticket Automation.
 * Hence, this (sub)class contains methods with the same name to make it easy to keep retrofitting bug fixes etc.
 */
abstract class CreateOrUpdateTicket extends Base {
	
	/**
	 * @inheritDoc
	 *
	 * @details This is a special policy which takes care of only basic Ticket creation or update. 
	 * Any real checks that block Ticket creation or update, should have been run by now. 
	 * Any policies following this one, should not be blocking!
	 */
	public static $iPrecedence = 200;
	
	/**
	 * @inheritDoc
	 */
	public static $sXMLSettingsPrefix = 'step_create_or_update_ticket';
	
	/*
	 * @var Array $aAddedAttachments Array containing info on any attachments in the email
	 */
	public static $aAddedAttachments = [];
	
	
	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {
		
		// Reset before processing each mail.
		static::$aAddedAttachments = [];
		
		$oMailBox = ProcessingHelper::GetMailBox();
		$oEmail = ProcessingHelper::GetMail();
		$oTicket = ProcessingHelper::GetTicket();
		
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
						ProcessingHelper::HandleError('nothing_to_update');
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
		
			switch($e->getMessage()) {
				// Match descriptions in CreateTicketFromEmail(), UpdateTicketFromEmail()
				case 'Ticket creation failed':
				case 'Ticket update failed':
					
					// Stop further processing (will delete or mark as error, based on user's settings)
					return;
					
			}
			
		}
		
		
		// No error occurred. Revert to default (continue processing).
		$oMailBox = ProcessingHelper::GetMailBox();
		ProcessingHelper::SetNextAction(eNextAction::PROCESS_MESSAGE);
		
		
	}
	
	/**
	 * Creates a new Ticket from an email.
	 *
	 * @return void
	 *
	 * @throws \Exception
	 */
	public static function CreateTicketFromEmail() {
		
		$oEmail = ProcessingHelper::GetMail();
		$oMailBox = ProcessingHelper::GetMailBox();
		
		$oCaller = $oEmail->GetSender();

		// In case of error (exception...) set the behavior
		// Upon success, this will be overruled again.
		if($oMailBox->Get('error_behavior') == PolicyBehavior::DELETE->value) {
			
			 // Remove the message from the mailbox
			ProcessingHelper::SetNextAction(eNextAction::DELETE_MESSAGE);
			
		}
		elseif($oMailBox->Get('error_behavior') == PolicyBehavior::MARK_AS_ERROR->value) {
			
			 // Keep the message in the mailbox, but marked as error
			ProcessingHelper::SetNextAction(eNextAction::MARK_MESSAGE_AS_ERROR);
			
		}
		
		static::Trace(". Creating a new Ticket from email '{$oEmail->sSubject}'");
		$sTargetClass = $oMailBox->Get('target_class');
			
		if(MetaModel::IsValidClass($sTargetClass) == false) {
			
			$sErrorMessage = "... Invalid 'target_class' configured: {$sTargetClass} is not a valid class. Cannot create such an object.";
			static::Trace($sErrorMessage);
			throw new Exception($sErrorMessage);
			
		}
		
		if($oCaller === null) {
			
			$sErrorMessage = "... Invalid caller specified: Cannot create Ticket without valid Person.";
			static::Trace($sErrorMessage);
			throw new Exception($sErrorMessage);
			
		}
		
		/** @var Ticket $oTicket */
		$oTicket = MetaModel::NewObject($sTargetClass);
		ProcessingHelper::SetTicket($oTicket);
		
		// If the Ticket class contains an org_id, it should inherit this from the user.
		$oTicket->Set('org_id', $oCaller->Get('org_id'));
		if(MetaModel::IsValidAttCode($sTargetClass, 'caller_id')) {
			$oTicket->Set('caller_id', $oCaller->GetKey());
		}
		if(MetaModel::IsValidAttCode($sTargetClass, 'origin')) {
			$oTicket->Set('origin', 'mail');
		}
		
		// Max length for title
		$oTicketTitleAttDef = MetaModel::GetAttributeDef($sTargetClass, 'title');
		$iTitleMaxSize = $oTicketTitleAttDef->GetMaxSize();
		$sSubject = $oEmail->sSubject;
		$oTicket->Set('title', substr($sSubject, 0, $iTitleMaxSize));
		
		// Insert the remaining attachments so that their ID is known and the attachments can be referenced in the message's body.
		// Cannot insert them for real since the Ticket is not saved yet (so Ticket id is unknown).
		// UpdateAttachments() will be called once the ticket is properly saved.
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
		
		static::Trace("Email body format: ".$oEmail->sBodyFormat);
		static::Trace("Target format for 'description': ".($bForPlainText ? 'text/plain' : 'text/html'));
		
		$sTicketDescription = static::BuildDescription($bForPlainText);

		$iDescriptionMaxSize = $oTicketDescriptionAttDef->GetMaxSize(); // Keep some room just in case...
		
		if(mb_strlen($sTicketDescription) > $iDescriptionMaxSize) {
			
			$sMsg = "CreateTicketFromEmail: Truncated description for [{$oTicket->Get('title')}] actual length: ".mb_strlen($sTicketDescription)." maximum: $iDescriptionMaxSize";
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
		
		// Default values.
		$sDefaultValues = $oMailBox->Get('ticket_default_values');
		$aDefaults = preg_split(static::NEWLINE_REGEX, $sDefaultValues);
		$aDefaultValues = [];
		foreach($aDefaults as $sLine) {
			if (preg_match('/^([^:]+):(.*)$/', $sLine, $aMatches)) {
				$sAttCode = trim($aMatches[1]);
				$sValue = trim($aMatches[2]);
				$sValue = static::ReplaceMailPlaceholders($sValue);
				$aDefaultValues[$sAttCode] = $sValue;
			}
		}
		
		ProcessingHelper::InitObjectFromDefaultValues($oTicket, $aDefaultValues);
		
		static::AddAdditionalContacts();
		
		static::BeforeInsertTicket();
		
		try {
			$oTicket->DBInsert();
			static::Trace(". Ticket ".$oTicket->GetName()." created.");
		}
		catch(Exception $e) {
			// Known issues:
			// - Incorrect related contacts
			// - E-mail notifications (?)
			static::Trace("Ticket {$oTicket->GetName()} could not be created.");
			static::Trace($e->getMessage()); // Add actual error message (if available)
			throw new Exception('Ticket creation failed');
		}
			
		static::AfterInsertTicket();
		
	}
	
	/**
	 * Updates an existing Ticket from an email
	 *
	 * @return void
	 *
	 * @throws \Exception
	 */
	public static function UpdateTicketFromEmail() {
		
		$oMailBox = ProcessingHelper::GetMailBox();
		$oEmail = ProcessingHelper::GetMail();
		$oTicket = ProcessingHelper::GetTicket();
		$oCaller = $oEmail->GetSender();
		
		// In case of error (exception...) set the behavior
		// Upon success, this will be overruled again.
		if($oMailBox->Get('error_behavior') == PolicyBehavior::DELETE->value) {
			ProcessingHelper::SetNextAction(eNextAction::DELETE_MESSAGE); // Remove the message from the mailbox
		}
		elseif($oMailBox->Get('error_behavior') == PolicyBehavior::MARK_AS_ERROR->value) {
			ProcessingHelper::SetNextAction(eNextAction::MARK_MESSAGE_AS_ERROR); // Keep the message in the mailbox, but marked as error
		}
		
		// Check that the ticket is of the expected class
		$sTargetClass = $oMailBox->Get('target_class');
		if(is_a($oTicket, $sTargetClass) == false) {
			
			$sClass = get_class($oTicket);
			static::Trace("Error: the incoming email refers to the ticket '{$oTicket->GetName()}' of class '{$sClass}', but this mailbox is configured to process only tickets of class '{$sTargetClass}'");
			ProcessingHelper::SetNextAction(eNextAction::MARK_MESSAGE_AS_ERROR); // Keep the message in the mailbox, but marked as error
			return;
			
		}
		
		// Try to extract what's new from the message's body
		static::Trace("Updating Ticket '{$oTicket->GetName()}' from email '{$oEmail->sSubject}'");
		
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

		// Process attachments.
		static::AddAttachments(true);
		
		$sCaseLogEntry = static::BuildCaseLogEntry();
		
		if($oEmail->sTrace == '') {
			static::Trace("No email trace available.");
		}
		else {
			static::Trace("Email trace: ".$oEmail->sTrace);
		}
		
		// Write the log on behalf of the caller.
		// Fallback to e-mail address if name is unknown.
		$sCallerName = $oEmail->sCallerName;
		$iCallerUserId = null;
		if($oCaller === null) {
			$sCallerName = $oEmail->sCallerEmail;
			// $iCallerUserId remains null
		}
		else {
			
			$sCallerName = $oCaller->GetName(); // Derive name from Person

			// Only first user will be matched. Multiple accounts could theoretically be linked to 1 Person.
			$oUser = UserRights::GetUserFromPerson($oCaller, false);
			
			if($oUser !== null) {
				$iCallerUserId = $oUser->GetKey();
			}
			
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
		if($bInsertChangeUserId){
			/** @var User $oUser */
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
			static::Trace("Ticket '{$oTicket->GetName()}' has been updated.");
		}
		catch(Exception $e) {
			static::Trace("Ticket {$oTicket->GetName()} might not be properly updated or something else went wrong (for instance: notifications).");
			static::Trace($e->getMessage()); // Add actual error message (if available)
			throw new Exception('Ticket update failed');
		}
		
		static::AfterUpdateTicket();

		// Restore previous change as current change
		if($bInsertChangeUserId){
			CMDBObject::SetTrackUserId($iCurrentUserId);
			/** @var CMDBChange $oCMDBChange */
			CMDBObject::SetCurrentChange($oCMDBChange);
		}
		
	}
	
	/**
	 * Called right before a Ticket is created.
	 *
	 * @return void
	 */
	public static function BeforeInsertTicket() {
		 
		// Do nothing
	
	}
	
	/**
	 * Called right after a Ticket is created.
	 *
	 * @return void
	 */
	public static function AfterInsertTicket() {
		 
		// Process attachments now the ID is known
		static::UpdateAttachments();
	
	}
	 
	/**
	 * Returns a description for a new Ticket.
	 *
	 * @param Boolean $bForPlainText True if the desired output format is plain text, false if HTML
	 * @return String Ticket description
	 */
	public static function BuildDescription($bForPlainText) {
		
		$sTicketDescription = '';
		$oEmail = ProcessingHelper::GetMail();
		
		if($oEmail->sBodyFormat == 'text/html') {
			// Original message is in HTML
			static::Trace("Managing inline images...");
			$sTicketDescription = static::ManageInlineImages($oEmail->sBodyText, $bForPlainText);
			if($bForPlainText == true) {
				static::Trace("Converting HTML to text using utils::HtmlToText...");
				$sTicketDescription = utils::HtmlToText($oEmail->sBodyText);
			}
		}
		else {
			// Original message is in plain text
			$sTicketDescription = utils::TextToHtml($oEmail->sBodyText);
			if($bForPlainText == false) {
				static::Trace("Converting text to HTML using utils::TextToHtml...");
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
	 *
	 * @param String $sBodyText Body text
	 * @param Boolean $bForPlainText Plain text (true) or HTML (false)
	 * 
	 * @return String Body text
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
					static::Trace("... Info: inline image: {$sCID} not found as an attachment. Ignored.");
				}
				elseif(array_key_exists($sCID, static::$aAddedAttachments)) {
					$aInlineImages[$idx]['cid'] = $sCID;
					static::Trace("... Inline image cid:$sCID stored as ".get_class(static::$aAddedAttachments[$sCID])."::".static::$aAddedAttachments[$sCID]->GetKey());
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
			static::Trace("Inline Images: no inline-image found in the message");
		}
		return $sBodyText;
		
	}
	 
	/**
	 * Adds additional contacts in the email as related contacts in the Ticket.
	 *
	 * @return void
	 */
	public static function AddAdditionalContacts() {
		
		$oTicket = ProcessingHelper::GetTicket();
		$oEmail = ProcessingHelper::GetMail();
		
		$sTargetClass = get_class($oTicket);
		if(MetaModel::IsValidAttCode($sTargetClass, 'contacts_list') == false) {
			
			return;
			
		}
		
		$oContactsSet = $oTicket->Get('contacts_list');
		$aExistingContacts = [];
		
		// Check which contacts were previously linked to the ticket.
		while($oLnk = $oContactsSet->Fetch()) {
			$aExistingContacts[] = $oLnk->Get('contact_id');
		}

		foreach($oEmail->GetRelatedContacts() as $oContact) {
			
			$sContactName = $oContact->GetName();
				
			if(MetaModel::IsValidAttCode($sTargetClass, 'caller_id') == true && $oContact->GetKey() == $oTicket->Get('caller_id')) {

				static::Trace("... Skipping '{$sContactName}' (ID {$oContact->GetKey()}) as additional contact since it is the caller.");

			}
			elseif(in_array($oContact->GetKey(), $aExistingContacts) == true) {
				
				static::Trace("... Skipping '{$sContactName}' (ID {$oContact->GetKey()}) as additional contact since the person is already linked to the ticket.");
				
			}
			else {
				
				$oLnk = new lnkContactToTicket();
				$oLnk->Set('contact_id', $oContact->GetKey());
				$oContactsSet->AddItem($oLnk);
				
			}
			
		}
		$oTicket->Set('contacts_list', $oContactsSet);
		
	}
	
	/**
	 *
	 * @return void
	 */
	public static function BeforeUpdateTicket() {
		
		// Do nothing
		
	}
	
	/**
	 * Hook to run function after a Tickt has been updated.
	 * In this case, it activates the trigger "TriggerOnMailUpdate"
	 *
	 * @return void
	 */
	public static function AfterUpdateTicket() {
		
		$oTicket = ProcessingHelper::GetTicket();
		
		// If there are any TriggerOnMailUpdate defined, let's activate them
		$aClasses = MetaModel::EnumParentClasses(get_class($oTicket), ENUM_PARENT_CLASSES_ALL);
		$sClassList = implode(', ', CMDBSource::Quote($aClasses));
		$oSet_TriggerMailUpdate = new DBObjectSet(DBObjectSearch::FromOQL_AllData("SELECT TriggerOnMailUpdate AS t WHERE t.target_class IN ($sClassList)"));

		$aContext = [
			'this->object()' => $oTicket,
			'sender->object()' => ProcessingHelper::GetMail()->GetSender(),
        ];

		/** @var Trigger $oTrigger iTop Trigger. */
		while($oTrigger = $oSet_TriggerMailUpdate->Fetch()) {
			$oTrigger->DoActivate($aContext);
		}

		// Apply a stimulus if needed, will write the ticket to the database, may launch triggers, etc...
		static::ApplyConfiguredStimulus();
		
		// No error occurred
		ProcessingHelper::SetNextAction(eNextAction::PROCESS_MESSAGE);
		
	}
	
	/**
	 * Read the configuration in the 'stimuli' field (format: <state_code>:<stimulus_code>, one per line)
	 * and apply the corresponding stimulus according to the current state of the ticket
	 *
	 *
	 * @return void
	 */
	public static function ApplyConfiguredStimulus() {
		
		$oMailBox = ProcessingHelper::GetMailBox();
		$oTicket = ProcessingHelper::GetTicket();
		$sConf = $oMailBox->Get('stimuli');
		
		// In Combodo's version, this resulted in a warning?
		// Reopen ticket elsewhere if needed.
		if(trim($sConf) == '') {
			return;
		}
	
		$aConf = preg_split(static::NEWLINE_REGEX, $sConf);
		$aStateToStimulus = [];
		foreach($aConf as $sLine) {
			if (preg_match('/^([^:]+):(.*)$/', $sLine, $aMatches))
			{
				$sState = trim($aMatches[1]);
				$sStimulus = trim($aMatches[2]);
				$aStateToStimulus[$sState] = $sStimulus;
			}
			elseif(empty($sLine) == false) {
				static::Trace("Invalid line in the 'stimuli' configuration: '{$sLine}'. The expected format for each line is <state_code>:<stimulus_code>");
			}
		}
		if (array_key_exists($oTicket->GetState(), $aStateToStimulus))
		{
			$sStimulusCode = $aStateToStimulus[$oTicket->GetState()];
			static::Trace("About to apply the stimulus: ".$sStimulusCode." for the ticket in state: ".$oTicket->GetState());
			
			// Check that applying the stimulus will not break the data integrity constaints (mandatory, must change)
			$aTransitions = $oTicket->EnumTransitions();
			$bCanApplyStimulus = true;
			if (!isset($aTransitions[$sStimulusCode])) {
				$bCanApplyStimulus = false;
				static::Trace("The Stimulus {$sStimulusCode} for ".get_class($oTicket)." in state {$oTicket->GetState()} has no effect (no transition). Ignored.");
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
								static::Trace("Setting the mandatory External Key {$sAttCode} to the only allowed value: {$oRemoteObj->GetKey()}");
							}
							else {
								static::Trace("Cannot apply the stimulus since the attribute {$sAttCode} is mandatory in the target state {$sTargetState} and is neither currently set nor has just one allowed value.");
								$bCanApplyStimulus = false;
							}
						}
						else {
							$aAllowedValues = MetaModel::GetAllowedValues_att(get_class($oTicket), $sAttCode, $aArgs) ?? [];
							if (count($aAllowedValues) == 1) {
								$aValues = array_keys($aAllowedValues);
								$oTicket->Set($sAttCode, $aValues[0]);
								static::Trace("Setting the mandatory attribute {$sAttCode} to the only allowed value: ".(string)$aValues[0]);
							}
							else {
								static::Trace("Cannot apply the stimulus since the attribute {$sAttCode} is mandatory in the target state {$sTargetState} and is neither currently set nor has just one allowed value.");
								$bCanApplyStimulus = false;
							}
						}
					}
					if ($iExpectCode & OPT_ATT_MUSTCHANGE) {
						static::Trace("Cannot apply the stimulus since the value of the attribute {$sAttCode} must be modified (manually) during the transition to the target state {$sTargetState}.");
						$bCanApplyStimulus = false;
					}
				}
			}
			
			if($bCanApplyStimulus == true) {
				try {
					static::Trace("Actually applying the stimulus: {$sStimulusCode} for the ticket in state: {$oTicket->GetState()}");
					$oTicket->ApplyStimulus($sStimulusCode);
				}
				catch(Exception $e) {
					static::Trace("ApplyStimulus failed: {$e->getMessage()}");
				}
			}
			else {
				static::Trace("ApplyStimulus ignored.");
			}
		}
		
	}
	
	/**
	 * Build the text/html to be inserted in the case log when the ticket is updated
	 * Starting with iTop 2.3.0, the format is always HTML
	 *
	 * @return String The HTML text to be inserted in the case log
	 */
	 public static function BuildCaseLogEntry() {
		 
		$oEmail = ProcessingHelper::GetMail();
		$sCaseLogEntry = '';
		
		static::Trace("Email body format: ".$oEmail->sBodyFormat);
		if ($oEmail->sBodyFormat == 'text/html') {
			static::Trace("Extracting the new part using GetNewPartHTML()...");
			$sCaseLogEntry = $oEmail->GetNewPartHTML($oEmail->sBodyText);
			if (strip_tags($sCaseLogEntry) == '')
			{
				// No new part (only blank tags)...
				// It's better use the whole text of the message
				$sCaseLogEntry = $oEmail->sBodyText;
			}
			static::Trace("Managing inline images...");
			$sCaseLogEntry = static::ManageInlineImages($sCaseLogEntry, false /* $bForPlainText */);
		}
		else {
			static::Trace("Extracting the new part using GetNewPart()...");
			$sCaseLogEntry = $oEmail->GetNewPart($oEmail->sBodyText, $oEmail->sBodyFormat); // GetNewPart always returns a plain text version of the message
			$sCaseLogEntry = utils::TextToHtml($sCaseLogEntry);
		}
		
		return $sCaseLogEntry;
		
	 }
	 
	/**
	 * @param Boolean $bNoDuplicates If true, don't add attachment that seem already attached to the ticket (same type, same name, same size, same md5 checksum).
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
	 */
	public static function AddAttachments($bNoDuplicates = true) {
		
		$oEmail = ProcessingHelper::GetMail();
		$oTicket = ProcessingHelper::GetTicket();
		
		// Process attachments (if any)
		$aPreviousAttachments = []; // Attachments already linked to this Ticket
		
		// Build a list of Attachments already present in this ticket.
		// This includes both Attachment and InlineImage classes.
		if($bNoDuplicates ) {
			
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
		
		$oCaller = $oEmail->GetSender();
		$oUser = null;
		if(method_exists('UserRights', 'GetUserFromPerson') && $oCaller !== null) {
			
			$oUser = UserRights::GetUserFromPerson($oCaller, true);
			
		}
		
		foreach($oEmail->aAttachments as $iIndex => $aAttachment) {
			
			$bIgnoreAttachment = false;
			
			if($bIgnoreAttachment == false && $bNoDuplicates) {
				
				// Check if an attachment with the same name/type/size/md5 already exists
				$iSize = strlen($aAttachment['content']);
				$sMd5 = md5($aAttachment['content']);
				foreach($aPreviousAttachments as $aPrevious) {
					if (
						($aAttachment['filename'] === $aPrevious['filename']) &&
						($aAttachment['mimeType'] === $aPrevious['mimeType']) &&
						($iSize == $aPrevious['size']) &&
						($sMd5 == $aPrevious['md5']) )
					{
						// Skip this attachment
						static::Trace("Attachment {$aAttachment['filename']} skipped, already attached to the ticket.");
						static::$aAddedAttachments[$aAttachment['content-id']] = $aPrevious['object']; // Still remember it for processing inline images
						$bIgnoreAttachment = true;
						break;
					}
				}
				
				if($bIgnoreAttachment == false) {
					
					if(static::IsImage($aAttachment['mimeType']) && class_exists('InlineImage') && $aAttachment['inline']) {
						$oAttachment = new InlineImage();
						static::Trace("Attachment {$aAttachment['filename']} will be stored as an InlineImage.");
						$oAttachment->Set('secret', bin2hex(random_bytes(16))); // 128 bits of entropy, cryptographically secure
					}
					else {
						static::Trace("Attachment {$aAttachment['filename']} will be stored as an Attachment.");
						$oAttachment = new Attachment();
						
						$oAttachment->Set('creation_date', date('Y-m-d H:i:s'));
						$oAttachment->SetIfNull('creation_date', time());

						if($oUser !== null) {
							$oAttachment->Set('user_id', $oUser);
						}
						// Difference between iTop 3.0 (AttributeExternalField) and iTop 3.1 (AttributeExternalKey).
						if(MetaModel::GetAttributeDef('Attachment', 'contact_id') instanceof AttributeExternalKey && $oCaller !== null) {
							$oAttachment->Set('contact_id', $oCaller);
						}
						
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
					static::Trace("Attachment {$aAttachment['filename']} added to the ticket.");
					static::$aAddedAttachments[$aAttachment['content-id']] = $oAttachment;
				}
			}
			else {
				static::Trace("The attachment #{$iIndex} {$aAttachment['filename']} was NOT added to the ticket because its type '{$aAttachment['mimeType']}' is excluded according to the configuration");
			}
		}
		
		$iCount = count(static::$aAddedAttachments);
		static::Trace(". Added {$iCount} attachments".($iCount > 0 ? " ".implode(', ', array_keys(static::$aAddedAttachments)) : ""));
		
	 }
	 
	/**
	 * Links a collection of attachments to a newly created ticket.
	 *
	 * @return void
	 */
	public static function UpdateAttachments() : void {
		
		$iNumAttachments = count(static::$aAddedAttachments);
		if($iNumAttachments > 0) {
			static::Trace("Linking {$iNumAttachments} attachments...");
		}
			
		foreach(static::$aAddedAttachments as $oAttachment) {
			$oTicket = ProcessingHelper::GetTicket();
			$oAttachment->SetItem($oTicket);
			$oAttachment->DBUpdate();
		}
		
	}

	
	/**
	 * Checks whether a MimeType is an image which can be processed by iTop (PHP GD)
	 *
	 * @param string $sMimeType
	 *
	 * @return array|bool
	 */
	public static function IsImage(string $sMimeType) : array|bool {
				
		if(function_exists('gd_info') == false) {
			return false; // no image processing capability on this system
		}
		
		$aInfo = gd_info(); // What are the capabilities

		return match($sMimeType) {
			'image/gif' => $aInfo['GIF Read Support'],
			'image/jpeg' => $aInfo['JPEG Support'],
			'image/png' => $aInfo['PNG Support'],
			default => false,
		};

	}

	
	/**
	 * Truncates the text, if needed, to fit into the given the maximum length and:
	 * 1) Takes care of replacing line endings by \r\n since the browser produces this kind of line endings inside a TEXTAREA
	 * 2) Trims the result to emulate the behavior of iTop's inputs
	 *
	 * @param String $sInputText
	 * @param Int $iMaxLength
	 *
	 * @return String The fitted text
	 */
	public static function FitTextIn($sInputText, $iMaxLength) {
		
		$sInputText = trim($sInputText);
		$sInputText = str_replace("\r\n", "\r", $sInputText);
		$sInputText = str_replace("\n", "\r", $sInputText);
		$sInputText = str_replace("\r", "\r\n", $sInputText);
		if(mb_strlen($sInputText) > $iMaxLength) {
			$sInputText = trim(mb_substr($sInputText, 0, $iMaxLength - 3)).'...';
		}
		return $sInputText;
		
	}
	 
}

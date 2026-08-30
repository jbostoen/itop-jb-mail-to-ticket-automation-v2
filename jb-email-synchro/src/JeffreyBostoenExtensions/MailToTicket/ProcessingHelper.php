<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260711
 */

namespace JeffreyBostoenExtensions\MailToTicket;

use JeffreyBostoenExtensions\MailToTicket\Steps\Base as BaseStep;

// iTop internals.
use AttributeExternalKey;
use AttributeEnum;
use CMDBSource;
use DBObject;
use DBObjectSet;
use DBObjectSearch;
use DBSearch;
use Email;
use Exception;

// iTop email processing.
use EmailMessage;
use EmailReplica;
use EmailSource;
use MailInboxStandard;
use MessageFromMailbox;
use MetaModel;
use RawEmailMessage;

// iTop classes.
use Ticket;

/**
 * Enum eNextAction. An enum to define the possible next actions after processing an e-mail message.
 */
enum eNextAction {
	case PROCESS_MESSAGE;
	case DELETE_MESSAGE;
	case MOVE_MESSAGE;
	case MARK_MESSAGE_AS_UNDESIRED;
	case MARK_MESSAGE_AS_ERROR;
	case NO_ACTION;
	case ABORT_ALL_FURTHER_PROCESSING;
	case SKIP_FOR_NOW;

	/** Currently unused! */
	case PROCESS_ERROR;

}

/**
 * Class ProcessingHelper. A helper class to share info between various methods,  
 * and to keep track of a mailbox, e-mail message, ticket, etc. that are being processed.
 * 
 * Note: Currently only used for the background process!
 */
abstract class ProcessingHelper {

	/**
	 * @var string[] $aPreviouslyExecutedSteps Array of steps (class names) which have been executed already.
	 */
	private static array $aPreviouslyExecutedSteps = [];
	
	/**
	 * @var EmailMessage $oEmail E-mail message.
	 */
	private static $oEmail = null;
	
	/**
	 * @var MailInboxStandard $oMailBox Mailbox.
	 */
	private static $oMailBox = null;
	
	/**
	 * @var EmailSource $oSource E-mail source (e.g. IMAPEmailSource).
	 */
	private static $oSource = null;
	
	/**
	 * @var Ticket $oTicket Ticket object (in iTop).
	 */
	private static $oTicket = null;
	
	/**
	 * @var MessageHandler $oMessageHandler A handler on the e-mail while processing.
	 */
	private static MessageHandler $oMessageHandler;


	/**
	 * @var int $iMaxAllowedPacketSize The maximum allowed packet size.
	 */
	private static int $iMaxAllowedPacketSize;

	/**
	 * @var eNextAction $eNextAction The next action for the mail that is being processed.
	 */
	private static eNextAction $eNextAction;

	/**
	 * @var string[] $aAvailableStepClasses Array of class names of the available steps.
	 */
	private static array $aAvailableStepClasses;


	/**
	 * Trace function used for debugging.
	 *
	 * @param string $sMessage The message.
	 * @param mixed ...$args
	 *
	 * @return void
	 */
	public static function Trace($sMessage, ...$args) : void {

		static::TraceChannel(Logger::CHANNEL_DEFAULT, $sMessage, ...$args);

	}

	/**
	 * Trace function used for debugging, tagging the message with a specific log channel.
	 * iTop's log_level_min config can be set per channel, so this allows an admin to tune the
	 * verbosity of a specific area (e.g. 'cron') independently of the rest, without any code change.
	 *
	 * @param string $sChannel The log channel to tag this message with.
	 * @param string $sMessage The message.
	 * @param mixed ...$args
	 *
	 * @return void
	 */
	public static function TraceChannel(string $sChannel, $sMessage, ...$args) : void {

		$sMessage = (count($args) > 0) ? sprintf($sMessage, ...$args) : $sMessage;

		Logger::Trace($sMessage, $sChannel);

	}

	/**
	 * Trace function used for debugging during a cron run.
	 * Echoes the message when the module's 'debug' setting is enabled (matching the existing
	 * cron-visible tracing behavior), and always logs it to the 'cron' channel.
	 *
	 * @param string $sMessage The message.
	 * @param mixed ...$args
	 *
	 * @return void
	 */
	public static function TraceCron($sMessage, ...$args) : void {

		$sMessage = (count($args) > 0) ? sprintf($sMessage, ...$args) : $sMessage;

		if(MetaModel::GetModuleSetting('jb-email-synchro', 'debug', false)) {
			echo $sMessage."\n";
		}

		static::TraceChannel('cron', $sMessage);

	}

	/**
	 * Gets the mailbox.
	 *
	 * @return MailInboxStandard
	 */
	public static function GetMailBox() : MailInboxStandard {
		
		return static::$oMailBox;
		
	}
	
	/**
	 * Sets the mailbox that's being processed.
	 *
	 * @param MailInboxStandard $oMailBox Mailbox
	 *
	 * @return void
	 */
	public static function SetMailBox(MailInboxStandard $oMailBox) : void {
		
		static::$oMailBox = $oMailBox;
		
	}

	
	/**
	 * Sets the e-mail that's being processed.
	 *
	 * @param EmailMessage $oEmail E-mail message.
	 *
	 * @return void
	 */
	public static function SetMail(EmailMessage $oEmail) : void {
		
		static::$oEmail = $oEmail;
		
	}
	
	
	/**
	 * Gets the e-mail that's being processed.
	 *
	 * @return EmailMessage
	 */
	public static function GetMail() : EmailMessage {
		
		return static::$oEmail;
		
	}
	

	/**
	 * Gets the raw e-mail that's being processed.
	 *
	 * @return RawEmailMessage
	 */
	public static function GetRawMail() : RawEmailMessage {
		
		return static::$oEmail->oRawEmail;
		
	}
	

	/**
	 * Gets the e-mail source.
	 *
	 * @return EmailSource The e-mail source.
	 */
	public static function GetMailSource() : EmailSource {
		
		return static::$oSource;
		
	}
	
	/**
	 * Sets the e-mail source.
	 *
	 * @param EmailSource $oSource E-mail source
	 *
	 * @return void
	 */
	public static function SetMailSource(EmailSource $oSource) : void {
		
		static::$oSource = $oSource;
		
	}
	

	/**
	 * Gets mail process data (indexes, UIDL, etc.) of the e-mail message that is being processed.
	 * 
	 * Note: This is an experimental method.
	 *
	 * @return MessageHandler
	 */
	public static function GetMessageHandler() : MessageHandler {
		
		return static::$oMessageHandler;
		
	}
	
	/**
	 * Sets index of e-mail message.
	 *
	 * @param MessageHandler $oHandler
	 * 
	 * @return void
	 */
	public static function SetMessageHandler(MessageHandler $oHandler) : void {
		
		static::$oMessageHandler = $oHandler;
		
	}
	
	/**
	 * Gets ticket.
	 *
	 * @return Ticket|null Ticket object if found/created; null otherwise.
	 */
	public static function GetTicket() : ?Ticket {
		
		return static::$oTicket;
		
	}
	
	/**
	 * Sets ticket.
	 *
	 * @param Ticket|null $oTicket Ticket
	 *
	 * @return void
	 */
	public static function SetTicket(?Ticket $oTicket) : void {
		
		static::$oTicket = $oTicket;
		
	}
	
	/**
	 * Gets executed steps.
	 *
	 * @return string[]
	 */
	public static function GetExecutedSteps() : array {
		
		return static::$aPreviouslyExecutedSteps;
		
	}
	
	/**
	 * Sets executed steps.
	 *
	 * @param string[] $aPreviouslyExecutedSteps Class names of previously executed steps.
	 *
	 * @return void
	 */
	public static function SetExecutedSteps(array $aPreviouslyExecutedSteps) : void {
		
		static::$aPreviouslyExecutedSteps = $aPreviouslyExecutedSteps;
		
	}


	/**
	 * Returns the max allowed packet size.
	 * 
	 * Notes:
	 * 
	 * - The value is cached, for the entire run of the cron execution.
	 * 
	 * - When calling this method; keep in mind that this is the absolute maximum.  
	 *   Most likely, some margin might be needed for the rest of the SQL query, so it might be better to use a slightly lower value.
	 * 
	 * @return int
	 */
	public static function GetMaxAllowedPacketSize() : int {

		if(!isset(static::$iMaxAllowedPacketSize)) {

			$aData = CMDBSource::QueryToArray('SELECT @@global.max_allowed_packet');
			static::$iMaxAllowedPacketSize = (int)$aData[0]['@@global.max_allowed_packet'];

		}

		return static::$iMaxAllowedPacketSize;

	}


	/**
	 * Sets the next action.
	 *
	 * @param eNextAction $eAction
	 * @return void
	 */
	public static function SetNextAction(eNextAction $eAction) : void {

		static::$eNextAction = $eAction;

	}

	/**
	 * Gets the next action.
	 *
	 * @return eNextAction
	 */
	public static function GetNextAction() : eNextAction {

		return static::$eNextAction;

	}
	
		
	/**
	 * Returns a list of class names of the available steps.
	 *
	 * @return string[]
	 */
	public static function GetAvailableSteps() : array {

		if(!isset(static::$aAvailableStepClasses)) {
				
			static::$aAvailableStepClasses = [];

			foreach(get_declared_classes() as $sClassName) {
			
				if(is_subclass_of($sClassName, BaseStep::class) == true) {
					
					static::$aAvailableStepClasses[] = $sClassName;
					
				}
				
			}

		}

		return static::$aAvailableStepClasses;

	}
	

	/**
	 * Determines the steps to process for a mailbox, based on the current configuration of this helper.
	 * 
	 * @return string[]
	 */
	public static function GetApplicableSteps() : array {

		// @todo Add some caching.

		$oInbox = static::GetMailBox();
		
		$callable = function(string $sClassName) use ($oInbox) : bool {

			// - The step must be applicable.
				
				if($sClassName::IsApplicable()) {
						
					// Policies can easily be turned off.
					// 'inactive' means it can be skipped altogether.
					// 'do_nothing' means it should still be processed, but it shouldn't have any influence nor take any.
					$sAttCode = $sClassName::GetXMLSettingsPrefix().'_behavior';

					// - If a step has no behavior, it's a step that always will be executed.
					// - Else, if a step has a behavior and it's not set to "inactive", it means it's a policy that must be executed.

						if(!MetaModel::IsValidAttCode(get_class($oInbox), $sAttCode)) {

							return true;
							
						}
						elseif($oInbox->Get($sAttCode) != 'inactive') {
						
							return true;
							
						}

				}

			return false;

		};

		return array_filter(static::GetAvailableSteps(), $callable);

	}



	/**
	 * Initial dispatching of an e-mail. (Determination on what to do).
	 *
	 * @param EmailReplica $oEmailReplica
	 * @return eNextAction
	 */
	public static function DispatchEmail(EmailReplica $oEmailReplica) : eNextAction {

		$oInbox = static::GetMailBox();
		
		// - Safety.
		static::SetNextAction(eNextAction::NO_ACTION);

		// New messages will be processed.
		// Already processed messages:
		// - If the related ticket is closed, the e-mail could be deleted; or the ticket could be re-opened.
		// - If no ticket was created before or if it has been deleted, the message will also be deleted (if previously 'ok' or 'undesired').

		if($oEmailReplica->IsNew()) {

			// New (unread) message, let's process it
			$oInbox->Trace('EmailReplica is new. Process message.');
			static::SetNextAction(eNextAction::PROCESS_MESSAGE);

		}
		else {

			$iTicketId = $oEmailReplica->Get('ticket_id');
			$oTicket = MetaModel::GetObject('Ticket', $iTicketId, false);

			if(is_object($oTicket) && $oTicket instanceof Ticket) { 

				// All good.
				// If this is a reply to a closed ticket, it will be handled by the TicketClosed step.

			}
			else {

				// - The corresponding ticket was deleted (or never created!), 
				//   delete the email (and the replica).
				$sReplicaStatus = $oEmailReplica->Get('status');

				if(in_array($sReplicaStatus, ['ok', 'undesired'])) {

					// - An EmailReplica might still be present even if its related ticket has been deleted.
					//   This EmailReplica may contain a reference to the UID of the message.
					$oInbox->Trace('EmailReplica is not new. No ticket found. Status: "%1$s". Delete message. It is labeled as OK or undesired; which means it was processed before.', $sReplicaStatus);
					static::SetNextAction(eNextAction::DELETE_MESSAGE);

				}
				else {

					$oInbox->Trace('EmailReplica is not new. No ticket found. Status: "%1$s". Do nothing - requires manual intervention.', $sReplicaStatus);
					static::SetNextAction(eNextAction::NO_ACTION);
					
				}

			}

		}

		return static::GetNextAction();

	}


	/**
	 * Process a new e-mail.
	 *
	 * @return ?Ticket
	 */
	public static function ProcessNewEmail() : ?Ticket {

		static::SetNextAction(eNextAction::PROCESS_MESSAGE);

		$oInbox = static::GetMailBox();
		$oInbox->sLastError = null;

		$oMsgHandler = static::GetMessageHandler();

		$oInbox->Trace('Processing new e-mail ( index = %1$s , Message-ID = %2$s , UID = %3$s )',
			$oMsgHandler->GetOriginalListingIndex(),
			$oMsgHandler->GetMessageId(),
			$oMsgHandler->GetUID(),
		);
		
		// - Check whether a new ticket must be initiated or an existing one updated.

			$oTicket = static::GetRelatedTicket();
			static::SetTicket($oTicket);
		
		// - If there was no related ticket above, a new one may be created while processing the steps.

			static::ProcessSteps();
	
			return static::GetTicket();


	}


	/**
	 * Executes the steps for the e-mail that's being processed, based on the current configuration of this helper.
	 *
	 * @return void
	 */
	public static function ProcessSteps() : void {

		$oInbox = static::GetMailBox();

		// - Calling it this late, means that the IsApplicable() method can already perform conditional checks on the e-mail or ticket.
		$aStepClasses = static::GetApplicableSteps();
		
		// - Steps must be executed in a certain order.
		//   Some steps may set info on the Ticket or derive the caller; other steps may block further processing.
		//   The order in which these actions are executed, is important.
			
			// Class name is used as a deterministic tiebreaker: without it, the relative order of
			// Steps sharing the same $iPrecedence would depend on get_declared_classes()/autoloader
			// order, which isn't guaranteed stable across environments or PHP versions.
			usort($aStepClasses, function($sClassNameA, $sClassNameB) {
				return [$sClassNameA::$iPrecedence, $sClassNameA] <=> [$sClassNameB::$iPrecedence, $sClassNameB];
			});
		
		// - This is extra info.
			$aPreviouslyExecutedSteps = [];
			static::SetExecutedSteps($aPreviouslyExecutedSteps);

		// - Default to the current next action, in case no step is applicable at all (empty $aStepClasses):
		//   the loop below would otherwise never assign $eNextAction, leaving the post-loop check unable
		//   to ever reset the next action to NO_ACTION, wedging the message into permanent reprocessing.
			$eNextAction = static::GetNextAction();

		foreach($aStepClasses as $sStep) {
		
			$sShortStep = basename($sStep);
			$sPolicyInfo = '';
			
			// - In case of policy: Add behavior in log entry.

				$sAttCode = $sStep::GetXMLSettingsPrefix().'_behavior';
				if(MetaModel::IsValidAttCode($oInbox::class, $sAttCode) == true) {
					$sPolicyInfo = ' - Behavior: '.$oInbox->Get($sAttCode);
				}
			

			$oInbox->Trace('Step "%1$s" (#%2$s), precedence: %3$s %4$s',
				$sShortStep,
				count($aPreviouslyExecutedSteps) + 1,
				$sStep::$iPrecedence,
				$sPolicyInfo,
			);
			
			$sStep::Execute();
			
			// - Keep track.
				$aPreviouslyExecutedSteps[] = $sStep;
				static::SetExecutedSteps($aPreviouslyExecutedSteps);
			
			$eNextAction = ProcessingHelper::GetNextAction();
			$oInbox->Trace('- Complete: "%1$s", next action = "%2$s".', $sShortStep, $eNextAction->name);
			
			// If a next action has already been set: stop further processing.
			// It either means one of the steps wants to take an action which makes further processing of this particular e-mail not necessary.
			// The action could be: delete, move, mark as error, mark as undesired, skip for now, abort all further processing, ...
			if($eNextAction != eNextAction::PROCESS_MESSAGE) {
				break;
			}
			
		
		}
		
		if($eNextAction === eNextAction::PROCESS_MESSAGE) {
		
			// - Everything went smoothly, but it's done now.
				ProcessingHelper::SetNextAction(eNextAction::NO_ACTION);
			
		}
		
		$oInbox->Trace('= Processed all steps.');
		
	}


	/**
	 * Gets the related ticket.
	 *
	 * @return Ticket|null
	 */
	public static function GetRelatedTicket() : ?Ticket {

		$oEmail = static::GetMail();
		$oInbox = static::GetMailBox();

		// - Check if there is any iTop object mentioned in the headers of the e-mail.

			$oTicket = $oEmail->oRelatedObject;

			// - There is a related object, but it's not a ticket.

			if(($oTicket != null) && !($oTicket instanceof Ticket)) {

				// The object referenced by the email is not a ticket.
				// => Forward the message and delete the ticket?
				$sObjClass = get_class($oTicket);
				$sObjId = $oTicket->GetKey();
				$oInbox->Trace("Warning: Message ({$oEmail->sUIDL}) contains a reference to a valid iTop object that is NOT a ticket! ({$sObjClass}::{$sObjId})");
				$oTicket = null;

			}

		// - If the related object was not valid:
		
		if($oTicket == null) {
			
			// - No associated ticket found by parsing the headers.
			//   Check if the subject matches a specific pattern

			// - To find the reference, make sure subject is handled in the same way as with the original ticket (removing unwanted pattern).

			$sSubject = $oEmail->sSubject;
			
			// Here it's possible to ignore title patterns.
			// The use case had to do with another service desk also using a similar pattern.
			// @todo Move this to a step.
		
			$sPatterns = $oInbox->Get('title_pattern_ignore_patterns');
			
			if(trim($sPatterns) != '') {
				
				$aPatterns = preg_split('/\r\n|\r|\n/', $sPatterns);
				
				$oInbox->Trace("Ignoring patterns: {$sPatterns}");
				
				foreach($aPatterns as $sPattern) {
					if(trim($sPattern) != '') {
						$oPregMatch = @preg_match($sPattern, $sSubject);
						
						if( $oPregMatch === false) {
							$oInbox->Trace("Invalid pattern: '{$sPattern}'");
						}
						elseif(preg_match($sPattern, $sSubject)) {
							$oInbox->Trace("Removing: '{$sPattern}'");
							$sSubject = preg_replace($sPattern, '', $sSubject);
						}
						else {
							// Just not matching
						}
					}
				}
			}
			
			$sPattern = $oInbox->Get('title_pattern');

			// - If there are matches: Find the first matching ticket. (This will depend on the sort order from the datamodel).

				$oPregMatched = ($sPattern != '') ? @preg_match($sPattern, $sSubject, $aMatches) : null;

				if($oPregMatched === false) {

					$oInbox->Trace("Invalid pattern: '{$sPattern}'");

				}
				elseif(($sPattern != '') && $oPregMatched) {

					$oFilter = DBSearch::FromOQL('SELECT Ticket WHERE ref = :ref');
					foreach($aMatches as $sMatch) {
						if(!empty($sMatch)) {
							$oInbox->Trace("Retrieving ticket $sMatch (match by subject pattern)...");
							$oSet = new DBObjectSet($oFilter, [], ['ref' => $sMatch]);
							$oTicket = $oSet->Fetch();
							if($oTicket) {
								break;
							}
						}
					}

				}

		}		
		
		return $oTicket;
	}

		
	/**
	 * Handles an actual processing error.
	 *
	 * @param string $sErrorCode
	 * @param string $sAdditionalErrorMessage Optional extra error message.
	 * @return void
	 */
	public static function HandleError(string $sErrorCode, string $sAdditionalErrorMessage = '') : void {

		$oInbox = static::GetMailBox();
		$oEmail = static::GetMail();

		/** @var MessageFromMailbox $oRawEmail This raw e-mail will be the specific subclass that supports sending the e-mail as attachment. */
		$oRawEmail = static::GetRawMail();

		// - notify_errors_to holds an OQL query returning Person objects. Resolve it into a comma-separated list of e-mail addresses.
		$sTo = '';
		$sNotifyErrorsToOQL = $oInbox->Get('notify_errors_to');
		if($sNotifyErrorsToOQL !== '') {

			try {

				$oPersonSet = new DBObjectSet(DBObjectSearch::FromOQL($sNotifyErrorsToOQL));
				$aRecipients = [];
				while($oPerson = $oPersonSet->Fetch()) {

					$sEmail = $oPerson->Get('email');
					if($sEmail !== '') {
						$aRecipients[] = $sEmail;
					}

				}
				$sTo = implode(',', $aRecipients);

			}
			catch(Exception $e) {

				$oInbox->Trace('HandleError: Invalid OQL in notify_errors_to (%1$s): %2$s', $sNotifyErrorsToOQL, $e->getMessage());

			}

		}
		$sFrom = $oInbox->Get('notify_from');
		
		// The behavior is overriden in case of undesired_message
		if($oInbox->Get('error_behavior') == 'delete') {
		
			static::SetNextAction(eNextAction::DELETE_MESSAGE); // Remove the message from the mailbox
			$sLastAction = "<p>The eMail was deleted from the mailbox.</p>\n";
		}
		else {
		
			static::SetNextAction(eNextAction::MARK_MESSAGE_AS_ERROR); // Keep the message in the mailbox, but marked as error
			$sLastAction = "<p>The eMail is marked as error and will be ignored in further processing of the mailbox.</p>\n";
			
		}
		
		switch($sErrorCode) {

			case 'decode_failed':
			
				$sSubject = '[iTop] Failed to decode an incoming e-mail (mail inbox: '.$oInbox->GetName().')';
				$sBody = '<p>The following eMail (see attachment) was not decoded properly and therefore was not processed at all.</p>';
				$sBody .= '<ul><li>'.implode('</li><li>', array_map('htmlspecialchars', $oEmail->GetInvalidReasons())).'</li></ul>';
				$sBody .= $sLastAction;
				break;


			case 'failed_to_create_contact':

				$sSubject = '[iTop] Failed to create a contact for the incoming e-mail - '.htmlspecialchars($oEmail->sSubject);
				$sBody = "<p>The following email (see attachment) comes from an unknown caller (".htmlspecialchars($oEmail->sCallerEmail).").<br/>\n";
				$sBody .= "The configuration of the Mail Inbox ".$oInbox->GetName()." instructs to create a new contact based on some default values, but this creation was not successful.<br/>\n";
				$sBody .= "Check the contact's default values configured in the Mail Inbox.</p>\n";
				$sBody .= $sLastAction;
				$oInbox->sLastError = "Failed to create a contact from the incoming e-mail. Caller = ".$oEmail->sCallerEmail;
				break;
			
			case 'rejected_attachments':
			
				$sSubject = '[iTop] Failed to process attachment(s) for the incoming e-mail - '.htmlspecialchars($oEmail->sSubject);
				$sBody = "<p>Some attachments to the eMail were not processed because they are too big:</p>\n";
				$oInbox->sLastError = $sAdditionalErrorMessage;
				$sBody .= "<pre>".$sAdditionalErrorMessage."</pre>\n";
				$sBody .= $sLastAction;
				
				$oRawEmail = null; // No original message in attachment
				$oInbox->Trace($sSubject."\n\n".$sBody);
				// Send the email now...
				if(($sTo != '') && ($sFrom != '')) {
					$oEmailToSend = new Email();
					$oEmailToSend->SetRecipientTO($sTo);
					$oEmailToSend->SetSubject($sSubject);
					$oEmailToSend->SetBody($sBody, 'text/html');
					$oEmailToSend->SetRecipientFrom($sFrom);
					$aIssues = array();
					$oEmailToSend->Send($aIssues, true /* bForceSynchronous */, null /* $oLog */);
				}
				break;

			case 'undesired_message':

				$sSubject = '[iTop] Undesired message - '.htmlspecialchars($oEmail->sSubject);
				$sBody = "<p>The attached message was rejected because it is considered as undesired, based on the 'undesired_subject_patterns' specified in the iTop configuration file.</p>\n";
				$sBody .= $sLastAction;

				// Send the email now...
				if(($sTo != '') && ($sFrom != '')) {
					$oEmailToSend = new Email();
					$oEmailToSend->SetRecipientTO($sTo);
					$oEmailToSend->SetSubject($sSubject);
					$oEmailToSend->SetBody($sBody, 'text/html');
					$oEmailToSend->SetRecipientFrom($sFrom);
					$aIssues = array();
					$oEmailToSend->Send($aIssues, true /* bForceSynchronous */, null /* $oLog */);
				}
				
				// Email has been undesired for multiple days.
				if(MetaModel::GetModuleSetting('jb-email-synchro', 'undesired_purge_delay', 7) == 0) {
					static::SetNextAction(eNextAction::DELETE_MESSAGE); // immediate delete when delay is 0
				}
				else {
					static::SetNextAction(eNextAction::MARK_MESSAGE_AS_UNDESIRED);
				}
				
				$oInbox->sLastError = 'Undesired email';
				$oRawEmail = null; // No feedback on undesired emails
				break;
				
			
			default:
				$sSubject = '[iTop] handle error';
				$sBody = '<p>Unexpected error: '.$sErrorCode."</p>\n";
				$sBody .= $sLastAction;
				$oInbox->sLastError = 'Unexpected error: '.$sErrorCode;
		}
		$sBody .= "<p>&nbsp;</p><p>Mail Inbox Configuration: ".$oInbox->GetHyperlink()."</p>\n";
		
		if(($sTo == '') || ($sFrom == '')) {
			$oInbox->Trace("HandleError: No forward configured for forwarding the email (To: '$sTo', From: '$sFrom'), skipping.");
		}
		elseif($oRawEmail) {

			$oInbox->Trace($sSubject."\n\n".$sBody);
			$oRawEmail->SendAsAttachment($sTo, $sFrom, $sSubject, $sBody);

		}

	}


	/*
	 * Initializes an object from default values.
	 * Each default value must be a valid value for the given field.
	 * 
	 * @param DBObject $oObj The object to update
	 * @param hash $aValues The values to set attcode => value
	 */
	public static function InitObjectFromDefaultValues(DBObject $oObj, array $aValues) : void {

		$oInbox = static::GetMailBox();

		// - Loop over the given values.
		foreach($aValues as $sAttCode => $value) {

			if(!MetaModel::IsValidAttCode(get_class($oObj), $sAttCode)) {

				$oInbox->Trace("Warning: cannot set default value '$value'; '$sAttCode' is not a valid attribute of the class ".get_class($oObj).".");
				continue;

			}
			$oAttDef = MetaModel::GetAttributeDef(get_class($oObj), $sAttCode);

			if(!$oAttDef->IsWritable()) 	{
				$oInbox->Trace("Warning: cannot set default value '$value' for the non-writable attribute: '$sAttCode'.");
				continue;
			}

		
			$aArgs = array('this' => $oObj->ToArgs());
			$aAllowedValues = $oAttDef->GetAllowedValues($aArgs);

			if($aAllowedValues === null) {

				// No special constraint for this attribute
				if($oAttDef->IsExternalKey()) {

					/** @var AttributeExternalKey $oAttDef */
					$oTarget = MetaModel::GetObjectByName($oAttDef->GetTargetClass(), $value, false);
					if(!is_object($oTarget)) {
						$oInbox->Trace("Warning: cannot set default value '$value' for the external key: '$sAttCode'. Unable to find an object of class ".$oAttDef->GetTargetClass()." named '$value'.");
						continue;
					}
					
					$oObj->Set($sAttCode, $oTarget->GetKey());

				}
				else if($oAttDef->IsScalar()) {
					$oObj->Set($sAttCode, $value);
				}
				else {
					$oInbox->Trace("Warning: cannot set default value '$value' for the non-scalar attribute: '$sAttCode'.");
				}
			}
			else {

				$bFound = false;
				
				// Check that the specified value is a possible/allowed value.
				if($oAttDef->IsExternalKey()) {

					$iIntVal = (int)$value;
					$bByKey = false;
					// Numeric-string comparison via explicit string casts on both sides, rather than
					// a loose int/string ==, so this doesn't rely on PHP's implicit type juggling.
					if(is_numeric($value) && ((string) $iIntVal === (string) $value)) {
						// A numeric value is supposed to be the object's key
						$bByKey = true;
					}
					foreach($aAllowedValues as $id => $sName) {
						if($bByKey) {
							if($id === $iIntVal) {
								$bFound = true;
								$oObj->Set($sAttCode, $id);
								break;
							}
						}
						else {
							if(strcasecmp($sName,$value) === 0) {
								$bFound = true;
								$oObj->Set($sAttCode, $id);
								break;
							}
						}
					}
				}
				elseif($oAttDef instanceof AttributeEnum) {

					// For enums the allowed values are value => label. PHP auto-casts purely-numeric
					// array keys to int, while $value (parsed from an admin-configured string) is
					// always a string; compare via explicit string casts rather than a loose ==, so
					// an enum whose value happens to look like an integer still matches correctly.
					foreach($aAllowedValues as $allowedValue => $sLocalizedLabel) {
						if(((string) $allowedValue === (string) $value) || ((string) $sLocalizedLabel === (string) $value)) {
							$bFound = true;
							$oObj->Set($sAttCode, $allowedValue);
							break;
						}
					}
				}
				elseif($oAttDef->IsScalar()) {
					foreach($aAllowedValues as $allowedValue) {
						if((string) $allowedValue === (string) $value) {
							$bFound = true;
							$oObj->Set($sAttCode, $value);
							break;
						}
					}
				}
				else {
					$bFound = true;
					$oInbox->Trace("Warning: Can not set default value '$value' for the non-scalar attribute: '$sAttCode'.");
				}
				
				if(!$bFound) {
					$oInbox->Trace("Warning: Can not set the value '$value' for the field $sAttCode of the ticket. '$value' is not a valid value for $sAttCode.");		
				}
			}
		
		}
	}

}

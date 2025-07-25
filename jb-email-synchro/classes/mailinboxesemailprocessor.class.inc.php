<?php
// Copyright (C) 2012-2019 Combodo SARL
//
//   This program is free software; you can redistribute it and/or modify
//   it under the terms of the GNU Lesser General Public License as published by
//   the Free Software Foundation; version 3 of the License.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of the GNU General Public License
//   along with this program; if not, write to the Free Software
//   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
/**
 * @copyright   Copyright (c) 2012-2025 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

/**
 * Processes messages coming from email sources corresponding to instances
 * of MailInbox (and derived) classes. 1 instance = 1 email source
 *
 */
class MailInboxesEmailProcessor extends EmailProcessor {
	
	protected static $bDebug;
	protected static $aExcludeAttachments;
	protected static $sBodyPartsOrder;
	protected static $sModuleName;
	protected $aInboxes;

	
	/**
	 * Construct a new EmailProcessor... some initialization, reading configuration parameters
	 */
	public function __construct() {
		
		self::$sModuleName = 'jb-email-synchro';
		self::$bDebug = MetaModel::GetModuleSetting(self::$sModuleName, 'debug', false);
		self::$aExcludeAttachments = MetaModel::GetModuleSetting(self::$sModuleName, 'exclude_attachment_types', array());
		self::$sBodyPartsOrder = MetaModel::GetModuleSetting(self::$sModuleName, 'body_parts_order', 'text/html,text/plain');
		$this->aInboxes = array();
		
	}
	
	/**
	 * Outputs some debug text if debugging is enabled from the configuration
	 * @param string $sText The text to output
	 * @return void
	 */
	public static function Trace($sText) {
		if(self::$bDebug) {
			echo $sText."\n";
		}
	}
	/**
	 * Initializes the email sources: one source is created and associated with each MailInboxBase instance
	 *
	 * @return array An array of EmailSource objects
	 * @throws \CoreException
	 * @throws \CoreUnexpectedValue
	 * @throws \MySQLException
	 */
	public function ListEmailSources() {

		static::Trace('-----------------------------------------------------------------------------------------');
		static::Trace('Listing sources:');
		
		$aSources = [];
		$oSearch = new DBObjectSearch('MailInboxBase');
		$oSearch->AddCondition('active', 'yes');
		$oSet = new DBObjectSet($oSearch);
		
		static::Trace('Number of sources found: '.$oSet->Count());
		
		/** @var \MailInboxBase $oInbox */
		while($oInbox = $oSet->Fetch()) {
			
			static::Trace('Add: '.$oInbox->GetKey());
		
			$this->aInboxes[$oInbox->GetKey()] = $oInbox;
			try {
				$oSource = $oInbox->GetEmailSource();
				$oSource->SetToken($oInbox->GetKey()); // to match the source and the inbox later on
				$oSource->SetPartsOrder(static::$sBodyPartsOrder); // in which order to decode the message's body
				$aSources[] = $oSource;
			}
			catch(Exception $e) {
				// Don't use Trace, always output the error so that the log file can be monitored for errors
				echo "Error - Failed to initialize the mailbox: ".$oInbox->GetName().", the mailbox will not be polled. Reason (".$e->getMessage().")\n";
				static::Trace("Error - Failed to initialize the mailbox: ".$oInbox->GetName().", the mailbox will not be polled. Reason (".$e->getMessage().")\n");
			}
		}

		return $aSources;
	}
	
	/**
	 * Retrieves the MailInbox instance associated with the given EmailSource object
	 * @param EmailSource $oSource The EmailSource in which the messages are read
	 * @return MailInboxBase The instance associated with the source
	 * @throws Exception
	 */
	public function GetInboxFromSource(EmailSource $oSource) {
		$iId = $oSource->GetToken();
		if(!array_key_exists($iId, $this->aInboxes)) {
			self::Trace("Unknown MailInbox (id=$iId) for EmailSource '".$oSource->GetName()."'");
			throw new Exception("Unknown MailInbox (id=$iId) for EmailSource '".$oSource->GetName()."'");
		}
		return $this->aInboxes[$iId];
	}
	
	/**
	 * Decides whether a message should be downloaded and processed, deleted, or simply ignored
	 * (i.e left as-is in the mailbox)
	 *
	 * @throws \Exception
	 */
	public function DispatchMessage(EmailSource $oSource, $index, $sUIDL, $oEmailReplica = null) {
		
		self::Trace("Combodo Email Synchro: MailInboxesEmailProcessor: dispatch of the message $index ($sUIDL)");

		$oInbox = $this->GetInboxFromSource($oSource);
		$iRetCode = $oInbox->DispatchEmail($oEmailReplica);
		$sRetCode = EmailProcessor::GetActionFromCode($iRetCode);

		self::Trace("Combodo Email Synchro: MailInboxesEmailProcessor: dispatch of the message $index ($sUIDL) returned $iRetCode ($sRetCode)");
		return $iRetCode;
		
	}

	/**
	 * Process the email downloaded from the mailbox.
	 * This implementation delegates the processing the MailInbox instances.
	 * The caller (identified by its email) must already exists in the database.
	 * @param EmailSource $oSource The source from which the email was read.
	 * @param integer $index The index of the message in the mailbox.
	 * @param EmailMessage $oEmail The downloaded/decoded email message.
	 * @param EmailReplica $oEmailReplica The information associating a ticket to the email. This replica is new (i.e. not yet in DB for new messages)
	 * @param array $aErrors
	 *
	 * @return int
	 */
	public function ProcessMessage(EmailSource $oSource, $index, EmailMessage $oEmail, EmailReplica $oEmailReplica, &$aErrors = array()) {
				
		// Reset error messages before processing.
		// If not, they may be (incorrectly) transferred to the next email replicas too.
		$this->sLastErrorSubject = '';
		$this->sLastErrorMessage = '';
		
		try {			
		
			$oInbox = $this->GetInboxFromSource($oSource);
			self::Trace("Combodo Email Synchro: MailInboxesEmailProcessor: Processing message $index ({$oEmail->sUIDL})");
			if($oEmailReplica->IsNew()) {
				self::Trace('Ticket creation: ProcessNewEmail');
				$oResult = $oInbox->ProcessNewEmail($oSource, $index, $oEmail);
				
				if(is_object($oResult) && $oResult instanceof Ticket) {
					self::Trace('Linked mail to ticket. Handle email replica.');
					
					$oEmailReplica->Set('uidl', $oEmail->sUIDL);
					$oEmailReplica->Set('mailbox_id', $oInbox->GetKey());
					$oEmailReplica->Set('mailbox_path', $oSource->GetMailbox());
					$oEmailReplica->Set('message_id', $oEmail->sMessageId);
					$oEmailReplica->Set('ticket_id', $oResult->GetKey());
					$oEmailReplica->DBInsert();
					if(!empty($oInbox->sLastError)) {
						$this->sLastErrorSubject = "Error during ticket update";
						$this->sLastErrorMessage = $oInbox->sLastError;
						$aErrors[] = $oInbox->sLastError;
					}
				}
				else {
					// Other unexpected error
					$sUIDL = htmlentities($oEmail->sUIDL, ENT_QUOTES, 'UTF-8');
					$this->sLastErrorSubject = "Failed to create a ticket for the incoming email ({$sUIDL}) (" . __METHOD__ . "). No Ticket object. ".(is_object($oResult) ? 'Class: '.get_class($oResult): 'No object.');
					$this->sLastErrorMessage = $oInbox->sLastError;
					$sMessage = "Email Synchro: MailInboxesEmailProcessor: Failed to create a ticket for the incoming email $index ({$oEmail->sUIDL})";
					$aErrors[] = $sMessage;
					$aErrors[] = $oInbox->sLastError;
					self::Trace($sMessage);
				}	
			}
			else {

					$oInbox->ReprocessOldEmail($oSource, $index, $oEmail, $oEmailReplica);		
			}
			$iRetCode = $oInbox->GetNextAction();
			$sRetCode = EmailProcessor::GetActionFromCode($iRetCode);
			self::Trace("Email Synchro: MailInboxesEmailProcessor: End of processing of the new message $index ({$oEmail->sUIDL}) retCode: ($iRetCode) $sRetCode");
		}
		catch(Exception $e) {
			$iRetCode = $oInbox->GetNextAction();
			$this->sLastErrorSubject = "Failed to process email $index ({$oEmail->sUIDL})";
			$this->sLastErrorMessage = "Email Synchro: Failed to create a ticket for the incoming email $index ({$oEmail->sUIDL}), reason: exception: ".$e->getMessage();
			$aErrors[] = $this->sLastErrorMessage;
			self::Trace("Email Synchro: MailInboxesEmailProcessor: Failed to create a ticket for the incoming email $index ({$oEmail->sUIDL}), reason: exception: ".$e->getMessage()."\n".$e->getTraceAsString());
		}

		return $iRetCode;
	}
	
	/**
	 * Called, before deleting the message from the source when the decoding fails
	 * $oEmail can be null
	 *
	 * @param \EmailSource $oSource
	 * @param string $sUIDL
	 * @param \EmailMessage $oEmail
	 * @param \RawEmailMessage $oRawEmail
	 * @param array $aErrors
	 *
	 * @return integer Next action code
	 * @throws \Exception
	 */
	public function OnDecodeError(EmailSource $oSource, $sUIDL, $oEmail, RawEmailMessage $oRawEmail, &$aErrors = array()) {
		$oInbox = $this->GetInboxFromSource($oSource);
		$aErrors[] = "Combodo Email Synchro: MailInboxesEmailProcessor: failed to decode the message ({$sUIDL})";
		if(isset($oEmail)) {
			$aErrors = array_merge($aErrors, $oEmail->GetInvalidReasons());
		}
		$oInbox->HandleError($oEmail, 'decode_failed', $oRawEmail);
		// message will be deleted from the source or marked as error...
		return $oInbox->GetNextAction();
	}
	
}

// Register the background action for asynchronous execution in cron.php
EmailBackgroundProcess::RegisterEmailProcessor('MailInboxesEmailProcessor');

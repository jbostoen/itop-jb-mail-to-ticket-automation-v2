<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260711
 */

namespace JeffreyBostoenExtensions\MailToTicket;

use DirectoryTree\ImapEngine\Message;

// iTop.
use EmailSource;

// Generic.
use Exception;

/**
 * Class MessageHandler. A helper class to keep track of some information related to an e-mail message that is being processed.  
 * It's main purpose is to keep track of an identifier (e.g. index in a listing, uid, uidl).
 */
class MessageHandler {
	
	/**
	 * @var int $iOriginalListingIndex The **original** index (sequence number, starting at 1) of this message when listing the messages in the folder.
	 */
	private int $iOriginalListingIndex;
	
	/**
	 * @var int $iUid The UID of the message.
	 */
	private int $iUid = -1;
	
	/**
	 * @var string $sInternalIdentifier A (supposedly) unique identifier for e-mails within iTop.
	 */
	private string $sInternalIdentifier;

	/**
	 * @var string $sMessageId.
	 */
	private string $sMessageId;

	/**
	 * @var int $iTimestamp
	 */
	private int $iTimestamp;

	/**
	 * @var EmailSource $oSource The e-mail source in iTop.
	 */
	private EmailSource $oSource;
	
	/**
	 * @var Message The message object from the IMAP engine. Higher chance to change at some point.
	 */
	private Message $oMessage;
	
	/**
	 * Sets the original index of the mail within a listing of the e-mails in the folder.
	 *
	 * @param integer $iOriginalListingIndex
	 * @return void
	 */
	public function SetOriginalListingIndex(int $iOriginalListingIndex) : void {
		$this->iOriginalListingIndex = $iOriginalListingIndex;
	}

	/*
	 * Gets the original index of the mail within a listing of the e-mails in the folder.
	 * @return int
	 */
	public function GetOriginalListingIndex() : int {
		return $this->iOriginalListingIndex;
	}

	/**
	 * Sets the UID of the message.
	 * 
	 * The IMAP UID is strictly an unsigned 32-bit integer.  
	 * Note that this is stored per mailbox folder, and for instance GMail will return a different UID for the same message if it's present in multiple "folders" (dynamic view based on labels).
	 * 
	 * @param integer $iUid
	 * @return void
	 */
	public function SetUid(int $iUid) : void {
		$this->iUid = $iUid;
	}

	/**
	 * Returns the UID of the message.
	 * @return int
	 */
	public function GetUid() : int {
		return $this->iUid;
	}

	/**
	 * Sets the internal identifier for iTop.
	 *
	 * @param string $sInternalIdentifier
	 * @return void
	 */
	public function SetInternalIdentifier(string $sInternalIdentifier) : void {
		$this->sInternalIdentifier = $sInternalIdentifier;
	}

	/**
	 * Returns the internal identifier for iTop.
	 * @return string
	 */
	public function GetInternalIdentifier() : string {
		return $this->sInternalIdentifier;
	}

	/**
	 * Sets the message ID.
	 *
	 * @param string $sMessageId
	 * @return void
	 */
	public function SetMessageId(string $sMessageId) : void {
		$this->sMessageId = $sMessageId;
	}

	/**
	 * Returns the message ID.
	 * @return string
	 */
	public function GetMessageId() : string {
		return $this->sMessageId;
	}

	/**
	 * Sets the timestamp. ("sent")
	 *
	 * @param int $iTimestamp
	 * @return void
	 */
	public function SetTimestamp(int $iTimestamp) : void {
		$this->iTimestamp = $iTimestamp;
	}

	/**
	 * Returns the timestamp. ("sent")
	 *
	 * @return int
	 */
	public function GetTimestamp() : int {
		return $this->iTimestamp;
	}

	/**
	 * Sets the related message object (IMAP engine).
	 *
	 * @param Message $oMessage
	 * @return void
	 */
	public function SetMessageObject(Message $oMessage) : void {
		$this->oMessage = $oMessage;
	}

	/**
	 * Returns the related message object (IMAP engine).
	 *
	 * @return Message|null
	 */
	public function GetMessageObject() : ?Message {
		return $this->oMessage;
	}

	/**
	 * Sets the e-mail source.
	 *
	 * @param EmailSource $oSource
	 * @return void
	 */
	public function SetEmailSource(EmailSource $oSource) : void {
		$this->oSource = $oSource;
	}

	/**
	 * Returns the e-mail source.
	 *
	 * @return EmailSource
	 */
	public function GetEmailSource() : EmailSource {
		return $this->oSource;
	}

	/**
	 * **Do NOT call this constructor directly.**  
	 * Use the static FromMessage() method instead.
	 */
	public function __construct() {
		
	}

	/**
	 * Returns a handler.
	 * 
	 * @param Message $oMessage
	 * @param int $iOriginalListIndex The original index of the message in the listing of the messages in the folder.
	 * 
	 * @return MessageHandler
	 */
	public static function FromMessage(Message $oMessage, EmailSource $oSource, int $iOriginalListIndex) : MessageHandler {

		$oHandler = new MessageHandler();

		$oHandler->SetMessageObject($oMessage);

		$oHandler->SetMessageId($oMessage->messageId() ?? '');
		$oHandler->SetUid($oMessage->uid());

		$oHandler->SetEmailSource($oSource);
		$oHandler->SetOriginalListingIndex($iOriginalListIndex);

		// - Date/time.
			$oDate = $oMessage->date();
			$oHandler->SetTimestamp($oDate !== null ? $oDate->timestamp : time());

		// - Internal identifier.
			// Message-ID can be absent on some providers/messages. The IMAP UID is always available,
			// but per Helper::UseMessageIdAsUid()'s own docblock, it can be unstable across sessions
			// on some providers (e.g. Outlook 365, GMail) - precisely the providers use_message_id_as_uid
			// exists for. Silently falling back to the UID there would produce a different "identifier"
			// on every poll of the same message, defeating EmailReplica-based deduplication and creating
			// a duplicate ticket each run. Instead, derive a stable synthetic identifier from data that
			// doesn't change between polls of the same message.
			$sInternalIdentifier = Helper::UseMessageIdAsUid() ? $oMessage->messageId() : $oMessage->uid();

			if($sInternalIdentifier === null) {

				$oFrom = $oMessage->from();
				$sSyntheticSource = ($oFrom !== null ? $oFrom->email() : '').'|'.($oMessage->subject() ?? '').'|'.($oDate !== null ? $oDate->format('Y-m-d H:i:s') : '');
				$sInternalIdentifier = 'synthetic_'.hash('sha256', $sSyntheticSource);

			}

			$oHandler->SetInternalIdentifier($sInternalIdentifier);

		return $oHandler;


	}

	/**
	 * Initiates a message. For instance, to undelete a restored message.
	 *
	 * @return boolean
	 */
	public function InitMessage() : bool {

		try {

			$oSource = $this->GetEmailSource();
			$oSource->InitMessage($this);

		}
		catch(Exception $e) {
			return false;
		}

		return true;


	}


	/**
	 * Deletes the message.
	 *
	 * @return bool False on failure.
	 */
	public function DeleteMessage() : bool {

		try {

			$oSource = $this->GetEmailSource();
			$oSource->DeleteMessage($this);

		}
		catch(Exception $e) {
			return false;
		}

		return true;

	}


	/**
	 * Moves a message to a target folder.
	 *
	 * @param string $sTargetFolder
	 * @return bool
	 */
	public function MoveMessage(string $sTargetFolder) : bool {

		try {

			$oSource = $this->GetEmailSource();
			$oSource->MoveMessage($this, $sTargetFolder);

		}
		catch(Exception $e) {
			return false;
		}

		return true;


	}


	/**
	 * Copies a message to a target folder.
	 *
	 * @param string $sTargetFolder
	 * @return bool
	 */
	public function CopyMessage(string $sTargetFolder) : bool {

		try {

			$oSource = $this->GetEmailSource();
			$oSource->CopyMessage($this, $sTargetFolder);

		}
		catch(Exception $e) {
			return false;
		}

		return true;


	}

	/**
	 * Marks a message as seen (read).
	 *
	 * @return boolean
	 */
	public function MarkAsSeen() : bool {
		
		try {

			$oSource = $this->GetEmailSource();
			$oSource->MarkAsSeen($this);

		}
		catch(Exception $e) {
			return false;
		}

		return true;

	}


}

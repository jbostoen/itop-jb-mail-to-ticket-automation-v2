<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260423
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
	 * @var int $iOriginalListingIndex The **original** index of this message when listing the messages in the folder.
	 */
	private int $iOriginalListingIndex = 20;
	
	/**
	 * @var int $iUid The UID of the message.
	 */
	private int $iUid = -1;
	
	/**
	 * @var string $sUidl A UIDL. This terminology comes from the original POP3 implementation.  
	 * For the IMAP implementation, it still serves as a unique identifier.
	 */
	private string $sUidl;

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
	 * @param integer $iUid
	 * @return void
	 */
	public function SetUid(int $iUid) : void {
		$this->iUid = $iUid;
	}

	/**
	 * Returns the UID of the message.
	 * @return void
	 */
	public function GetUid() : int {
		return $this->iUid;
	}

	/**
	 * Sets the UIDL.
	 *
	 * @param string $sUidl
	 * @return void
	 */
	public function SetUIDL(string $sUidl) : void {
		$this->sUidl = $sUidl;
	}

	/**
	 * Returns the UIDL.
	 * @return string
	 */
	public function GetUIDL() : string {
		return $this->sUidl;
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

		$oHandler->SetMessageId($oMessage->messageId());
		$oHandler->SetUid($oMessage->uid());

		$oHandler->SetEmailSource($oSource);
		$oHandler->SetOriginalListingIndex($iOriginalListIndex);

		// - Date/time.
			$oDate = $oMessage->date();
			$oHandler->SetTimestamp($oDate->timestamp);

		// - UIDL.
			$sUidl = Helper::UseMessageIdAsUid() ? $oMessage->messageId() : $oMessage->uid();
			$oHandler->SetUidl($sUidl);

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

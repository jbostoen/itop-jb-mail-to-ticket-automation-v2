<?php
// Copyright (C) 2019 Combodo SARL
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
 * @copyright   Copyright (c) 2016-2026 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

use JeffreyBostoenExtensions\MailToTicket\MessageHandler;

/**
 * A source of messages (IMAP or File...)
 */
abstract class EmailSource {

	protected $sLastErrorSubject;
	protected $sLastErrorMessage;
	protected $sPartsOrder;
	protected $token;
	
	public function __construct() {
		$this->sPartsOrder = 'text/plain,text/html'; // Default value can be changed via SetPartsOrder
		$this->token  =null;
	}
	
	/**
	 * Get the number of messages to process
	 * @return integer The number of available messages
	 */
	abstract public function GetMessagesCount() : int;
	
	/**
	 * Retrieves the message of the given index [0..Count]
	 * @param int $index The index between zero and count
	 * @return null|MessageFromMailbox
	 */
	abstract public function GetMessage(int $index) : ?MessageFromMailbox;
	
	/**
	 * Initializes the message when it is being processed.
	 * @param MessageHandler $oMsgHandler
	 * @return void
	 */
	abstract public function InitMessage(MessageHandler $oMsgHandler) : void;
	
	/**
	 * Deletes the message of the given index [0..Count] from the mailbox
	 * @param MessageHandler $oMsgHandler The message handler.
	 */
	abstract public function DeleteMessage(MessageHandler $oMsgHandler);
	
	/**
	 * Name of the e-mail source
	 */
	abstract public function GetName() : string;

	
	/**
	 * Moves the message to another folder.
	 * 
	 * @param MessageHandler $oMsgHandler The message handler.
	 * @param string $sTargetFolder
	 * @return bool
	 */
	public function MoveMessage(MessageHandler $oMsgHandler, string $sTargetFolder) : bool {
		// Do nothing
		return false;
	}
	
	/**
	 * Copies the message to another folder.
	 * 
	 * @param MessageHandler $oMsgHandler The message handler.
	 * @param string $sTargetFolder
	 * @return bool
	 */
	public function CopyMessage(MessageHandler $oMsgHandler, string $sTargetFolder) : bool {
		// Do nothing
		return false;
	}


	/**
	 * Marks a message as read / seen.
	 */
	public function MarkAsSeen(MessageHandler $oMsgHandler) : bool {
		return false;
	}

	/**
	 * Mailbox path of the eMail source
	 */
	public function GetMailbox() : string {
		return '';
	}

	/**
	 * Gets the list (of message handlers) of all the messages
	 * @return MessageHandler[]
	 */
	abstract public function GetListing() : array;
	
	/**
	 * Disconnect from the server
	 */
	abstract public function Disconnect() : void;

	
	public function GetLastErrorSubject() : string {
		return $this->sLastErrorSubject;
	}
	
	public function GetLastErrorMessage() : string {
		return $this->sLastErrorMessage;
	}
	
	/**
	 * This impl is bad, but it will lower the risk for children classes in extensions !
	 *
	 * @return string something to identify the source in a log
	 *                this is useful as for example EmailBackgroundProcess is working on this class and not persisted mailboxes ({@link \MailInboxBase})
	 * @since 3.6.1 N°5633 method creation
	 */
	public function GetSourceId() : string {
		return $this->token;
	}
	
	/**
	 * Preferred order for retrieving the mail "body" when scanning a multiparts emails
	 * @param $sPartsOrder string A comma separated list of MIME types e.g. text/plain,text/html
	 */
	public function SetPartsOrder($sPartsOrder) : void {
		$this->sPartsOrder = $sPartsOrder;
	}
	/**
	 * Preferred order for retrieving the mail "body" when scanning a multiparts emails
	 * @return string A comma separated list of MIME types e.g. text/plain,text/html
	 */
	public function GetPartsOrder() : string {
		return $this->sPartsOrder;
	}
	/**
	 * Set an opaque reference token for use by the caller...
	 * @param mixed $token
	 */
 	public function SetToken($token) : void {
 		$this->token = $token;
 	}
 	/**
 	 * Get the reference token set earlier....
 	 * @return mixed The token set by SetToken()
 	 */
 	public function GetToken() : mixed {
 		return $this->token;
 	}
}

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
 * @copyright   Copyright (c) 2016-2022 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

/**
 * A source of messages either POP3, IMAP or File...
 */
abstract class EmailSource
{
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
	abstract public function GetMessagesCount();
	
	/**
	 * Retrieves the message of the given index [0..Count]
	 * @param $index integer The index between zero and count
	 * @return MessageFromMailbox
	 */
	abstract public function GetMessage($index);
	
	/**
	 * Initializes the message when it is being processed.
	 * @param $index integer The index between zero and count
	 * @return void
	 */
	abstract public function InitMessage($index);
	
	/**
	 * Deletes the message of the given index [0..Count] from the mailbox
	 * @param $index integer The index between zero and count
	 */
	abstract public function DeleteMessage($index);
	/**
	 * Name of the eMail source
	 */
	abstract public function GetName();

	/**
	 * @return string something to identify the source in a log
	 *                this is useful as for example EmailBackgroundProcess is working on this class and not persisted mailboxes
	 * @since 3.6.1 N°5633 method creation
	 */
	abstract public function GetSourceId();
	
	/**
	 * Move the message of the given index [0..Count] from the mailbox to another folder
	 * @param $index integer The index between zero and count
	 */
	public function MoveMessage($index) {
		// Do nothing
		return false;
	}
	
	/**
	 * Mailbox path of the eMail source
	 */
	public function GetMailbox() {
		return '';
	}

	/**
	 * Get the list (with their IDs) of all the messages
	 * @return array{msg_id: int, uidl: ?string} 'msg_id' => index, 'uidl' => message identifier (null if message cannot be decoded)
	 */
	abstract public function GetListing();
	
	/**
	 * Disconnect from the server
	 */
	abstract public function Disconnect();

	/**
	 * Workaround for some email servers (like GMail and MS Office 365) where the UID may change between two sessions, so let's use the MessageID
	 * as a replacement for the UID !
	 *
	 * Note that it is possible to receive twice a message with the same MessageID, but since the content of the message
	 * will be the same, it's a safe to process such messages only once...
	 *
	 * BEWARE: Make sure that you empty the mailbox before toggling this setting in the config file, since all the messages
	 *    present in the mailbox at the time of the toggle will be considered as "new" and thus processed again.
	 *
	 * @return boolean
	 * @uses `use_message_id_as_uid` config parameter
	 */
	public static function UseMessageIdAsUid() {
		
		// Note: Contrary to Combodo's version: in most environments it seems better that this is enabled by default.
		return (bool)MetaModel::GetModuleSetting('jb-email-synchro', 'use_message_id_as_uid', true);
		
	}
	
	public function GetLastErrorSubject() {
		return $this->sLastErrorSubject;
	}
	
	public function GetLastErrorMessage() {
		return $this->sLastErrorMessage;
	}
	
	/**
	 * Preferred order for retrieving the mail "body" when scanning a multiparts emails
	 * @param $sPartsOrder string A comma separated list of MIME types e.g. text/plain,text/html
	 */
	public function SetPartsOrder($sPartsOrder) {
		$this->sPartsOrder = $sPartsOrder;
	}
	/**
	 * Preferred order for retrieving the mail "body" when scanning a multiparts emails
	 * @return string A comma separated list of MIME types e.g. text/plain,text/html
	 */
	public function GetPartsOrder() {
		return $this->sPartsOrder;
	}
	/**
	 * Set an opaque reference token for use by the caller...
	 * @param mixed $token
	 */
 	public function SetToken($token) {
 		$this->token = $token;
 	}
 	/**
 	 * Get the reference token set earlier....
 	 * @return mixed The token set by SetToken()
 	 */
 	public function GetToken() {
 		return $this->token;
 	}
}

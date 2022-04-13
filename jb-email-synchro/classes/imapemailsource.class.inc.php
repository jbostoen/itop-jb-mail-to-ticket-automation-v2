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
 * @copyright   Copyright (c) 2012-2022 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

/**
 * Read messages from an IMAP mailbox using PHP's IMAP extension
 * Note: in theory PHP IMAP methods can also be used to connect to  a POP3 mailbox, 
 * but in practice the missing emulation of actual unique identifiers (UIDLs) for the messages makes
 * this unusable for our particular purpose
 */
class IMAPEmailSource extends EmailSource {
	
	protected $rImapConn = null;
	protected $sLogin = '';
	protected $sMailbox = '';
	protected $sTargetFolder = '';

	public function __construct($sServer, $iPort, $sLogin, $sPwd, $sMailbox, $aOptions, $sTargetFolder = '') {
		parent::__construct();
		$this->sLastErrorSubject = '';
		$this->sLastErrorMessage = '';
		$this->sLogin = $sLogin;
		$this->sMailbox = $sMailbox;
		$this->sTargetFolder = $sTargetFolder;

		$sOptions = '';
		if(count($aOptions) > 0) {
			$sOptions = '/'.implode('/',$aOptions);
		}
		
		if (!function_exists('imap_open')) throw new Exception('The imap_open function is missing. Is the PHP module "IMAP" installed on the server?');

		$sIMAPConnStr = "{{$sServer}:{$iPort}$sOptions}$sMailbox";
		
		$this->rImapConn = imap_open($sIMAPConnStr, $sLogin, $sPwd );
		if($this->rImapConn === false) {
			if(class_exists('EventHealthIssue')) {
				EventHealthIssue::LogHealthIssue('jb-email-synchro', "Cannot connect to IMAP server: '$sIMAPConnStr', with credentials: '$sLogin/***'");
			}
			print_r(imap_errors());
			throw new Exception("Cannot connect to IMAP server: '$sIMAPConnStr', with credentials: '$sLogin/'***'");
		}
	}	

	/**
	 * Initializes the message when it is being processed.
	 * @param $index integer The index between zero and count
	 * @return void
	 */
	public function InitMessage($index) {
		
		// Preventive measure. For restored emails, sometimes there's still an IMAP flag indicating it was 'marked for removal'.
		$this->UndeleteMessage($index);
		
		return;
	}
	
	/**
	 * Get the number of messages to process
	 * @return integer The number of available messages
	 */
	public function GetMessagesCount() {
		$oInfo = imap_check($this->rImapConn);
		if ($oInfo !== false) return $oInfo->Nmsgs;
		
		return 0;	
	}
	
	/**
	 * Retrieves the message of the given index [0..Count]
	 * @param $index integer The index between zero and count
	 * @return \MessageFromMailbox
	 */
	public function GetMessage($index) {		
	
		$sRawHeaders = imap_fetchheader($this->rImapConn, 1+$index);
		
		// Rare occurrence of e-mails from 2010/2011, from VirginMedia, sent by an unknown client.
		// To: <Undisclosed-Recipient:;>
		$sLastError = imap_last_error();
		if($sLastError !== false) {
			if(preg_match('/Unexpected characters at end of address: <(.*)>/', $sLastError, $aMatches)) {
				// Exception occurred while parsing From, To, CC headers.
				// It's not necessarily critical, because the problem can be located just in one single address.
				// Drop this warning.
				imap_errors();
			}
		}
		
		
		$sBody = imap_body($this->rImapConn, 1+$index, FT_PEEK);

		$aOverviews = imap_fetch_overview($this->rImapConn, 1+$index);
		$oOverview = array_pop($aOverviews);

		$bUseMessageId = (bool) MetaModel::GetModuleSetting('jb-email-synchro', 'use_message_id_as_uid', false);
		if($bUseMessageId) {
			$oOverview->uid = $oOverview->message_id;
		}

		return new MessageFromMailbox($oOverview->uid, $sRawHeaders, $sBody);
	}

	/**
	 * Marks the message for deletion (IMAP-flag) of the given index [0..Count] from the mailbox.
	 * Still needs expunging (happens on EmailSource::Disconnect() )
	 * @param $index integer The index between zero and count
	 *
	 */
	public function DeleteMessage($index) {
		$ret = imap_delete($this->rImapConn, (1+$index).':'.(1+$index));
		return $ret;
	}
	
	
	/**
	 * Marks the message for undeletion (IMAP-flag) of the given index [0..Count] from the mailbox.
	 * @param $index integer The index between zero and count
	 */
	public function UndeleteMessage($index) {
		$ret = imap_undelete($this->rImapConn, (1+$index).':'.(1+$index));
		return $ret;
	}
	
	/**
	 * Move the message of the given index [0..Count] from the mailbox to another folder
	 * @param $index integer The index between zero and count
	 */
	public function MoveMessage($index) {
		// Note that the message is in fact copied to the new folder (and getting a new UID there), while the original one is marked for deletion
		$ret = imap_mail_move($this->rImapConn, (1+$index).':'.(1+$index), $this->sTargetFolder);
		if(!$ret){
			throw new Exception("Error : Cannot move message to folder ".$this->sTargetFolder.' - '.json_encode(print_r(imap_errors(), true)));
		}
		return $ret;
	}
	
	
	/**
	 * Name of the eMail source
	 */
	 public function GetName() {
	 	return $this->sLogin;
	 }

	/**
	 * Mailbox path of the eMail source
	 */
	public function GetMailbox() {
		return $this->sMailbox;
	}

	/**
	 * Get the list (with their IDs) of all the messages
	 * @return Array An array of hashes: 'msg_id' => index 'uild' => message identifier
	 */
	 public function GetListing() {
	 	$ret = null;
	 	
	 	$oInfo = imap_check($this->rImapConn);
        if(($oInfo !== false) && ($oInfo->Nmsgs > 0)) {
        	$sRange = "1:".$oInfo->Nmsgs;
			// Workaround for some email servers (like GMail!) where the UID may change between two sessions, so let's use the
			// MessageID as a replacement for the UID.
			// Note that it is possible to receive a message with the same MessageID two times, but since the content of the message
			// will be the same, it's safe to process such messages only once...
			// BEWARE: Make sure that you empty the mailbox before toggling this setting in the config file, since all the messages
			// present in the mailbox at the time of the toggle will be considered as "new" and thus processed again.
			// Contrary to the Combodo implementation, this fork defaults to 'true', since it's definitely recommended for GMail and Exchange (IMAP).
        	$bUseMessageId = (bool)MetaModel::GetModuleSetting('jb-email-synchro', 'use_message_id_as_uid', true);

        	$ret = array();
			$aResponse = imap_fetch_overview($this->rImapConn, $sRange);
			
			foreach($aResponse as $aMessage) {
				if($bUseMessageId) {
					// There is a known issue here, probably due to SPAM messages.
					// Tried to figure it out but no example yet.
					// Sometimes an error is returned which outputs this as subject: "Retrieval using the IMAP4 protocol failed for the following message: <some-id>"
					$ret[] = array('msg_id' => $aMessage->msgno, 'uidl' => $aMessage->message_id, 'udate' => $aMessage->udate);
				}
				else {
					$ret[] = array('msg_id' => $aMessage->msgno, 'uidl' => $aMessage->uid, 'udate' => $aMessage->udate);
				}
			}
        }
        
		return $ret;
	 }
	 
	 public function Disconnect() {
	 	imap_close($this->rImapConn, CL_EXPUNGE);
	 	$this->rImapConn = null; // Just to be sure
	 }
	 
	 
	 /**
	  * Get IMAP connection. Exposed to dedicated extensions.
	  *
	  * @return IMAP connection
	  */
	 public function GetConnection() {
		
		return $this->rImapConn;
		
	 }
	 
}

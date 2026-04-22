<?php
// Copyright (C) 2012-2016 Combodo SARL
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
 * @copyright   Copyright (c) 2012-2026 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

use JeffreyBostoenExtensions\MailToTicket\MessageHandler;

/**
 * Reads messages from files stored in a given folder, ordered by their creation date
 */
class TestEmailSource extends EmailSource {

	protected $sSourceDir;
	protected $aMessages;
	protected $sName;
	
	public function __construct($sSourceDir, $sName) {
		parent::__construct();
		//$this->sPartsOrder = 'text/html,text/plain'; // Default value can be changed via SetPartsOrder
		
		$this->sLastErrorSubject = '';
		$this->sLastErrorMessage = '';
		$this->sSourceDir = $sSourceDir;
		$this->sName = $sName;
		$this->aMessages = array();
		
		if(is_dir($this->sSourceDir) == false) {
			mkdir($this->sSourceDir);
		}
		
		$hDir = opendir($this->sSourceDir);
		while(($sFile = readdir($hDir)) !== false)
		{
			if (($sFile != '.') && ($sFile != '..'))
			{
				$sExtension = pathinfo($sFile,PATHINFO_EXTENSION);
				if ($sExtension == 'eml')
				{
					$this->aMessages[] = $sFile;
				}
			}
		}
		closedir($hDir);

		sort($this->aMessages);
	}	
	/**
	 * Get the number of messages to process
	 * @return integer The number of available messages
	 */
	public function GetMessagesCount() : int {
		return count($this->aMessages);
	}
	
	/**
	 * Initializes the message when it is being processed.
	 * @param MessageHandler $oMsgHandler
	 * @return void
	 */
	public function InitMessage(MessageHandler $oMsgHandler) : void {
	}
	
	/**
	 * Retrieves the message of the given index [0..Count]
	 * @param int $index The index between zero and count.
	 * @return null|MessageFromMailbox
	 */
	public function GetMessageFromMailbox(int $index) : ?MessageFromMailbox {
		return MessageFromMailbox::FromFile($this->sSourceDir.'/'.$this->aMessages[$index]);
	}

	/**
	 * Simulates the deletion of the message of the given index [0..Count] from the mailbox... does nothing
	 * @inheritDoc
	 */
	public function DeleteMessage(MessageHandler $oMsgHandler) : bool {
		// Do nothing !
		return true;
	}

	/**
	 * @inheritDoc
	 */
	 public function GetName() : string {
		 
	 	if (!empty($this->sName)) {
		 	return $this->sName;
	 	}
	 	return 'Test Source (from '.$this->sSourceDir.')';

	}

	/**
	 * @inheritDoc
	 */
	public function GetSourceId() : string {
		return $this->sName.' ('.$this->sSourceDir.')';
	}


	/**
	 * @inheritDoc
	 */
	 public function GetListing() : array {
		 
		$aListing = array();
		foreach($this->aMessages as $index => $sName)
		{
			$aListing[] = array('msg_id' => $index, 'uidl' => basename($sName));
		}
		return $aListing;
		
	 }
	 
	 public function Disconnect() : void {
		
	 }

}

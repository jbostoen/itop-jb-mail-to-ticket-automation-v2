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
 * @copyright   Copyright (c) 2016-2024 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

/**
 * Abstract class which serves as a skeleton for implementing your own processor of emails
 *
 */
abstract class EmailProcessor {
	
	const NO_ACTION = 0; // No action taken / don't process message / processing ended.
	const DELETE_MESSAGE = 1; // Mark message for deletion.
	const PROCESS_MESSAGE = 2; // Process message. Used when a new e-mail is dispatched.
	const PROCESS_ERROR = 3; // Mark processing as in error. Unused in this fork?
	const MARK_MESSAGE_AS_ERROR = 4; // Mark message as error.
	const MARK_MESSAGE_AS_UNDESIRED = 5; // Marks message as undesired.
	const MOVE_MESSAGE = 6; // Moves message to specified target folder.
	
	const SKIP_FOR_NOW = 998; // Skip only this message for now.
	const ABORT_ALL_FURTHER_PROCESSING = 999; // Aborts all further processing.

	/**
	 * @var \String[] List of class names of steps in e-mail processing.
	 */
	public static $aAvailableStepClasses = [];
	
	/**
	 * @var \String[] List of class names of steps which are active (so not 'inactive') in e-mail processing.
	 */
	public static $aActiveStepClasses = [];
	
	/**
	 * @return \EmailSource[]
	 */
	abstract public function ListEmailSources();
	
	abstract public function DispatchMessage(EmailSource $oSource, $index, $sUIDL, $oEmailReplica = null);

	/**
	 * Process the email downloaded from the mailbox.
	 * This implementation delegates the processing the MailInbox instances
	 * The caller (identified by its email) must already exists in the database
	 * @param EmailSource $oSource The source from which the email was read
	 * @param integer $index The index of the message in the mailbox
	 * @param EmailMessage $oEmail The downloaded/decoded email message
	 * @param EmailReplica $oEmailReplica The information associating a ticket to the email. This replica is new (i.e. not yet in DB for new messages)
	 * @param array $aErrors
	 *
	 * @return integer Next Action Code
	 */
	abstract public function ProcessMessage(EmailSource $oSource, $index, EmailMessage $oEmail, EmailReplica $oEmailReplica, &$aErrors = array());

	/**
	 * Outputs some debug text if debugging is enabled from the configuration
	 * @param string $sText The text to output
	 * @return void
	 */
	public static function Trace($sText) {
		echo "$sText\n";
	}
	
	/**
	 * Called, before deleting the message from the source when the decoding fails
	 * $oEmail can be null
	 *
	 * @param \EmailSource $oSource
	 * @param $sUIDL
	 * @param \EmailMessage $oEmail
	 * @param \RawEmailMessage $oRawEmail
	 * @param array $aErrors
	 *
	 * @return integer Next Action Code
	 */
	public function OnDecodeError(EmailSource $oSource, $sUIDL, $oEmail, RawEmailMessage $oRawEmail, &$aErrors = array()) {
		$sEmailSubject = '';
		if($oEmail != null) {
			$sEmailSubject = $oEmail->sSubject;
			$aErrors = $oEmail->GetInvalidReasons();
		}
		$aErrors[] = "The message (".$sUIDL."), subject: '$sEmailSubject', was not decoded properly and therefore was not processed.";
		return self::MARK_MESSAGE_AS_ERROR;
	}
	
	/**
	 * @var string To be set by ProcessMessage in case of error
	 */
	protected $sLastErrorSubject;
	/**
	 * @var string To be set by ProcessMessage in case of error
	 */
	protected $sLastErrorMessage;
	 
	/**
	 * Returns the subject for the last error when process ProcessMessage returns PROCESS_ERROR
	 * @return string The subject for the error message email
	 */
	public function GetLastErrorSubject() {
		return $this->sLastErrorSubject;
	}
	/**
	 * Returns the body of the message for the last error when process ProcessMessage returns PROCESS_ERROR
	 * @return string The body for the error message email
	 */
	public function GetLastErrorMessage() {
		return $this->sLastErrorMessage;
	}
	
	/**
	 * Returns a action (string) corresponding to the given action code
	 * @param int $iRetCode The action code from EmailProcessor
	 * @return string The textual code of the action
	 */
	public static function GetActionFromCode($iRetCode) {
		
		$sRetCode = 'Unknown Code '.$iRetCode;
		switch($iRetCode)
		{
			case EmailProcessor::NO_ACTION:
				$sRetCode = 'NO_ACTION';
				break;

			case EmailProcessor::DELETE_MESSAGE;
				$sRetCode = 'DELETE_MESSAGE';
				break;

			case EmailProcessor::PROCESS_MESSAGE:
				$sRetCode = 'PROCESS_MESSAGE';
				break;

			case EmailProcessor::PROCESS_ERROR:
				$sRetCode = 'PROCESS_ERROR';
				break;

			case EmailProcessor::MARK_MESSAGE_AS_ERROR:
				$sRetCode = 'MARK_MESSAGE_AS_ERROR';
				break;

			case EmailProcessor::MARK_MESSAGE_AS_UNDESIRED:
				$sRetCode = 'MARK_MESSAGE_AS_UNDESIRED';
				break;
				
            case EmailProcessor::MOVE_MESSAGE:
				$sRetCode = 'MOVE_MESSAGE';
				break;
				
			case EmailProcessor::SKIP_FOR_NOW:
				$sRetCode = 'SKIP_FOR_NOW';
				break;
				
			case EmailProcessor::ABORT_ALL_FURTHER_PROCESSING:
				$sRetCode = 'ABORT_ALL_FURTHER_PROCESSING';
				break;
		}
		return $sRetCode;
	}
	
}

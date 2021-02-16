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
 * @copyright   Copyright (C) 2016 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

/**
 * The interface between iBackgroundProcess (generic background tasks for iTop)
 * and the emails processing mechanism based on EmailProcessor
 */
class EmailBackgroundProcess implements iBackgroundProcess {
	protected static $aEmailProcessors = array();
	protected static $sSaveErrorsTo = '';
	protected static $sNotifyErrorsTo = '';
	protected static $sNotifyErrorsFrom = '';
	protected static $bMultiSourceMode = false;
	public static $iMaxEmailSize = 0;
	protected $bDebug;
	private $aMessageTrace = array();
	private $iCurrentRequestMessage;
	/**
	 * @var EmailSource
	 */
	private $oCurrentSource;
	
	/**
	 * Activates the given EmailProcessor specified by its class name
	 * @param string $sClassName
	 */
	public static function RegisterEmailProcessor($sClassName) {
		self::$aEmailProcessors[] = $sClassName;
	}
	
	public function __construct() {
		$this->bDebug = MetaModel::GetModuleSetting('jb-email-synchro', 'debug', false);
		self::$sSaveErrorsTo = MetaModel::GetModuleSetting('jb-email-synchro', 'save_errors_to', '');
		self::$sNotifyErrorsTo = MetaModel::GetModuleSetting('jb-email-synchro', 'notify_errors_to', '');
		self::$sNotifyErrorsFrom = MetaModel::GetModuleSetting('jb-email-synchro', 'notify_errors_from', '');
		$sMaxEmailSize = MetaModel::GetModuleSetting('jb-email-synchro', 'maximum_email_size', '0');
		self::$iMaxEmailSize = utils::ConvertToBytes($sMaxEmailSize);
	}

	protected function Trace($sText) {
		$this->aMessageTrace[] = $sText;
		if ($this->bDebug) {
			echo $sText."\n";
		}
	}
	
    /**
     * Tries to set the error message from the $oProcessor. Sets a default error message in case of failure.
     *
     * @param EmailReplica $oEmailReplica
     * @param EmailProcessor $oProcessor
     * @param string $sErrorCode
	 * @param null|RawEmailMessage $oRawEmail
	 *
	 * @throws \CoreCannotSaveObjectException
	 * @throws \CoreException
	 * @throws \CoreUnexpectedValue
     */
	protected function UpdateEmailReplica(&$oEmailReplica, $oProcessor, $sErrorCode = 'error', $oRawEmail = null) {
		try {
			if(is_null($oRawEmail)) {
				$oCurrentSource = $this->oCurrentSource;
				$iCurrentRequestMessage = $this->iCurrentRequestMessage;
				if(isset($oCurrentSource)) {
					$oRawEmail = $oCurrentSource->GetMessage($iCurrentRequestMessage);
				}
			}
			if(!in_array($sErrorCode, MetaModel::GetAllowedValues_att('EmailReplica', 'status'))) {
				$sErrorCode = 'error';
			}
			$oEmailReplica->Set('status', $sErrorCode);
			if(isset($oRawEmail)) {
				$this->SaveEml($oEmailReplica, $oRawEmail);
			}

			$iMaxSize = MetaModel::GetAttributeDef('EmailReplica', 'error_message')->GetMaxSize();
			$sErrorMessage = $oProcessor->GetLastErrorSubject()." - ".$oProcessor->GetLastErrorMessage();
			$sErrorMessage = substr($sErrorMessage, 0, $iMaxSize);
			$oEmailReplica->Set('error_message', $sErrorMessage);

            $sDate = $oEmailReplica->Get('message_date');
            if(empty($sDate)) {
                $oEmailReplica->SetCurrentDate('message_date');
            }
			$oEmailReplica->Set('error_trace', $this->GetMessageTrace());
			
			$oEmailReplica->DBWrite();
		}
		catch (Exception $e) {
			$this->Trace('Error: ' . $oProcessor->GetLastErrorSubject() . " - " . $oProcessor->GetLastErrorMessage());
			IssueLog::Error('Email not processed for email replica of uidl "' . $oEmailReplica->Get('uidl') . '" and message_id "' . $oEmailReplica->Get('message_id') . '" : ' . $oProcessor->GetLastErrorSubject() . " - " . $oProcessor->GetLastErrorMessage());

			$sMessage = $e->getMessage();
			if(strlen($sMessage) > 10*1024) {
				$sMessage = "Truncated message: \n".substr($sMessage, 0, 8*1024)."\n[...]\n".substr($sMessage, -2*1024);
			}
			IssueLog::Error($sMessage);

			if(strpos($e->getMessage(), 'MySQL server has gone away') === false) {
				$oEmailReplica->Set('status', 'error');
				$oEmailReplica->Set('error_message', 'An error occurred during the processing of this email that could not be displayed here. Consult application error log for details.');
				$oEmailReplica->Set('error_trace', '');
				$oEmailReplica->DBWrite();
			}
		}
	}
	public function GetPeriodicity() {	
		return (int)MetaModel::GetModuleSetting('jb-email-synchro', 'periodicity', 30); // seconds
	}

	public function ReportError($sSubject, $sMessage, $oRawEmail) {
		if((self::$sNotifyErrorsTo != '') && (self::$sNotifyErrorsFrom != '')) {
			$oRawEmail->SendAsAttachment(self::$sNotifyErrorsTo, self::$sNotifyErrorsFrom, $sSubject, $sMessage);
			//@mail(self::$sNotifyErrorsTo, $sSubject, $sMessage, 'From: '.self::$sNotifyErrorsFrom);
		}
	}
	
	/**
	 * Call this function to set this mode to true if you want to
	 * process several incoming mailboxes and if the mail server
	 * does not assign unique UIDLs across all mailboxes
	 * For example with MS Exchange the UIDL is just a sequential
	 * number 1,2,3... inside each mailbox.
	 */
	public static function SetMultiSourceMode($bMode = true) {
		self::$bMultiSourceMode = $bMode;
	}
	
	public static function IsMultiSourceMode() {
		return self::$bMultiSourceMode;
	}
	
	public function Process($iTimeLimit) {
		$iTotalMessages = 0;
		$iTotalProcessed = 0;
		$iTotalMarkedAsError = 0;
		$iTotalSkipped = 0;
		$iTotalDeleted = 0;
        $iTotalUndesired = 0;
		foreach(self::$aEmailProcessors as $sProcessorClass) {
			/** @var \EmailProcessor $oProcessor */
			$oProcessor = new $sProcessorClass();
			$aSources = $oProcessor->ListEmailSources();
			foreach($aSources as $oSource) {
				$iMsgCount = $oSource->GetMessagesCount();
				$this->Trace("-----------------------------------------------------------------------------------------");			
				$this->Trace("Processing Message Source: ".$oSource->GetName()." GetMessagesCount returned: $iMsgCount");			

				if($iMsgCount != 0) {
					
					$aMessages = $oSource->GetListing();
					$iMsgCount = count($aMessages);
					
					/** @var \MailInboxBase $oInbox Mail inbox */
					$oInbox = $oProcessor->GetInboxFromSource($oSource);
					if($oInbox->Get('imap_order') == 'default') {
						$iStart = 0;
						$iEnd = ($iMsgCount - 1); // $iMsgCount will already be positive, no additional check needed
						$iCounter = 1;
					}
					elseif($oInbox->Get('imap_order') == 'reverse') {
						$iStart = ($iMsgCount - 1); // $iMsgCount will already be positive, no additional check needed
						$iEnd = 0;
						$iCounter = -1;
					}
					
					// Get the corresponding EmailReplica object for each message
					$aUIDLs = array();
					
					// Gets all UIDLs to identify EmailReplicas in iTop.
					$iMessage = $iStart - $iCounter; // Counter is incremented early on in while right after a condition check. So decrease/increase already.
					$bKeepProcessing = true;
					while($bKeepProcessing == true) {
						
						$iRealMessageIndex = array_keys($aMessages)[$iMessage];
						
						// Evaluate new index
						if($iCounter == 1) {
							// Already processed the last message; or worse: invalid (higher) index
							if($iMessage >= $iEnd) {
								$bKeepProcessing = false;
								break;
							}
						}
						elseif($iCounter == -1) {
							// Already processed the last message; or worse: invalid (lower) index
							if($iMessage <= $iEnd) {
								$bKeepProcessing = false;
								break;
							
							}
						}
						
						$iMessage = $iMessage + $iCounter;
						
						// Assume that EmailBackgroundProcess::IsMultiSourceMode() is always set to true
						if(self::IsMultiSourceMode()) {
							$aUIDLs[] = $oSource->GetName().'_'.$aMessages[$iRealMessageIndex]['uidl'];
						}
						else {
							$aUIDLs[] = $aMessages[$iRealMessageIndex]['uidl'];
						}
						
						
					}
					
					$sOQL = 'SELECT EmailReplica WHERE uidl IN (' . implode(',', CMDBSource::Quote($aUIDLs)) . ') AND mailbox_path = ' . CMDBSource::Quote($oSource->GetMailbox());
					$this->Trace("Searching EmailReplicas: '$sOQL'");
					$oReplicaSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL));
					$aReplicas = array();
					while($oReplica = $oReplicaSet->Fetch()) {
						$aReplicas[$oReplica->Get('uidl')] = $oReplica;
					}
					
					// Processes the actual messages
					$iMessage = $iStart - $iCounter; // Counter is incremented early on in while right after a condition check. So decrease/increase already.
					$bKeepProcessing = true;
					while($bKeepProcessing == true) {
						
						// Evaluate new index
						if($iCounter == 1) {
							// Already processed the last message; or worse: invalid (higher) index
							if($iMessage >= $iEnd) {
								$bKeepProcessing = false;
								break;
							}
						}
						elseif($iCounter == -1) {
							// Already processed the last message; or worse: invalid (lower) index
							if($iMessage <= $iEnd) {
								$bKeepProcessing = false;
								break;
							}
						}
						
						$iMessage = $iMessage + $iCounter;
						$iRealMessageIndex = array_keys($aMessages)[$iMessage];
						
						
						// N°3218 initialize a new CMDBChange for each message
						// we cannot use \CMDBObject::SetCurrentChange($oChange) as this would force to persist our change for each message
						// even if no CMDBChangeOp is created during the message processing !
						// By doing so we lose the ability to set the CMDBChange date
						CMDBObject::SetCurrentChange(null);
						CMDBObject::SetTrackInfo('Mail to ticket automation (background process)');
						CMDBObject::SetTrackOrigin('custom-extension');
						
						try {
									
							$this->InitMessageTrace($oSource, $iRealMessageIndex);
							
							$iTotalMessages++;
							if(self::IsMultiSourceMode()) {
								$sUIDL = $oSource->GetName().'_'.$aMessages[$iRealMessageIndex]['uidl'];
							}
							else {
								$sUIDL = $aMessages[$iRealMessageIndex]['uidl'];
							}

							$oEmailReplica = array_key_exists($sUIDL, $aReplicas) ? $aReplicas[$sUIDL] : null;
		
							if($oEmailReplica == null) {
								
								$this->Trace("\nDispatching new message: uidl=$sUIDL index=$iRealMessageIndex");
								// Create a replica to keep track that we've processed this email
								$oEmailReplica = new EmailReplica();
								$oEmailReplica->Set('uidl', $sUIDL);
								$oEmailReplica->Set('mailbox_path', $oSource->GetMailbox());
								$oEmailReplica->Set('message_id', $iRealMessageIndex); // Investigate. Placeholder?
								$oEmailReplica->Set('last_seen', date('Y-m-d H:i:s'));
								
								// Initialize e-mail which is being processed for the first time
								$oSource->InitMessage($iRealMessageIndex);
							}
							else {
								
								if($oEmailReplica->Get('status') == 'error') {
									$this->Trace("\nSkipping old (already processed) message: uidl=$sUIDL index=$iRealMessageIndex marked as 'error'");
									$iTotalSkipped++;
									continue;
								}
								elseif($oEmailReplica->Get('status') == 'ignored') {
									$this->Trace("\nSkipping old (already processed) message: uidl=$sUIDL index=$iRealMessageIndex marked as 'ignored'");
									$iTotalSkipped++;
									continue;
								}
								elseif($oEmailReplica->Get('status') == 'undesired') {
									$this->Trace("\nUndesired message: uidl=$sUIDL index=$iRealMessageIndex");
									$iDelay = MetaModel::GetModuleSetting('jb-email-synchro', 'undesired_purge_delay', 7) * 86400;
									if($iDelay > 0) {
										$sDate = $oEmailReplica->Get('message_date');
										$oDate = DateTime::createFromFormat('Y-m-d H:i:s', $sDate);
										if($oDate !== false) {
											$iDate = $oDate->getTimestamp();
											$iDelay -= time() - $iDate;
										}
									}
									if($iDelay <= 0) {
										$iDelay = MetaModel::GetModuleSetting('jb-email-synchro', 'undesired_purge_delay', 7);
										$this->Trace("\nDeleting undesired message (AND replica) due to purge delay threshold ({$iDelay}): uidl={$sUIDL} index={$iRealMessageIndex}");
										$iTotalDeleted++;
										$ret = $oSource->DeleteMessage($iRealMessageIndex);
										$this->Trace("DeleteMessage($iRealMessageIndex) returned $ret");
										if(!$oEmailReplica->IsNew()) {
										   $aReplicas[$sUIDL] = $oEmailReplica;
										}
										continue;
									}
									$iTotalSkipped++;
									continue;
								}
								else {
									$this->Trace("\nDispatching old (already read) message: uidl={$sUIDL} index={$iRealMessageIndex}");						
								}
							}
							
							$iActionCode = $oProcessor->DispatchMessage($oSource, $iRealMessageIndex, $sUIDL, $oEmailReplica);
					
							switch($iActionCode) {
								case EmailProcessor::MARK_MESSAGE_AS_ERROR:
									$iTotalMarkedAsError++;
									$this->Trace("Marking the message (and replica): uidl={$sUIDL} index={$iRealMessageIndex} as in error.");
									$this->UpdateEmailReplica($oEmailReplica, $oProcessor);
									break;
								
								case EmailProcessor::DELETE_MESSAGE:
									$iTotalDeleted++;
									$this->Trace("Deleting message (AND replica): uidl={$sUIDL} index={$iRealMessageIndex}");
									$ret = $oSource->DeleteMessage($iRealMessageIndex);
									$this->Trace("DeleteMessage({$iRealMessageIndex}) returned {$ret}");
									if(!$oEmailReplica->IsNew()) {
										$aReplicas[$sUIDL] = $oEmailReplica;
									}
									break;
								
								case EmailProcessor::PROCESS_MESSAGE:
									$iTotalProcessed++;
									if($oEmailReplica->IsNew()) {
										$this->Trace("Processing new message: {$sUIDL}");
									}
									else {
										$this->Trace("Processing old (already read) message: {$sUIDL}");
									}
			
			
									$oRawEmail = $oSource->GetMessage($iRealMessageIndex);
									
									// IMAP error occurred?
									if(is_null($oRawEmail)) {
										$this->Trace("Could not get message (raw email): {$sUIDL}");
										return "Stopped processing due to (possible temporary) IMAP error. Message(s) read: $iTotalMessages, message(s) skipped: {$iTotalSkipped}, message(s) processed: {$iTotalProcessed}, message(s) deleted: {$iTotalDeleted}, message(s) marked as error: {$iTotalMarkedAsError}, undesired message(s): {$iTotalUndesired}";
									}


									$oEmail = $oRawEmail->Decode($oSource->GetPartsOrder());
									
									// Checks for valid caller (name, email), UIDL and attachments
									if(!$oEmail->IsValid()) {

										$iNextActionCode = $oProcessor->OnDecodeError($oSource, $sUIDL, null, $oRawEmail);

										switch($iNextActionCode) {
											case EmailProcessor::MARK_MESSAGE_AS_ERROR:
												$iTotalMarkedAsError++;
												$this->Trace("Failed to decode the message, marking the message (and replica): uidl={$sUIDL} index={$iRealMessageIndex} as in error.");
												$this->UpdateEmailReplica($oEmailReplica, $oProcessor);
												$aReplicas[$sUIDL] = $oEmailReplica; // Remember this new replica, don't delete it later as "unused"
											break;
								
											case EmailProcessor::DELETE_MESSAGE:
												$iTotalDeleted++;
												$this->Trace("Failed to decode the message, deleting it (and its replica): {$sUIDL}");
												$oSource->DeleteMessage($iRealMessageIndex);
												if(!$oEmailReplica->IsNew()) {
													$aReplicas[$sUIDL] = $oEmailReplica;
												}
										}
									}
									 
									else {
										

										$iNextActionCode = $oProcessor->ProcessMessage($oSource, $iRealMessageIndex, $oEmail, $oEmailReplica);									  
										$this->Trace("EmailReplica ID after ProcessMessage(): ".$oEmailReplica->GetKey());
						
										switch($iNextActionCode) {
											case EmailProcessor::MARK_MESSAGE_AS_ERROR:

												$iTotalMarkedAsError++;
												$this->Trace("Marking the valid message (and replica): uidl={$sUIDL} index={$iRealMessageIndex} as in error.");
												$this->UpdateEmailReplica($oEmailReplica, $oProcessor);							
												$aReplicas[$sUIDL] = $oEmailReplica; // Remember this new replica, don't delete it later as "unused"
												break;
	 
											case EmailProcessor::MARK_MESSAGE_AS_UNDESIRED:

												$iTotalUndesired++;
												$this->Trace("Marking the message (and replica): uidl={$sUIDL} index={$iRealMessageIndex} as undesired.");
												$this->UpdateEmailReplica($oEmailReplica, $oProcessor, 'undesired');
												$aReplicas[$sUIDL] = $oEmailReplica; // Remember this new replica, don't delete it later as "unused"
												break;
	 
											case EmailProcessor::DELETE_MESSAGE:

												$iTotalDeleted++;
												$this->Trace("Deleting message (marked as DELETE_MESSAGE) (but not replica): {$sUIDL}");
												$oSource->DeleteMessage($iRealMessageIndex);
												if(!$oEmailReplica->IsNew()) { 
													$aReplicas[$sUIDL] = $oEmailReplica;
												}
												break;
											
											case EmailProcessor::PROCESS_ERROR:
												$sSubject = $oProcessor->GetLastErrorSubject();
												$sMessage = $oProcessor->GetLastErrorMessage();
												EmailBackgroundProcess::ReportError($sSubject, $sMessage, $oRawEmail);
												$iTotalDeleted++;
												$this->Trace("Deleting message (but not replica) due to process error: {$sUIDL}");
												$oSource->DeleteMessage($iRealMessageIndex);
												if(!$oEmailReplica->IsNew()) {								
													$aReplicas[$sUIDL] = $oEmailReplica;
												}
												break;
			
											default:
											case EmailProcessor::NO_ACTION:

												$this->Trace("No more action for EmailReplica ID: ".$oEmailReplica->GetKey());
												$this->UpdateEmailReplica($oEmailReplica, $oProcessor, 'ok', $oRawEmail);
												$aReplicas[$sUIDL] = $oEmailReplica; // Remember this new replica, don't delete it later as "unused"
												break;
										}

									}
									

									break;
					
								case EmailProcessor::NO_ACTION:
								default:
									$this->Trace("No action for message (and replica): {$sUIDL}");
									$aReplicas[$sUIDL] = $oEmailReplica; // Remember this new replica, don't delete it later as "unused"
									break;
							}
							if(time() > $iTimeLimit)  {
								// Process the other e-mails later

								break; 
							}
							
							
							
						}
						catch(Exception $e) {

							if(!empty($oEmailReplica)) {
								$this->Trace($e->getMessage());
								$this->UpdateEmailReplica($oEmailReplica, $oProcessor);
							}
							throw $e;
						}
						
					}
					if(time() > $iTimeLimit) {
						// Process the other e-mails later

						break;
					}
					

					if(self::IsMultiSourceMode()) {
						$aIDs = array(-1); // Make sure that the array is never empty...
						foreach($aReplicas as $oUsedReplica) {
							if(is_object($oUsedReplica) && ($oUsedReplica->GetKey() != null)) {
								// Fix IMAP: remember last seen. Aka: do not delete message because connection failed.
								$oUsedReplica->Set('last_seen', date('Y-m-d H:i:s'));
								$oUsedReplica->DBUpdate();
								$aIDs[] = (Int)$oUsedReplica->GetKey();
							}
						}
						
						// Cleanup the unused replicas based on the pattern of their UIDL, unfortunately this is not possible in NON multi-source mode
						$sOQL = "SELECT EmailReplica WHERE uidl LIKE " . CMDBSource::Quote($oSource->GetName() . '_%') . 
							" AND mailbox_path = " . CMDBSource::Quote($oSource->GetMailbox()) . 
							" AND id NOT IN (" . implode(',', $aIDs) . ")";
						$this->Trace("Searching for unused EmailReplicas: {$sOQL}");
						$oUnusedReplicaSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL));
						$oUnusedReplicaSet->OptimizeColumnLoad(array('EmailReplica' => array('uidl')));
						while($oReplica = $oUnusedReplicaSet->Fetch()) {
							if(strtotime($oReplica->Get('last_seen')) < strtotime('-7 day')) {
								// Replica not used for at least 7 days
								$this->Trace("Deleting unused and outdated EmailReplica (#".$oReplica->GetKey()."), UIDL: ".$oReplica->Get('uidl'));
								$oReplica->DBDelete();
							}
							
							if (time() > $iTimeLimit) break; // We'll do the rest later
						}
					}
				}
				$oSource->Disconnect();
			}
			if (time() > $iTimeLimit) break; // We'll do the rest later
		}
		return "Message(s) read: $iTotalMessages, message(s) skipped: $iTotalSkipped, message(s) processed: $iTotalProcessed, message(s) deleted: $iTotalDeleted, message(s) marked as error: $iTotalMarkedAsError, undesired message(s): $iTotalUndesired";
	}
	
	private function InitMessageTrace($oSource, $iRealMessageIndex) {
		$this->oCurrentSource = $oSource;
		$this->iCurrentRequestMessage = $iRealMessageIndex;
		$this->aMessageTrace = array();
	}

	private function GetMessageTrace() {
		return "<pre>".htmlentities(implode("\n", $this->aMessageTrace), ENT_QUOTES, 'UTF-8')."</pre>";
	}
	
	/**
	 * @param \EmailReplica $oEmailReplica
	 * @param $oRawEmail
	 *
	 * @throws \CoreException
	 * @throws \CoreUnexpectedValue
	 */
	protected function SaveEml(&$oEmailReplica, $oRawEmail) {
		$iContentSize = strlen($oRawEmail->GetRawContent());
		$iMaxServerSize = CMDBSource::GetServerVariable('max_allowed_packet') - 128*1024;
		if($iContentSize < $iMaxServerSize) {
			$oEmailReplica->Set('contents', new ormDocument($oRawEmail->GetRawContent(), 'message/rfc822', 'email.eml'));
		}
		else {
			$this->Trace("EML too big ($iContentSize bytes) max is ($iMaxServerSize bytes), not saved in database.");
			$oEmailReplica->Set('error_trace', $this->GetMessageTrace());
		}
	}
}

//EmailBackgroundProcess::RegisterEmailProcessor('TestEmailProcessor');

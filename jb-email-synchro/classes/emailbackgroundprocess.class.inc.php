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
 * @copyright   Copyright (c) 2016-2023 Combodo SARL
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
	 * @var string Message to print before exiting, when the iProcess time limit is exceeded
	 * @since 3.5.3 N°5043
	 */
	const CRON_TIME_LIMIT_REACHED_MESSAGE = 'iProcess time limit exceeded: exiting!';
	
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
		
		
		// Build filtered list of policy classes only once per process execution.
		EmailProcessor::$aAvailableStepClasses = [];

		foreach(get_declared_classes() as $sClassName) {
		
			if(is_subclass_of($sClassName, 'jb_itop_extensions\mail_to_ticket\Step') == true) {
				
				EmailProcessor::$aAvailableStepClasses[] = $sClassName;
				
			}
			
		}

		
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
				$this->Trace('Unable to set error code to "'.$sErrorCode.'". Fallback to "error"').
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
		$iTotalSkippedError = 0;
		$iTotalSkippedIgnored = 0;
		$iTotalSkippedUndesired = 0;
		$iTotalDeleted = 0;
		$iTotalMoved = 0;
        $iTotalUndesired = 0;
		$iTotalUnreadable = 0; // Can not be red by the mail library (N° 5633)
		
		$this->Trace("-----------------------------------------------------------------------------------------");
		$this->Trace('. '.count(EmailProcessor::$aAvailableStepClasses).' steps to process.');
		
		foreach(self::$aEmailProcessors as $sProcessorClass) {
			
			/** @var \EmailProcessor $oProcessor */
			$oProcessor = new $sProcessorClass();
			$aSources = $oProcessor->ListEmailSources();
			
			
			
			foreach($aSources as $oSource) {
				
				$iMsgCount = $oSource->GetMessagesCount();
				$this->Trace("-----------------------------------------------------------------------------------------");
				$this->Trace("Processing Message Source: ".$oSource->GetName());
				try {
					$iMsgCount = $oSource->GetMessagesCount();
				}
				catch (Exception $e) {
					$this->LogProcessException($e, $oSource);
					$oSource->Disconnect();
					continue;
				}
				$this->Trace("GetMessagesCount returned: $iMsgCount");
				
				

				if($iMsgCount != 0) {
					
					try {
						$aMessages = $oSource->GetListing();
					}
					catch (Exception $e) {
						$this->LogProcessException($e, $oSource);
						$oSource->Disconnect();
					}
					
					$iMsgCount = count($aMessages);
					
					/** @var \MailInboxBase $oInbox Mail inbox */
					$oInbox = $oProcessor->GetInboxFromSource($oSource);
					
					// Whether a policy is active or inactive, is determined per mailbox.
					EmailProcessor::$aActiveStepClasses = [];
					
					// Start from pre-filtered class names.
					foreach(EmailProcessor::$aAvailableStepClasses as $sClassName) {
					
						// Policies can easily be turned off.
						// 'inactive' means the policy can be skipped altogether.
						// 'do_nothing' means the policy should still be processed, but the policy itself shouldn't have any influence.
						$sAttCode = $sClassName::GetXMLSettingsPrefix().'_behavior';
						
						if(MetaModel::IsValidAttCode(get_class($oInbox), $sAttCode) == false) {

							EmailProcessor::$aActiveStepClasses[] = $sClassName;
							
						}
						elseif($oInbox->Get($sAttCode) != 'inactive') {
						
							// No default "behavior" attribute; assume this step must always be executed
							EmailProcessor::$aActiveStepClasses[] = $sClassName;
							
						}
						
					}
					
					
					// Sort but keep original index (to request right message)
					if($oInbox->Get('protocol') == 'imap') {
						
						// Sort but keep original index
						uasort($aMessages, function($a, $b) {
							return $a['udate'] <=> $b['udate'];
						});
					}
					else {
						// In case of POP3 (no longer supported) or other protocols
						// No sorting
					}
					
					// @todo The following 2 lines don't seem to be used anymore?
					$iStart = 0;
					$iEnd = ($iMsgCount - 1); // $iMsgCount will already be positive, no additional check needed
					
					// Get the corresponding EmailReplica object for each message
					$aUIDLs = array();
					
					// Gets all UIDLs to identify EmailReplicas in iTop.
					foreach(array_keys($aMessages) as $iMessage) {
						
						$sUIDL = $aMessages[$iMessage]['uidl'];
						
						if(is_null($sUIDL) == true) {
							$iTotalUnreadable++;
							continue;
						}
						
						// Assume that EmailBackgroundProcess::IsMultiSourceMode() is always set to true
						if(self::IsMultiSourceMode()) {
							$sUIDL = $oSource->GetName().'_'.$aMessages[$iMessage]['uidl'];
						}
						else {
							$sUIDL = $aMessages[$iMessage]['uidl'];
						}
						
						$aUIDLs[] = $sUIDL;
						
					}
					
					$sOQL = 'SELECT EmailReplica WHERE uidl IN (' . implode(',', CMDBSource::Quote($aUIDLs)) . ') AND mailbox_path = ' . CMDBSource::Quote($oSource->GetMailbox());
					$this->Trace("Searching EmailReplicas: '$sOQL'");
					$oReplicaSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL));
					$aReplicas = array();
					while($oReplica = $oReplicaSet->Fetch()) {
						$aReplicas[$oReplica->Get('uidl')] = $oReplica;
					}
					
					// Processes the actual messages in the correct order
					// Due to sorting above with uasort(), the array keys might have changed from e.g. 0, 1, 2  to 0, 2, 1
					foreach(array_keys($aMessages) as $iMessage) {
						
						// N°3218 initialize a new CMDBChange for each message
						// we cannot use \CMDBObject::SetCurrentChange($oChange) as this would force to persist our change for each message
						// even if no CMDBChangeOp is created during the message processing !
						// By doing so we lose the ability to set the CMDBChange date
						CMDBObject::SetCurrentChange(null);
						CMDBObject::SetTrackInfo('Mail to ticket automation (background process)');
						// Important: Don't use the \Combodo\iTop\Core\CMDBChange\CMDBChangeOrigin::EMAIL_PROCESSING yet, as it is only available in iTop 3.0+
						CMDBObject::SetTrackOrigin('email-processing');
						
						try {
									
							$this->InitMessageTrace($oSource, $iMessage);
							
							$iTotalMessages++;

							$sUIDL = $aMessages[$iMessage]['uidl'];
							if(is_null($sUIDL)) {
								continue; // invalid email, see \EmailSource::GetListing and N°5633
							}
							
							if(self::IsMultiSourceMode()) {
								$sUIDL = $oSource->GetName().'_'.$sUIDL;
							}

							$oEmailReplica = array_key_exists($sUIDL, $aReplicas) ? $aReplicas[$sUIDL] : null;
		
							if($oEmailReplica == null) {
								
								$this->Trace("\nDispatching new message: uidl=$sUIDL index=$iMessage");
								// Create a replica to keep track that we've processed this email
								$oEmailReplica = new EmailReplica();
								$oEmailReplica->Set('uidl', $sUIDL);
								$oEmailReplica->Set('mailbox_path', $oSource->GetMailbox());
								$oEmailReplica->Set('message_id', $iMessage); // This will be set to the actual Message-ID/UIDL in ProcessMessage().
								$oEmailReplica->Set('last_seen', date('Y-m-d H:i:s'));
								
								// Initialize e-mail which is being processed for the first time
								$oSource->InitMessage($iMessage);
								
							}
							else {
								
								if($oEmailReplica->Get('status') == 'error') {
									$this->Trace("\nSkipping old (already processed) message: uidl=$sUIDL index=$iMessage marked as 'error'");
									$iTotalSkippedError++;
									continue;
								}
								elseif($oEmailReplica->Get('status') == 'ignored') {
									$this->Trace("\nSkipping old (already processed) message: uidl=$sUIDL index=$iMessage marked as 'ignored'");
									$iTotalSkippedIgnored++;
									continue;
								}
								elseif($oEmailReplica->Get('status') == 'undesired') {
									$this->Trace("\nUndesired message: uidl=$sUIDL index=$iMessage");
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
										$this->Trace("\nDeleting undesired message (AND replica) due to purge delay threshold ({$iDelay}): uidl={$sUIDL} index={$iMessage}");
										$iTotalDeleted++;
										$ret = $oSource->DeleteMessage($iMessage);
										$this->Trace("DeleteMessage($iMessage) returned $ret");
										if(!$oEmailReplica->IsNew()) {
										   $aReplicas[$sUIDL] = $oEmailReplica;
										}
										continue;
									}
									$iTotalSkippedUndesired++;
									continue;
								}
								else {
									$this->Trace("\nDispatching old (already read) message: uidl={$sUIDL} index={$iMessage}");						
								}
							}
							
							$iActionCode = $oProcessor->DispatchMessage($oSource, $iMessage, $sUIDL, $oEmailReplica);
					
							switch($iActionCode) {
								
								case EmailProcessor::MARK_MESSAGE_AS_ERROR:
									$iTotalMarkedAsError++;
									$this->Trace("Marking the message (and replica): uidl={$sUIDL} index={$iMessage} as in error.");
									$this->UpdateEmailReplica($oEmailReplica, $oProcessor);
									break;
								
								case EmailProcessor::DELETE_MESSAGE:
									$iTotalDeleted++;
									$this->Trace("Deleting message (AND replica): uidl={$sUIDL} index={$iMessage}");
									$ret = $oSource->DeleteMessage($iMessage);
									$this->Trace("DeleteMessage({$iMessage}) returned {$ret}");
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
			
			
									$oRawEmail = $oSource->GetMessage($iMessage);
									
									// IMAP error occurred?
									if(is_null($oRawEmail)) {
										$this->Trace("Could not get message (raw email): {$sUIDL}");
										return "Stopped processing due to (possible temporary) IMAP error. Message(s) read: $iTotalMessages, message(s) skipped: {$iTotalSkipped}, message(s) processed: {$iTotalProcessed}, message(s) deleted: {$iTotalDeleted}, message(s) marked as error: {$iTotalMarkedAsError}, undesired message(s): {$iTotalUndesired}";
									}


									$oEmail = $oRawEmail->Decode($oSource->GetPartsOrder());
									
									// Checks for valid caller (name, email), UIDL and attachments
									if(!$oEmail->IsValid()) {

										$iNextActionCode = $oProcessor->OnDecodeError($oSource, $sUIDL, $oEmail, $oRawEmail);

										switch($iNextActionCode) {
											case EmailProcessor::MARK_MESSAGE_AS_ERROR:
												$iTotalMarkedAsError++;
												$this->Trace("Failed to decode the message, marking the message (and replica): uidl={$sUIDL} index={$iMessage} as in error.");
												$this->UpdateEmailReplica($oEmailReplica, $oProcessor);
												$aReplicas[$sUIDL] = $oEmailReplica; // Remember this new replica, don't delete it later as "unused"
												break;
								
											case EmailProcessor::DELETE_MESSAGE:
												$iTotalDeleted++;
												$this->Trace("Failed to decode the message, deleting it (and its replica): {$sUIDL}");
												$oSource->DeleteMessage($iMessage);
												if(!$oEmailReplica->IsNew()) {
													$aReplicas[$sUIDL] = $oEmailReplica;
												}
										}
									}
									 
									else {
										

										$iNextActionCode = $oProcessor->ProcessMessage($oSource, $iMessage, $oEmail, $oEmailReplica);									  
										$this->Trace("EmailReplica ID after ProcessMessage(): ".$oEmailReplica->GetKey());
						
										switch($iNextActionCode) {
											
											case EmailProcessor::MARK_MESSAGE_AS_ERROR:

												$iTotalMarkedAsError++;
												$this->Trace("Marking the valid message (and replica): uidl={$sUIDL} index={$iMessage} as in error.");
												$this->UpdateEmailReplica($oEmailReplica, $oProcessor);							
												$aReplicas[$sUIDL] = $oEmailReplica; // Remember this new replica, don't delete it later as "unused"
												break;
	 
											case EmailProcessor::MARK_MESSAGE_AS_UNDESIRED:

												$iTotalUndesired++;
												$this->Trace("Marking the message (and replica): uidl={$sUIDL} index={$iMessage} as undesired.");
												$this->UpdateEmailReplica($oEmailReplica, $oProcessor, 'undesired');
												$aReplicas[$sUIDL] = $oEmailReplica; // Remember this new replica, don't delete it later as "unused"
												break;
	 
											case EmailProcessor::DELETE_MESSAGE:

												$iTotalDeleted++;
												$this->Trace("Deleting message (marked as DELETE_MESSAGE) (but not replica): {$sUIDL}");
												$oSource->DeleteMessage($iMessage);
												if(!$oEmailReplica->IsNew()) { 
													$aReplicas[$sUIDL] = $oEmailReplica;
												}
												break;
											
											case EmailProcessor::MOVE_MESSAGE:
											
												$iTotalMoved++;
												$this->Trace("Move message (and replica): $sUIDL / index $iMessage");
												try {
													$ret = $oSource->MoveMessage($iMessage);
												}
												catch(Exception $e) {
													$this->Trace("Unable to move message");
												}
												break;
											
											// @todo Seems to be unused in this fork. Clean up?
											case EmailProcessor::PROCESS_ERROR:
											
												$sSubject = $oProcessor->GetLastErrorSubject();
												$sMessage = $oProcessor->GetLastErrorMessage();
												EmailBackgroundProcess::ReportError($sSubject, $sMessage, $oRawEmail);
												$iTotalDeleted++;
												$this->Trace("Deleting message (but not replica) due to process error: {$sUIDL}");
												$oSource->DeleteMessage($iMessage);
												if(!$oEmailReplica->IsNew()) {								
													$aReplicas[$sUIDL] = $oEmailReplica;
												}
												break;
			
											
												
											case EmailProcessor::SKIP_FOR_NOW:
											
												$this->Trace('Skip for now.');
												if($oEmailReplica->IsNew() == false) {
													$oEmailReplica->DBDelete();
												}
												break 2;
												
											case EmailProcessor::ABORT_ALL_FURTHER_PROCESSING:

												$this->Trace('Abort all further processing.');
												if($oEmailReplica->IsNew() == false) {
													$oEmailReplica->DBDelete();
												}
												break 3;
												
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

							if(time() > $iTimeLimit) {
								
								$this->Trace(self::CRON_TIME_LIMIT_REACHED_MESSAGE);
								break; // We'll do the rest later
								
							}
							
							
							
						}
						catch(Exception $e) {

							if(!empty($oEmailReplica)) {
								$this->Trace($e->getMessage());
								$this->UpdateEmailReplica($oEmailReplica, $oProcessor);
							}
							
							$this->LogProcessException($e, $oSource);
							
							throw $e;
						}						
						
					}
					
					if(time() > $iTimeLimit) {
						// Process the other e-mails later

						break;
					}
					

					if(self::IsMultiSourceMode()) {
						
						$aIDs = [ -1 ]; // Make sure that the array is never empty...
						foreach($aReplicas as $oUsedReplica) {
							if(is_object($oUsedReplica) && ($oUsedReplica->GetKey() != null)) {
								// Fix IMAP: remember last seen. Aka: do not delete message because connection failed.
								$oUsedReplica->Set('last_seen', date('Y-m-d H:i:s'));
								$oUsedReplica->DBUpdate();
								$aIDs[] = (Int)$oUsedReplica->GetKey();
							}
						}
						
						// Cleanup the unused replicas based on the pattern of their UIDL, unfortunately this is not possible in NON multi-source mode
						$iRetentionPeriod = MetaModel::GetModuleSetting('jb-email-synchro', 'retention_period', 24);
						$sOQL = "SELECT EmailReplica WHERE uidl LIKE " . CMDBSource::Quote($oSource->GetName() . '_%') .
							" AND mailbox_path = " . CMDBSource::Quote($oSource->GetMailbox()) .
							" AND id NOT IN (" . implode(',', CMDBSource::Quote($aIDs)) . ")".
							" AND last_seen <	DATE_SUB(NOW(), INTERVAL ".$iRetentionPeriod." HOUR)";
						$this->Trace("Searching for unused EmailReplicas: {$sOQL}");
						$oUnusedReplicaSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL));
						$oUnusedReplicaSet->OptimizeColumnLoad(['EmailReplica' => ['uidl']]);
						while($oReplica = $oUnusedReplicaSet->Fetch()) {
							// Replica not used for at least 7 days
							$this->Trace("Deleting unused EmailReplica since ".$iRetentionPeriod." hours (#".$oReplica->GetKey()."), UIDL: ".$oReplica->Get('uidl'));
							$oReplica->DBDelete();
							
							if (time() > $iTimeLimit)  {
				
								$this->Trace(self::CRON_TIME_LIMIT_REACHED_MESSAGE);
								break; // We'll do the rest later
								
							}
						}
					}
				}
				$oSource->Disconnect();
			}
			if (time() > $iTimeLimit) {
				
				$this->Trace(self::CRON_TIME_LIMIT_REACHED_MESSAGE);
				break; // We'll do the rest later
				
			}
		}
		return "Message(s) read: $iTotalMessages, message(s) skipped: $iTotalSkippedError in error / $iTotalSkippedIgnored ignored / $iTotalSkippedUndesired undesired, message(s) processed: $iTotalProcessed, message(s) deleted: $iTotalDeleted, message(s) marked as error: $iTotalMarkedAsError, undesired message(s): $iTotalUndesired, message(s) moved: $iTotalMoved, unreadable: $iTotalUnreadable";
	}
	
	/**
	 * @param \Exception $e
	 * @param \EmailSource $oSource
	 *
	 * @return void
	 *
	 * @since 3.6.1 N°5633 Method creation
	 */
	protected function LogProcessException($e, $oSource) {
		
		$sExceptionMessage = $e->getMessage();
		$sSourceId = $oSource->GetSourceId();
		$sSimpleErrorMessage = __CLASS__.': an exception occurred when reading content for mailbox';

		// MailInboxStandard
		$sMailboxId = $oSource->GetToken(); // see init in \MailInboxesEmailProcessor::ListEmailSources
		/** @var \MailInboxStandard $oMailbox */
		try {
			$oMailbox = MetaModel::GetObject(MailInboxStandard::class, $sMailboxId, false);
		}
		catch (ArchivedObjectException $e) { // cannot group exceptions before PHP 7.1.0 (see https://www.php.net/manual/en/language.exceptions.php)
			$oMailbox = null;
		}
		catch (CoreException $e) {
			$oMailbox = null;
		}
		
		$sDetailedErrorMessage = $sSimpleErrorMessage;
		
		if(is_null($oMailbox) == true) {
			$sDetailedErrorMessage .= " `{$sSourceId}`: {$sExceptionMessage}";
		}
		else {
			
			$oMailbox->Trace($sSimpleErrorMessage);

			try {
				$sMailboxName = $oMailbox->GetName();
			}
			catch (CoreException $e) {
				// shouldn't happen, but trying to be defensive !
				$sMailboxName = 'N/A';
			}
			$sDetailedErrorMessage .= " `{$sMailboxName}::id=$sMailboxId`: {$sExceptionMessage}";
			
		}

		// BackgroundProcess
		$this->Trace($sDetailedErrorMessage);

		// LogAPI
		IssueLog::Error($sDetailedErrorMessage, 'CLI'); // \LogChannels::CLI isn't available in iTop < 3.0.0
		
	}
	
	private function InitMessageTrace($oSource, $iMessage) {
		$this->oCurrentSource = $oSource;
		$this->iCurrentRequestMessage = $iMessage;
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

<?php
// Copyright (C) 2019 Combodo SARL
//
//   This file is part of iTop.
//
//   iTop is free software; you can redistribute it and/or modify	
//   it under the terms of the GNU Affero General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.
//
//   iTop is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU Affero General Public License for more details.
//
//   You should have received a copy of the GNU Affero General Public License
//   along with iTop. If not, see <http://www.gnu.org/licenses/>
/**
 * Processing of AJAX calls for the CalendarView
 *
 * @copyright   Copyright (c) 2013-2025 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

require_once('../../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/ajaxwebpage.class.inc.php');

use Combodo\iTop\Application\WebPage\AjaxPage;

/**
 * @param AjaxPage $oPage
 * @param MailInboxBase $oInbox
 *
 * @throws ArchivedObjectException
 * @throws CoreException
 * @throws CoreUnexpectedValue
 * @throws MissingQueryArgument
 * @throws MySQLException
 * @throws MySQLHasGoneAwayException
 * @throws OQLException
 */
function GetMailboxContent($oPage, $oInbox) {
	
	if(is_object($oInbox)) {
		
		/** @var int $iStartIndex Pagination: Index of e-mail to start with. */
		$iStartIndex = utils::ReadParam('start', 0);
		/** @var int $iStartIndex Pagination: The number of e-mails to retrieve. */
		$iMaxCount = utils::ReadParam('count', 10);
		
		try {
			
			/** @var EmailSource $oSource */
			$oSource = $oInbox->GetEmailSource();

			/** @var int $iTotalMsgCount The number of available messages. */
			$iTotalMsgCount = $oSource->GetMessagesCount();
			$aMessages = $oSource->GetListing(); // Note: this may differ from $oSource->GetMessagesCount(); as messages with errors could be skipped.
			$iTotalMsgOkCount = count(array_filter($aMessages, function($aMsg) {
				return (is_null($aMsg['uidl']) == false);
			}));
			
			if($iStartIndex < 0 || $iMaxCount <= 0) {
				// Don't process, invalid indexes
				$oPage->add('Invalid start or max.');
			}
			
			// Avoid user specifying a higher number (start) than the total message number count
			$iStart = $iStartIndex;
		
			// Avoid user specifying a higher number (start + count) than the total mesage number count
			// The largest index is (message count - 1), since messages are retrieved by index (starting at 0)
			// Check the total (readable) message count here.
			$iEnd = min($iStart + $iMaxCount - 1, $iTotalMsgCount - 1); 
			
		}
		catch(Exception $e) {
			$aContext = array(
				'file'            => $e->getFile(),
				'line'            => $e->getLine(),
				'exception.class' => get_class($e),
				'exception.stack' => $e->getTraceAsString(),
			);
			IssueLog::Error('Failed to initialize the mailbox: '.$oInbox->GetName().'. Reason: '.$e->getMessage(), null, $aContext);
			$oPage->p('Failed to initialize the mailbox: '.$oInbox->GetName().'. Reason: '.$e->getMessage());
			return;
			
		}

		
		
		if($iTotalMsgCount > 0) {
			
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
			
			// Get the corresponding EmailReplica object for each message
			$aUIDLs = [];
			
			foreach(array_keys($aMessages) as $iMessage) {
				
				// Real index does not matter here. Just collecting ALL UIDLs
				$sMessageUidl = $aMessages[$iMessage]['uidl'];
				if (is_null($sMessageUidl)) {
					continue;
				}
				$aUIDLs[] = $sMessageUidl;
				
			}
			
			/** @var int $iMsgOkCount The number of readable emails between start and end index. */
			$iMsgOkCount = 0;

			/** @var int $iProcessedCount The number of processed (non-corrupted) emails / Existing e-mail replicas. */
			$iProcessedCount = 0;

			if($iTotalMsgOkCount > 0) {
				
				$sOQL = '
					SELECT EmailReplica 
					WHERE 
						uidl IN ('.implode(',', CMDBSource::Quote($aUIDLs)).') 
						AND mailbox_path = ' . CMDBSource::Quote($oInbox->Get('mailbox')).' 
						AND mailbox_id = '.$oInbox->GetKey();
					
				IssueLog::Info("Searching EmailReplica objects: $sOQL");
				$oReplicaSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL));
				$oReplicaSet->OptimizeColumnLoad(['EmailReplica' => [
					'uidl',
					'ticket_id',
					'status',
					'error_message'
				]]);
				$iProcessedCount = $oReplicaSet->Count();
				$aProcessed = [];
				
				while($oReplica = $oReplicaSet->Fetch()) {
					$aProcessed[$oReplica->Get('uidl')] = [
						'status' => $oReplica->Get('status'),
						'ticket_id' => $oReplica->Get('ticket_id'),
						'error_message' => $oReplica->Get('error_message'),
						'id' => $oReplica->GetKey(),
					];
				}
				
				// Table config. Will be used as headers while the data is processed.
				$aTableConfig = [
					'checkbox' => ['label' => '<input type="checkbox" id="mailbox_checkall"/>', 'description' => ''],
					'status' => ['label' => Dict::S('MailInbox:Status'), 'description' => ''],
					'date' => ['label' => Dict::S('MailInbox:Date'), 'description' => ''],
					'from' => ['label' => Dict::S('MailInbox:From'), 'description' => ''],
					'subject' => ['label' => Dict::S('MailInbox:Subject'), 'description' => ''],
					'ticket' => ['label' =>  Dict::S('MailInbox:RelatedTicket'), 'description' => ''],
					'error' => ['label' =>  Dict::S('MailInbox:ErrorMessage'), 'description' => ''],
					'details' => ['label' =>  Dict::S('MailInbox:MessageDetails'), 'description' => ''],
				];

				$aData = [];
				$aMessageIndexes = array_keys($aMessages);
				
				$iCurrentIndex = $iStart;
				while($iCurrentIndex <= $iEnd) {
					
					// Obtain the actual index for the message (take Nth index)
					$iMessage = $aMessageIndexes[$iCurrentIndex];
									
					$oRawEmail = $oSource->GetMessage($iMessage);
					
					if(is_null($oRawEmail) == true) {
						
						// Just ignore.
						// Adding a dummy record in the table could lead to actions being performed which should not be available for this corrupted message.
						
					}
					else {
						
						$iMsgOkCount =+ 1;
						$oEmail = $oRawEmail->Decode($oSource->GetPartsOrder());

						$sUIDLs = $aMessages[$iMessage]['uidl'];
						$sStatus = Dict::S('MailInbox:Status/New');
						$sLink = '';
						$sErrorMsg = '';
						$sDetailsLink = '';
						if(array_key_exists($sUIDLs, $aProcessed)) {
							
							switch($aProcessed[$sUIDLs]['status']) {
								case 'ok':
									$sStatus = Dict::S('MailInbox:Status/Processed');
									break;

								case 'error':
									$sStatus = Dict::S('MailInbox:Status/Error');
									break;

								case 'undesired':
									$sStatus = Dict::S('MailInbox:Status/Undesired');
									break;

								case 'ignored':
									$sStatus = Dict::S('MailInbox:Status/Ignored');
							}
							$sErrorMsg = $aProcessed[$sUIDLs]['error_message'];
							if($aProcessed[$sUIDLs]['ticket_id'] != '') {
								$sTicketUrl = ApplicationContext::MakeObjectUrl($oInbox->Get('target_class'), $aProcessed[$sUIDLs]['ticket_id']);
								$sLink = '<a href="'.$sTicketUrl.'">'.$oInbox->Get('target_class').'::'.$aProcessed[$sUIDLs]['ticket_id'].'</a>';
							}
							$aArgs = ['operation' => 'message_details', 'sUIDL' => $sUIDLs];
							$sDetailsURL = utils::GetAbsoluteUrlModulePage(basename(dirname(__FILE__)), 'details.php', $aArgs);
							$sDetailsLink = '<a href="'.$sDetailsURL.'">'.Dict::S('MailInbox:MessageDetails').'</a>';
						}
						$aData[] = [
							'checkbox' => '<input type="checkbox" class="mailbox_item" value="'.htmlentities($sUIDLs, ENT_QUOTES, 'UTF-8').'"/>',
							'status' => $sStatus,
							'date' => $oEmail->sDate,
							'from' => $oEmail->sCallerEmail,
							'subject' => $oEmail->sSubject,
							'ticket' => $sLink,
							'error' => $sErrorMsg,
							'details' => $sDetailsLink,
						];
						
					}
					
					$iCurrentIndex += 1;
					
				}
			
			}

			if($iTotalMsgCount > 0) {
				
				// If we have messages in the mailbox, even if none can be read (meaning they can't be displayed), we are displaying the mailbox stats
				// This will greatly help the user understanding what's going on !
				$oPage->p(Dict::Format('MailInbox:Z_DisplayedThereAre_X_Msg_Y_NewInTheMailbox',
					$iMsgOkCount,
					$iTotalMsgCount,
					($iTotalMsgCount - $iProcessedCount),
					($iTotalMsgCount - $iTotalMsgOkCount))
				);
				
			}
			
			if($iMsgOkCount > 0) {
				
				$oPage->table($aTableConfig, $aData);
				$oPage->add('<div><img alt="" src="../images/tv-item-last.gif" style="vertical-align:bottom;margin-left:10px;"/>&nbsp;'.Dict::S('MailInbox:WithSelectedDo').'&nbsp;&nbsp<button class="mailbox_button ibo-button ibo-is-regular ibo-is-neutral" id="mailbox_reset_status">'.Dict::S('MailInbox:ResetStatus').'</button>&nbsp;&nbsp;<button class="mailbox_button ibo-button ibo-is-regular ibo-is-danger" id="mailbox_delete_messages">'.Dict::S('MailInbox:DeleteMessage').'</button>&nbsp;&nbsp;<button class="mailbox_button ibo-button ibo-is-regular ibo-is-neutral" id="mailbox_ignore_messages">'.Dict::S('MailInbox:IgnoreMessage').'</button></div>');
			
			} 
			else {
				
				// Contrary to the original Combodo message: this could mean there are actually e-mails in the mailbox, but they can not be processed.
				$oPage->p(Dict::Format('MailInbox:NoValidEmailsFound'));
				
			}

			if($iTotalMsgCount > 0) {
				// If we have messages in the mailbox, even if none can be read (meaning they can't be displayed), we are displaying the mailbox stats
				// This will greatly help the user understanding what's going on !
				$oPage->p(Dict::Format('MailInbox:Z_DisplayedThereAre_X_Msg_Y_NewInTheMailbox',
					$iMsgOkCount,
					$iTotalMsgCount,
					($iTotalMsgCount - $iProcessedCount),
					($iTotalMsgCount - $iTotalMsgOkCount))
				);
			}
			
		}
		else {
			$oPage->p(Dict::Format('MailInbox:EmptyMailbox'));
		}
	}
	else {
		$oPage->P(Dict::S('UI:ObjectDoesNotExist'));
	}
}

/**
 * Finds the index of the message with the given UIDL identifier
 * 
 * @param \Array $aMessages The array returned by $oSource->GetListing()
 * @param \String $sUIDL The UIDL to find
 * @return \Integer The index of the message or false if not found
 */
function FindMessageIDFromUIDL($aMessages, $sUIDL) {

	$sKey = $sUIDL;
	foreach($aMessages as $aData) {
		if(strcmp($sKey, $aData['uidl']) == 0) {
			return $aData['msg_id'] - 1; // return a zero based index
		}
	}
	return false;
}

try {
	
	require_once(APPROOT.'/application/cmdbabstract.class.inc.php');
	require_once(APPROOT.'/application/startup.inc.php');
	require_once(APPROOT.'/application/loginwebpage.class.inc.php');
	LoginWebPage::DoLogin(true /* bMustBeAdmin */, false /* IsAllowedToPortalUsers */); // Check user rights and prompt if needed
	
	$oPage = new AjaxPage('');

	$sOperation = utils::ReadParam('operation', '');
	$iMailInboxId = utils::ReadParam('id', 0, false, 'raw_data');
	if(empty($iMailInboxId)) {
		$oInbox = null; // The Combodo implementation has this, but it will result in errors anyway later on in the operations below?
	}
	else {
		/** @var MailInboxBase $oInbox */						   
		$oInbox = MetaModel::GetObject('MailInboxBase', $iMailInboxId, false);
	}
	switch($sOperation) {
		
		case 'mailbox_content':
			GetMailboxContent($oPage, $oInbox);
			break;

		case 'mailbox_reset_status':

			$aUIDLs = utils::ReadParam('aUIDLs', [], false, 'raw_data');

			if(count($aUIDLs) > 0) {
				
				// As for the replicas, consider this:
				// - There could be multiple mailboxes with the same message (and UIDL).
				// - There could be a copy of the same message (same UIDL) in a different folder on the same mailbox.

				// Therefore, the email replica must consider the MailInboxBase!
				
				$sOQL = 'SELECT EmailReplica WHERE uidl IN ('.implode(',', CMDBSource::Quote($aUIDLs)).') AND mailbox_id = '.$oInbox->GetKey(); 
				$oReplicaSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL));
				$oReplicaSet->OptimizeColumnLoad(['EmailReplica' => ['uidl']]);
				$aReplicas = [];
				while($oReplica = $oReplicaSet->Fetch()) {
					$oReplica->DBDelete();
				}
				
			}
			GetMailboxContent($oPage, $oInbox);
			break;

		case 'mailbox_delete_messages':
		
			$aUIDLs = utils::ReadParam('aUIDLs', [], false, 'raw_data');

			if(count($aUIDLs) > 0) {
				
				// The message will be deleted on the e-mail server. 
				// It will be removed only from the folder that is configured for this MailInboxBase.

				// As for the replicas, consider this:
				// - There could be multiple mailboxes with the same message (and UIDL).
				// - There could be a copy of the same message (same UIDL) in a different folder on the same mailbox.
				// Therefore, the behavior here changed: email replicas are no longer removed at this point!
				// Instead, they will be cleaned up automatically anyway due to various jobs.

				// Delete the actual email from the mailbox
				$oSource = $oInbox->GetEmailSource();
				$aMessages = $oSource->GetListing();
				
				foreach($aUIDLs as $sUIDL) {
					
					$idx = FindMessageIDFromUIDL($aMessages, $sUIDL);
					if ($idx !== false) {
						// Delete the actual email from the mailbox
						$oSource->DeleteMessage($idx);
					}
					
				}
				
				$oSource->Disconnect();
				
			}
			GetMailboxContent($oPage, $oInbox);
			break;

		case 'mailbox_ignore_messages':
		
			$aUIDLs = utils::ReadParam('aUIDLs', [], false, 'raw_data');

			if(count($aUIDLs) > 0) {
				
				// In case the same mailbox happens to be configured multiple times in iTop:
				// Contrary to deletion, the intention may be to ignore the message for some specific reason only for this particular configuration.
				$sOQL = 'SELECT EmailReplica WHERE uidl IN ('.implode(',', CMDBSource::Quote($aUIDLs)).') AND mailbox_id = '.$oInbox->GetKey();
				$oReplicaSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL));
				$aReplicas = [];
				/** @var DBObject $oReplica */
				while($oReplica = $oReplicaSet->Fetch()) {
					$oReplica->Set('status', 'ignored');
					$oReplica->DBUpdate();
					$aReplicas[$oReplica->Get('uidl')] = true;
				}
				
			}
			
			if(count($aReplicas) < count($aUIDLs)) {
				
				// Some "New" messages are marked as ignore
				// Add them to the database
				$oSource = $oInbox->GetEmailSource();
				$aMessages = $oSource->GetListing();
				foreach ($aUIDLs as $sUIDL) {
					if(isset($aReplicas[$sUIDL])) {
						// Already processed
						continue;
					}
					$oEmailReplica = new EmailReplica();
					$oEmailReplica->Set('uidl', $sUIDL);
					$oEmailReplica->Set('status', 'ignored');
					$oEmailReplica->Set('mailbox_id', $oInbox->GetKey());
					$oEmailReplica->Set('mailbox_path', $oSource->GetMailbox());
					foreach ($aMessages as $iMessage => $aMessage) {
						if($aMessage['uidl'] == $sUIDL) {
							$oEmailReplica->Set('message_id', $iMessage);
							$oEmailReplica->DBInsert();
							break;
						}
					}
				}
			}
			
			GetMailboxContent($oPage, $oInbox);
			break;
	}
	$oPage->output();
}
catch(Exception $e) {
	$oPage->SetContentType('text/html');
	$oPage->add($e->getMessage());
	$oPage->output();
}

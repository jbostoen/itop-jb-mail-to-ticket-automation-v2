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
 * @copyright   Copyright (c) 2013-2022 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

require_once('../../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/ajaxwebpage.class.inc.php');

/**
 * @param \ajax_page $oPage
 * @param \MailInboxBase $oInbox
 *
 * @throws \ArchivedObjectException
 * @throws \CoreException
 * @throws \CoreUnexpectedValue
 * @throws \MissingQueryArgument
 * @throws \MySQLException
 * @throws \MySQLHasGoneAwayException
 * @throws \OQLException
 */
function GetMailboxContent($oPage, $oInbox) {
	
	if(is_object($oInbox)) {
		
		$iStartIndex = utils::ReadParam('start', 0);
		$iMaxCount = utils::ReadParam('count', 10);
		$iMsgCount = 0;
		
		try {
			
			/** @var \EmailSource $oSource */
			$oSource = $oInbox->GetEmailSource();
			$iTotalMsgCount = $oSource->GetMessagesCount();
			$aMessages = $oSource->GetListing();
			$bEmailsToProcess = false;
			
			
			if($iStartIndex < 0 || $iMaxCount <= 0) {
				// Don't process, invalid indexes
				$oPage->add('Invalid start or max.');
			}
			
			// Avoid user specifying a higher number (start) than the total message number count
			$iStart = $iStartIndex;
		
			// Avoid user specifying a higher number (start + count) than the total mesage number count
			// The largest index is (message count - 1), since messages are retrieved by index (starting at 0)
			$iEnd = min($iStart + $iMaxCount -1,  $iTotalMsgCount - 1); 
			
		}
		catch(Exception $e) {
			
			$oPage->p('Failed to initialize the mailbox: '.$oInbox->GetName().'. Reason: '.$e->getMessage());
			return;
			
		}
	
		$iProcessedCount = 0;
		
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
				
				// Assume that EmailBackgroundProcess::IsMultiSourceMode() is always set to true
				// Real index does not matter here. Just collecting ALL UIDLs
				$aUIDLs[] = $oSource->GetName().'_'.$aMessages[$iMessage]['uidl'];
				
			}
			
			$sOQL = 'SELECT EmailReplica WHERE uidl IN ('.implode(',', CMDBSource::Quote($aUIDLs)).') AND mailbox_path = ' . CMDBSource::Quote($oInbox->Get('mailbox'));
			$oReplicaSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL));
			$oReplicaSet->OptimizeColumnLoad(['EmailReplica' => ['uidl', 'ticket_id', 'status', 'error_message']]);
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
				$oEmail = $oRawEmail->Decode($oSource->GetPartsOrder());

				// Assume that EmailBackgroundProcess::IsMultiSourceMode() is always set to true
				$sUIDLs = $oSource->GetName().'_'.$aMessages[$iMessage]['uidl'];
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
				
				$iCurrentIndex += 1;
				
			}
		
			if(count($aData) > 0) {
				$oPage->p(Dict::Format('MailInbox:Z_DisplayedThereAre_X_Msg_Y_NewInTheMailbox', count($aData), $iTotalMsgCount, ($iTotalMsgCount - $iProcessedCount)));
				$oPage->table($aTableConfig, $aData);
				$oPage->add('<div><img alt="" src="../images/tv-item-last.gif" style="vertical-align:bottom;margin-left:10px;"/>&nbsp;'.Dict::S('MailInbox:WithSelectedDo').'&nbsp;&nbsp<button class="mailbox_button ibo-button ibo-is-regular ibo-is-neutral" id="mailbox_reset_status">'.Dict::S('MailInbox:ResetStatus').'</button>&nbsp;&nbsp;<button class="mailbox_button ibo-button ibo-is-regular ibo-is-danger" id="mailbox_delete_messages">'.Dict::S('MailInbox:DeleteMessage').'</button>&nbsp;&nbsp;<button class="mailbox_button ibo-button ibo-is-regular ibo-is-neutral" id="mailbox_ignore_messages">'.Dict::S('MailInbox:IgnoreMessage').'</button></div>');
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
 * @param \Array $aMessages The array returned by $oSource->GetListing()
 * @param \String $sUIDL The UIDL to find
 * @param \EmailSource $oSource
 * @return \Integer The index of the message or false if not found
 */
function FindMessageIDFromUIDL($aMessages, $sUIDL, EmailSource $oSource) {
	$sKey = $sUIDL;
	$sMultiSourceKey = substr($sUIDL, 1 + strlen($oSource->GetName())); // in Multisource mode the name of the source plus _ are prepended to the UIDL
	foreach($aMessages as $aData) {
		if((strcmp($sKey, $aData['uidl']) == 0) || (strcmp($sMultiSourceKey, $aData['uidl']) == 0)) {
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
	
	$oPage = new ajax_page('');

	$sOperation = utils::ReadParam('operation', '');
	$iMailInboxId = utils::ReadParam('id', 0, false, 'raw_data');
	if(empty($iMailInboxId)) {
		$oInbox = null;
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
		case 'mailbox_delete_messages':
		
			$aUIDLs = utils::ReadParam('aUIDLs', [], false, 'raw_data');
			if(count($aUIDLs) > 0) {
				
				$sOQL = 'SELECT EmailReplica WHERE uidl IN ('.implode(',', CMDBSource::Quote($aUIDLs)).')';
				$oReplicaSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL));
				$oReplicaSet->OptimizeColumnLoad(['EmailReplica' => ['uidl']]);
				$aReplicas = [];
				while($oReplica = $oReplicaSet->Fetch()) {
					$aReplicas[$oReplica->Get('uidl')] = $oReplica;
				}
				if ($sOperation == 'mailbox_delete_messages') {
					// Delete the actual email from the mailbox
					$oSource = $oInbox->GetEmailSource();
					$aMessages = $oSource->GetListing();
				}
				foreach($aUIDLs as $sUIDL) {
					if(array_key_exists($sUIDL, $aReplicas)) {
						// A replica exists for the given email, let's remove it
						$aReplicas[$sUIDL]->DBDelete();
					}
					if($sOperation == 'mailbox_delete_messages') {
						$idx = FindMessageIDFromUIDL($aMessages, $sUIDL, $oSource);
						if ($idx !== false) {
							// Delete the actual email from the mailbox
							$oSource->DeleteMessage($idx);
						}
					}
				}
				if ($sOperation == 'mailbox_delete_messages') {
					$oSource->Disconnect();
				}
			}
			GetMailboxContent($oPage, $oInbox);
			break;

		case 'mailbox_ignore_messages':
			$aUIDLs = utils::ReadParam('aUIDLs', [], false, 'raw_data');
			if(count($aUIDLs) > 0) {
				$sOQL = 'SELECT EmailReplica WHERE uidl IN ('.implode(',', CMDBSource::Quote($aUIDLs)).')';
				$oReplicaSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL));
				$aReplicas = [];
				/** @var \DBObject $oReplica */
				while ($oReplica = $oReplicaSet->Fetch()) {
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
					$oEmailReplica->Set('mailbox_path', $oSource->GetMailbox());
					foreach ($aMessages as $iMessage => $aMessage) {
						if ($oSource->GetName().'_'.$aMessage['uidl'] == $sUIDL) {
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

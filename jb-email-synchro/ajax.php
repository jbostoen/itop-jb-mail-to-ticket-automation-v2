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
 * @copyright   Copyright (c) 2013-2026 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

require_once('../../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/ajaxwebpage.class.inc.php');

use Combodo\iTop\Application\WebPage\AjaxPage;
use JeffreyBostoenExtensions\MailToTicket\MessageHandler;

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

			 // Note: This may differ from $oSource->GetMessagesCount(); as messages with errors could be skipped 
			 // (At least with the Laminas PHP library used in previous versions of this extension).
			
			/** @var MessageHandler[] $aMessages */
			$aMessages = $oSource->GetListing();

			// - In a previous iteration with a different IMAP engine, corruption could occur.
			//   Extra filtering was applied at this step.
			$iTotalMsgOkCount = count($aMessages);
			
			if($iStartIndex < 0 || $iMaxCount <= 0) {
				// Don't process, invalid indexes
				$oPage->add('Invalid start or max.');
				$oSource->Disconnect();
				return;
			}

			// Avoid user specifying a higher number (start) than the total message number count
			$iStart = $iStartIndex;
		
			// Avoid user specifying a higher number (start + count) than the total mesage number count
			// The largest index is (message count - 1), since messages are retrieved by index (starting at 0)
			// Bound against $iTotalMsgOkCount (the number of entries actually present in $aMessages/$aMessageIndexes),
			// not $iTotalMsgCount (a separate IMAP STATUS count that can differ), to avoid indexing past the array.
			$iEnd = min($iStart + $iMaxCount - 1, $iTotalMsgOkCount - 1);
			
		}
		catch(Exception $e) {
			$oInbox->Trace('Failed to initialize the mailbox: '.$oInbox->GetName().'. Reason: '.$e->getMessage());
			$oPage->p('Failed to initialize the mailbox: '.$oInbox->GetName().'. Reason: '.$e->getMessage());
			if(isset($oSource)) {
				$oSource->Disconnect();
			}
			return;

		}

		
		
		if($iTotalMsgCount > 0) {
			
			// Sort the items, but keep the original index.
			uasort($aMessages, function(MessageHandler $oHandlerA, MessageHandler $oHandlerB) {
				return $oHandlerA->GetTimestamp() <=> $oHandlerB->GetTimestamp();
			});
			
			// Get the corresponding EmailReplica object for each message
			$aInternalIdentifiers = array_map(function(MessageHandler $oMsgHandler) {
				return $oMsgHandler->GetInternalIdentifier();
			}, $aMessages);
			
			
			/** @var int $iMsgOkCount The number of readable emails between start and end index. */
			$iMsgOkCount = 0;

			/** @var int $iProcessedCount The number of processed (non-corrupted) emails / Existing e-mail replicas. */
			$iProcessedCount = 0;

			if($iTotalMsgOkCount > 0) {
				
				$sOQL = '
					SELECT EmailReplica 
					WHERE 
						uidl IN ('.implode(',', CMDBSource::Quote($aInternalIdentifiers)).') 
						AND mailbox_path = ' . CMDBSource::Quote($oInbox->Get('mailbox')).' 
						AND mailbox_id = '.$oInbox->GetKey();
					
				$oInbox->Trace("Searching EmailReplica objects: $sOQL");
				$oReplicaSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL));
				$oReplicaSet->OptimizeColumnLoad(['EmailReplica' => [
					'uidl',
					'ticket_id',
					'status',
					'error_message'
				]]);
				$iProcessedCount = $oReplicaSet->Count();

				/** @var array $aProcessed Array containing items with the following properties:
				 * - status
				 * - ticket ID
				 * - error_message
				 * - id (of the EmailReplica)
				 */
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
					'ticket' => ['label' => Dict::S('MailInbox:RelatedTicket'), 'description' => ''],
					'error' => ['label' => Dict::S('MailInbox:ErrorMessage'), 'description' => ''],
					'details' => ['label' => Dict::S('MailInbox:MessageDetails'), 'description' => ''],
					'replicaLink' => ['label' => Dict::S('MailInbox:ReplicaLink'), 'description' => ''],
					'uid' => ['label' => Dict::S('MailInbox:UID'), 'description' => ''],
					'internalId' => ['label' => Dict::S('MailInbox:InternalId'), 'description' => ''],
				];

				$aData = [];
				$aMessageIndexes = array_keys($aMessages);
				
				$iCurrentIndex = $iStart;
				while($iCurrentIndex <= $iEnd) {
					
					// Obtain the actual index for the message (take Nth index)
					$iMessage = $aMessageIndexes[$iCurrentIndex];

					/** @var MessageHandler $oMsgHandler */
					$oMsgHandler = $aMessages[$iMessage];

					$oRawEmail = $oSource->GetMessage($oMsgHandler);

					
					if(is_null($oRawEmail)) {
						
						// Just ignore.
						// Adding a dummy record in the table could lead to actions being performed which should not be available for this corrupted message.
						
					}
					else {
						
						$iMsgOkCount += 1;
						$oEmail = $oRawEmail->Decode($oSource->GetPartsOrder());

						$sUIDL = $oMsgHandler->GetInternalIdentifier();
						$sStatus = Dict::S('MailInbox:Status/New');
						$sLink = '';
						$sErrorMsg = '';
						$sDetailsLink = '';
						$sReplicaLink = '';
						if(array_key_exists($sUIDL, $aProcessed)) {
							
							switch($aProcessed[$sUIDL]['status']) {
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
							$sErrorMsg = $aProcessed[$sUIDL]['error_message'];
							if($aProcessed[$sUIDL]['ticket_id'] > 0) {
								$sTicketUrl = ApplicationContext::MakeObjectUrl($oInbox->Get('target_class'), $aProcessed[$sUIDL]['ticket_id']);
								$sLink = '<a href="'.$sTicketUrl.'">'.$oInbox->Get('target_class').'::'.$aProcessed[$sUIDL]['ticket_id'].'</a>';
							}
							$aArgs = ['operation' => 'message_details', 'sUIDL' => $sUIDL, 'mailbox_id' => $oInbox->GetKey()];
							$sDetailsURL = utils::GetAbsoluteUrlModulePage(basename(dirname(__FILE__)), 'details.php', $aArgs);
							$sDetailsLink = '<a href="'.$sDetailsURL.'">'.Dict::S('MailInbox:MessageDetails').'</a>';

							$sReplicaLink = sprintf('<a href="%1$s">%2$s</a>',
								ApplicationContext::MakeObjectUrl(EmailReplica::class, $aProcessed[$sUIDL]['id']),
								$aProcessed[$sUIDL]['id']
							);

						}
						$aData[] = [
							'checkbox' => '<input type="checkbox" class="mailbox_item" value="'.htmlentities($sUIDL, ENT_QUOTES, 'UTF-8').'"/>',
							'status' => $sStatus,
							'date' => $oEmail->sDate,
							'from' => $oEmail->sCallerEmail,
							'subject' => $oEmail->sSubject,
							'ticket' => $sLink,
							'error' => $sErrorMsg,
							'details' => $sDetailsLink,
							'uid' => $oMsgHandler->GetUid(),
							'internalId' => $oMsgHandler->GetInternalIdentifier(),
							'replicaLink' => $sReplicaLink,
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
				
				/** @var array $aTableConfig Associative array with config for the table. */
				/** @var array $aData Associative array with data for the table. */
				$oPage->table($aTableConfig, $aData);

				$sLblWithSelected = Dict::S('MailInbox:WithSelectedDo');
				$sLblReset = Dict::S('MailInbox:ResetStatus');
				$sLblIgnore = Dict::S('MailInbox:IgnoreMessage');
				$sLblDelete = Dict::S('MailInbox:DeleteMessage');

				$oPage->add(<<<HTML
					<div>
						<img alt="" src="../images/tv-item-last.gif" style="vertical-align:bottom;margin-left:10px;"/>&nbsp;{$sLblWithSelected}&nbsp;&nbsp;
						<button class="mailbox_button ibo-button ibo-is-regular ibo-is-neutral" id="mailbox_reset_status">{$sLblReset}</button>&nbsp;&nbsp;
						<button class="mailbox_button ibo-button ibo-is-regular ibo-is-neutral" id="mailbox_ignore_messages">{$sLblIgnore}</button>&nbsp;&nbsp;
						<button class="mailbox_button ibo-button ibo-is-regular ibo-is-danger" id="mailbox_delete_messages">{$sLblDelete}</button>
					</div>
				HTML);
			
			} 
			else {
				
				// Contrary to the original Combodo message: this could mean there are actually e-mails in the mailbox, but they can not be processed.
				$oPage->p(Dict::Format('MailInbox:NoValidEmailsFound'));

			}

		}
		else {
			$oPage->p(Dict::Format('MailInbox:EmptyMailbox'));
		}

		if(isset($oSource)) {
			$oSource->Disconnect();
		}
	}
	else {
		$oPage->P(Dict::S('UI:ObjectDoesNotExist'));
	}
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
		case 'mailbox_delete_messages':

			if($oInbox === null) {
				$oPage->add('Invalid or missing mailbox id.');
				break;
			}

			// As for the replicas, consider this:
			// - There could be multiple mailboxes that received the same message (and UIDL).
			// - There could be a copy of the same message (same UIDL) in a different folder on the same mailbox (different "Mailbox" config in iTop).
		
			$aInternalIdentifiers = utils::ReadParam('aInternalIdentifiers', [], false, 'raw_data');
			if(count($aInternalIdentifiers) > 0) {
				$sOQL = 'SELECT EmailReplica WHERE uidl IN ('.implode(',', CMDBSource::Quote($aInternalIdentifiers)).') AND mailbox_id = '.$oInbox->GetKey(); 
				$oReplicaSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL));
				$oReplicaSet->OptimizeColumnLoad(['EmailReplica' => ['uidl']]);
				$aReplicas = [];
				while($oReplica = $oReplicaSet->Fetch()) {
					$aReplicas[$oReplica->Get('uidl')] = $oReplica;
				}
				if($sOperation === 'mailbox_delete_messages') {
					// Delete the actual email from the mailbox.
					$oSource = $oInbox->GetEmailSource();
					$aMessages = $oSource->GetListing();
				}
				
				foreach($aInternalIdentifiers as $sUIDL) {
					$bDeletedFromMailbox = true;
					if($sOperation === 'mailbox_delete_messages') {

						/** @var MessageHandler[] $aMessages */
						$oMsgHandler = array_values(array_filter($aMessages, function(MessageHandler $oMsgHandler) use ($sUIDL) {
							return $oMsgHandler->GetInternalIdentifier() === $sUIDL;
						}))[0] ?? null;

						if($oMsgHandler) {
							// Delete the actual email from the mailbox.
							$bDeletedFromMailbox = $oMsgHandler->DeleteMessage();
						}
					}
					if(!$bDeletedFromMailbox) {
						// The IMAP delete failed: keep the replica, otherwise the still-present message would
						// be treated as brand-new (and potentially recreate a ticket) on the next cron run.
						$sMessage = "Could not delete message from the mailbox: {$sUIDL}. The replica was kept.";
						$oInbox->Trace($sMessage);
						$oPage->add($sMessage);
						continue;
					}
					if(array_key_exists($sUIDL, $aReplicas)) {
						// A replica exists for the given email, let's remove it.
						$aReplicas[$sUIDL]->DBDelete();
					}
				}
				if ($sOperation === 'mailbox_delete_messages') {
					/** @var EmailSource $oSource */
					$oSource->Disconnect();
				}
			}
			GetMailboxContent($oPage, $oInbox);
			break;

		case 'mailbox_ignore_messages':

			if($oInbox === null) {
				$oPage->add('Invalid or missing mailbox id.');
				break;
			}

			$aInternalIdentifiers = utils::ReadParam('aInternalIdentifiers', [], false, 'raw_data');
			$aReplicas = [];

			if(count($aInternalIdentifiers) > 0) {
				$sOQL = 'SELECT EmailReplica WHERE uidl IN ('.implode(',', CMDBSource::Quote($aInternalIdentifiers)).') AND mailbox_id = '.$oInbox->GetKey(); 
				$oReplicaSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL));
				/** @var DBObject $oReplica */
				while ($oReplica = $oReplicaSet->Fetch()) {
					$oReplica->Set('status', 'ignored');
					$oReplica->DBUpdate();
					$aReplicas[$oReplica->Get('uidl')] = true;
				}
			}

			if(count($aReplicas) < count($aInternalIdentifiers)) {
				// Some "New" messages will be marked as "ignore".
				// Add them to the database.
				$oSource = $oInbox->GetEmailSource();
				$aMessages = $oSource->GetListing();
				$aNotFound = [];
				foreach($aInternalIdentifiers as $sUIDL) {
					if (isset($aReplicas[$sUIDL])) {
						// Already processed
						continue;
					}
					$oEmailReplica = new EmailReplica();
					$oEmailReplica->Set('uidl', $sUIDL);
					$oEmailReplica->Set('status', 'ignored');
					$oEmailReplica->Set('mailbox_id', $oInbox->GetKey());
					$oEmailReplica->Set('mailbox_path', $oSource->GetMailbox());
					$bFound = false;
					/** @var MessageHandler $oMsgHandler */
					foreach ($aMessages as $iMessage => $oMsgHandler) {
						if($oMsgHandler->GetInternalIdentifier() == $sUIDL) {
							$oEmailReplica->Set('message_id', $iMessage);
							$oEmailReplica->DBInsert();
							$bFound = true;
							break;
						}
					}
					if(!$bFound) {
						// The message is no longer in the mailbox's listing (e.g. a concurrent cron run
						// already moved/deleted it): there is nothing left to mark as "ignored".
						$aNotFound[] = $sUIDL;
					}
				}

				if($aNotFound !== []) {
					$sMessage = 'Could not ignore '.count($aNotFound).' message(s), no longer found in the mailbox listing: '.implode(', ', $aNotFound);
					$oInbox->Trace($sMessage);
					$oPage->add($sMessage);
				}

				/** @var EmailSource $oSource */
				$oSource->Disconnect();
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

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

			if($iStartIndex < 0 || $iMaxCount <= 0) {
				// Don't process, invalid indexes
				$oPage->add('Invalid start or max.');
				$oSource->Disconnect();
				return;
			}

			// - Only the requested page is read from the mailbox: the server sorts (when supported)
			//   and the headers are fetched for the displayed messages only, not for the entire folder.
			//   The number of messages in the listing can differ from the message count of the folder (e.g. messages flagged as deleted).
			/** @var array{total: int, messages: MessageHandler[], unreadable: int} $aListingPage */
			$aListingPage = $oSource->GetListingPage($iStartIndex, $iMaxCount);

			/** @var MessageHandler[] $aMessages Message handlers of the displayed page. */
			$aMessages = $aListingPage['messages'];

		}
		catch(Throwable $e) {
			$oInbox->Trace('Failed to initialize the mailbox: '.$oInbox->GetName().'. Reason: '.$e->getMessage());
			$oPage->p(htmlspecialchars('Failed to initialize the mailbox: '.$oInbox->GetName().'. Reason: '.$e->getMessage(), ENT_QUOTES, 'UTF-8'));
			if(isset($oSource)) {
				$oSource->Disconnect();
			}
			return;

		}

		
		
		if($iTotalMsgCount > 0) {
			
			// Get the corresponding EmailReplica object for each displayed message
			$aInternalIdentifiers = array_map(function(MessageHandler $oMsgHandler) {
				return $oMsgHandler->GetInternalIdentifier();
			}, $aMessages);
			
			
			/** @var int $iMsgOkCount The number of readable emails on the displayed page. */
			$iMsgOkCount = 0;

			/** @var int $iProcessedCount The number of processed emails (existing e-mail replicas) among the readable emails on the displayed page. */
			$iProcessedCount = 0;

			/** @var int $iUnreadableCount The number of emails on the displayed page which could not be read. */
			$iUnreadableCount = $aListingPage['unreadable'];

			if(count($aMessages) > 0) {
				
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
				
				/** @var MessageHandler $oMsgHandler */
				foreach($aMessages as $oMsgHandler) {

					// - Date, sender and subject are read from the headers which were fetched for this page; the body is not needed.
					$aSummary = $oSource->GetMessageSummary($oMsgHandler);

					if(is_null($aSummary)) {
						
						// Just ignore.
						// Adding a dummy record in the table could lead to actions being performed which should not be available for this corrupted message.
						$iUnreadableCount += 1;
						
					}
					else {
						
						$iMsgOkCount += 1;

						$sUIDL = $oMsgHandler->GetInternalIdentifier();
						$sStatus = Dict::S('MailInbox:Status/New');
						$sLink = '';
						$sErrorMsg = '';
						$sDetailsLink = '';
						$sReplicaLink = '';
						if(array_key_exists($sUIDL, $aProcessed)) {
							
							$iProcessedCount += 1;

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
							// Decoded straight from the untrusted e-mail: the table renders cell values as
							// raw HTML, so these must be escaped to prevent stored XSS via a crafted header.
							'date' => htmlentities($aSummary['date'], ENT_QUOTES, 'UTF-8'),
							'from' => htmlentities($aSummary['from'], ENT_QUOTES, 'UTF-8'),
							'subject' => htmlentities($aSummary['subject'], ENT_QUOTES, 'UTF-8'),
							'ticket' => $sLink,
							'error' => htmlentities($sErrorMsg, ENT_QUOTES, 'UTF-8'),
							'details' => $sDetailsLink,
							'uid' => $oMsgHandler->GetUid(),
							// The internal identifier is usually the Message-ID header, which is controlled by the sender.
							'internalId' => htmlentities($oMsgHandler->GetInternalIdentifier(), ENT_QUOTES, 'UTF-8'),
							'replicaLink' => $sReplicaLink,
						];
						
					}
					
				}
			
			}

			if($iTotalMsgCount > 0) {
				
				// If we have messages in the mailbox, even if none can be read (meaning they can't be displayed), we are displaying the mailbox stats
				// This will greatly help the user understanding what's going on !
				// Note: the numbers of unprocessed and unreadable messages only apply to the displayed page, as only that part of the listing is read.
				$oPage->p(Dict::Format('MailInbox:Z_DisplayedThereAre_X_Msg_Y_NewInTheMailbox',
					$iMsgOkCount,
					$iTotalMsgCount,
					($iMsgOkCount - $iProcessedCount),
					$iUnreadableCount)
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

			// CSRF protection: these operations delete/mutate data based solely on the admin
			// session cookie, so a cross-site request forging this POST must be rejected.
			if(!utils::IsTransactionValid(utils::ReadParam('transaction_id', '', false, 'raw_data'), false)) {
				$oPage->add(Dict::S('UI:Error:InvalidToken'));
				break;
			}

			if($oInbox === null) {
				$oPage->add('Invalid or missing mailbox id.');
				break;
			}

			// As for the replicas, consider this:
			// - There could be multiple mailboxes that received the same message (and UIDL).
			// - There could be a copy of the same message (same UIDL) in a different folder on the same mailbox (different "Mailbox" config in iTop).
		
			$aInternalIdentifiers = utils::ReadParam('aInternalIdentifiers', [], false, 'raw_data');
			if(count($aInternalIdentifiers) > 0) {
				$sOQL = 'SELECT EmailReplica WHERE uidl IN ('.implode(',', CMDBSource::Quote($aInternalIdentifiers)).') AND mailbox_path = '.CMDBSource::Quote($oInbox->Get('mailbox')).' AND mailbox_id = '.$oInbox->GetKey();
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
						else {
							// The message is no longer in the mailbox's listing (e.g. a concurrent cron run
							// already moved/deleted it): nothing was actually deleted, so the replica must
							// be kept - otherwise, if the message is in fact still present, the next cron
							// run would treat it as brand-new (and potentially recreate a ticket).
							$bDeletedFromMailbox = false;
						}
					}
					if(!$bDeletedFromMailbox) {
						// The IMAP delete failed, or the message was not found in the mailbox listing:
						// keep the replica, otherwise a still-present message would be treated as
						// brand-new (and potentially recreate a ticket) on the next cron run.
						$sMessage = "Could not delete message from the mailbox: {$sUIDL}. The replica was kept.";
						$oInbox->Trace($sMessage);
						$oPage->add(htmlspecialchars($sMessage, ENT_QUOTES, 'UTF-8').'<br/>');
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

			// CSRF protection: this operation mutates data based solely on the admin session
			// cookie, so a cross-site request forging this POST must be rejected.
			if(!utils::IsTransactionValid(utils::ReadParam('transaction_id', '', false, 'raw_data'), false)) {
				$oPage->add(Dict::S('UI:Error:InvalidToken'));
				break;
			}

			if($oInbox === null) {
				$oPage->add('Invalid or missing mailbox id.');
				break;
			}

			$aInternalIdentifiers = utils::ReadParam('aInternalIdentifiers', [], false, 'raw_data');
			$aReplicas = [];

			if(count($aInternalIdentifiers) > 0) {
				$sOQL = 'SELECT EmailReplica WHERE uidl IN ('.implode(',', CMDBSource::Quote($aInternalIdentifiers)).') AND mailbox_path = '.CMDBSource::Quote($oInbox->Get('mailbox')).' AND mailbox_id = '.$oInbox->GetKey();
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
					$oPage->add(htmlspecialchars($sMessage, ENT_QUOTES, 'UTF-8').'<br/>');
				}

				/** @var EmailSource $oSource */
				$oSource->Disconnect();
			}
			GetMailboxContent($oPage, $oInbox);
			break;

	}
	$oPage->output();
}
catch(Throwable $e) {
	$oPage->SetContentType('text/html');
	$oPage->add(htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
	$oPage->output();
}

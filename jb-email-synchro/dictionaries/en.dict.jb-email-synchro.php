<?php
/**
 * Localized data
 *
 * @copyright Copyright (c) 2010-2025 Combodo SARL
 * @license	http://opensource.org/licenses/AGPL-3.0
 *
 * This file is part of iTop.
 *
 * iTop is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * iTop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with iTop. If not, see <http://www.gnu.org/licenses/>
 */

Dict::Add('EN US', 'English', 'English', array(
	// Dictionary entries go here
	'Class:MailInboxBase' => 'Mail Inbox',
	'Class:MailInboxBase+' => 'Source of incoming e-mails',

	'Class:MailInboxBase/Attribute:server' => 'Mail Server',
	'Class:MailInboxBase/Attribute:server+' => 'The IP address or fully qualified hostname of the mail server',
	'Class:MailInboxBase/Attribute:mailbox' => 'Mailbox folder (for IMAP)',
	'Class:MailInboxBase/Attribute:mailbox+' => 'Hint: IMAP folders are case sensitive. Specify subfolders using slashes: Deleted items/SomeExample. If omitted the default (root) mailbox will be scanned',
	'Class:MailInboxBase/Attribute:login' => 'Login',
	'Class:MailInboxBase/Attribute:login+' => 'The name of the mail account used for connecting to the mailbox',
	'Class:MailInboxBase/Attribute:password' => 'Password',
	'Class:MailInboxBase/Attribute:protocol' => 'Protocol',
	'Class:MailInboxBase/Attribute:protocol/Value:imap' => 'IMAP',
	'Class:MailInboxBase/Attribute:port' => 'Port',
	'Class:MailInboxBase/Attribute:port+' => 'Default ports: 143 for IMAP - 993 for secured IMAP.',
	'Class:MailInboxBase/Attribute:active' => 'Active',
	'Class:MailInboxBase/Attribute:active+' => 'Only if set to "Yes", the inbox will be polled. Otherwise, it will not be polled.',
	'Class:MailInboxBase/Attribute:active/Value:yes' => 'Yes',
	'Class:MailInboxBase/Attribute:active/Value:no' => 'No',
	'Class:MailInboxBase/Attribute:imap_options' => 'IMAP options',
	'Class:MailInboxBase/Attribute:imap_options+' => 'IMAP options can be specified, line by line. They are processed in that order.',
	'Class:MailInboxBase/Attribute:disable_authenticator' => 'Disable authenticator',
	'Class:MailInboxBase/Attribute:disable_authenticator+' => 'In the legacy implementation, it is possible to disable certain authentication mechanisms. In most cases, leave this empty.',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI' => 'GSSAPI',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI+' => 'GSSAPI (Kerberos)',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM+' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN' => 'PLAIN',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN+' => 'PLAIN',
	
	'MailInbox:MailboxContent' => 'Mailbox Content',
	'MailInbox:MailboxContent:ConfirmMessage' => 'Are you sure?',
	'MailInbox:NoValidEmailsFound' => 'No valid e-mails found in this mailbox.',
	'MailInbox:Z_DisplayedThereAre_X_Msg_Y_NewInTheMailbox' => '%1$d e-mail(s) displayed. There are %2$d e-mail(s) in the mailbox (%3$d unprocessed and %4$d unreadable/corrupt).',
	'MailInbox:UnprocessableMessages' => 'There are %1$s e-mail(s) which can not be listed due to a technical issue.',
	'MailInbox:MaxAllowedPacketTooSmall' => 'MySQL parameter max_allowed_packet in "my.ini" is too small: %1$s. The recommended value is at least: %2$s',
	'MailInbox:Status' => 'Status',
	'MailInbox:Subject' => 'Subject',
	'MailInbox:From' => 'From',
	'MailInbox:Date' => 'Date',
	'MailInbox:RelatedTicket' => 'Related Ticket',
	'MailInbox:ErrorMessage' => 'Error Message',
	'MailInbox:Status/Processed' => 'Already Processed',
	'MailInbox:Status/New' => 'New',
	'MailInbox:Status/Error' => 'Error',
    'MailInbox:Status/Undesired' => 'Undesired',
	'MailInbox:Status/Ignored' => 'Ignored',
		
	'MailInbox:Login/ServerMustBeUnique' => 'The combination Login (%1$s) and Server (%2$s) is already configured for another Mail Inbox.',
	'MailInbox:Login/Server/MailboxMustBeUnique' => 'The combination Login (%1$s), Server (%2$s) and Mailbox (%3$s) is already configured for another Mail Inbox',
	'MailInbox:Display_X_eMailsStartingFrom_Y' => 'Display %1$s e-mail(s), starting from %2$s.',
	'MailInbox:WithSelectedDo' => 'With the selected e-mails: ',
	'MailInbox:ResetStatus' => 'Reset status',
	'MailInbox:DeleteMessage' => 'Delete email',
	'MailInbox:IgnoreMessage' => 'Ignore email',

	'MailInbox:MessageDetails' => 'Message details',
	'MailInbox:DownloadEml' => 'Download .eml file',
	'Class:TriggerOnMailUpdate' => 'Trigger (when updated by mail)',
	'Class:TriggerOnMailUpdate+' => 'Trigger activated when a ticket is updated by processing an incoming email',
	
	'MailInbox:EmptyMailbox' => 'Empty mailbox',
	
	'Class:EmailReplica' => 'Email Replica',
	'Class:EmailReplica/Attribute:ticket_id' => 'Ticket ID',
	'Class:EmailReplica/Attribute:uidl' => 'UIDL',
	'Class:EmailReplica/Attribute:mailbox_path' => 'Mailbox path',
	'Class:EmailReplica/Attribute:message_id' => 'Message ID',
	'Class:EmailReplica/Attribute:message_text' => 'Message text',
	'Class:EmailReplica/Attribute:references' => 'References',
	'Class:EmailReplica/Attribute:thread_index' => 'Thread index',
	'Class:EmailReplica/Attribute:message_date' => 'Message date',
	'Class:EmailReplica/Attribute:last_seen' => 'Last seen',
	'Class:EmailReplica/Attribute:status' => 'Status',
	'Class:EmailReplica/Attribute:status/Value:error' => 'Error',
	'Class:EmailReplica/Attribute:status/Value:ignored' => 'Ignored',
	'Class:EmailReplica/Attribute:status/Value:ok' => 'OK',
	'Class:EmailReplica/Attribute:status/Value:undesired' => 'Undesired',
	'Class:EmailReplica/Attribute:error_message' => 'Error message',
	'Class:EmailReplica/Attribute:error_trace' => 'Error trace',
	'Class:EmailReplica/Attribute:contents' => 'Contents',
	'Class:EmailReplica/Attribute:mailbox_id' => 'Mailbox ID',
	
	
));

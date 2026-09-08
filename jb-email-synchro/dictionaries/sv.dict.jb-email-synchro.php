<?php
/**
 * Localized data
 *
 * @copyright Copyright (c) 2010-2026 Combodo SARL
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

Dict::Add('SV SE', 'Swedish', 'Svenska', array(
	// Dictionary entries go here
	'Class:MailInboxBase' => 'E-postkorg',
	'Class:MailInboxBase+' => 'Källa för inkommande e-post',

	'Class:MailInboxBase/Attribute:server' => 'E-postserver',
	'Class:MailInboxBase/Attribute:server+' => 'IP-adressen eller det fullständiga värdnamnet för e-postservern',
	'Class:MailInboxBase/Attribute:mailbox' => 'Brevlådemapp (för IMAP)',
	'Class:MailInboxBase/Attribute:mailbox+' => 'Obs: IMAP-mappar är skiftlägeskänsliga. Ange undermappar med snedstreck: Papperskorg/Exempel. Om utelämnat genomsöks standardbrevlådan (roten)',
	'Class:MailInboxBase/Attribute:login' => 'Inloggning',
	'Class:MailInboxBase/Attribute:login+' => 'Namnet på e-postkontot som används för att ansluta till brevlådan',
	'Class:MailInboxBase/Attribute:password' => 'Lösenord',
	'Class:MailInboxBase/Attribute:protocol' => 'Protokoll',
	'Class:MailInboxBase/Attribute:protocol/Value:imap' => 'IMAP',
	'Class:MailInboxBase/Attribute:port' => 'Port',
	'Class:MailInboxBase/Attribute:port+' => 'Standardportar: 143 för IMAP - 993 för säker IMAP.',
	'Class:MailInboxBase/Attribute:active' => 'Aktiv',
	'Class:MailInboxBase/Attribute:active+' => 'Endast om inställd till "Ja" kommer brevlådan att avläsas. Annars kommer den inte att avläsas.',
	'Class:MailInboxBase/Attribute:active/Value:yes' => 'Ja',
	'Class:MailInboxBase/Attribute:active/Value:no' => 'Nej',
	'Class:MailInboxBase/Attribute:imap_options' => 'IMAP-inställningar',
	'Class:MailInboxBase/Attribute:imap_options+' => 'IMAP-inställningar kan anges rad för rad. De behandlas i den ordningen.',
	'Class:MailInboxBase/Attribute:disable_authenticator' => 'Inaktivera autentisering',
	'Class:MailInboxBase/Attribute:disable_authenticator+' => 'I den äldre implementeringen är det möjligt att inaktivera vissa autentiseringsmekanismer. Lämna vanligtvis detta tomt.',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI' => 'GSSAPI',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI+' => 'GSSAPI (Kerberos)',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM+' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN' => 'PLAIN',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN+' => 'PLAIN',
	'Class:MailInboxBase/Attribute:folder_delimiter' => 'Mappavgränsare',
	'Class:MailInboxBase/Attribute:folder_delimiter+' => 'Tecknet som e-postleverantören använder för att ange undermappar. Vanligtvis "/" (Google, Microsoft) eller "." (Hetzner).',

	'MailInbox:MailboxContent' => 'Brevlådans innehåll',
	'MailInbox:MailboxContent:ConfirmMessage' => 'Är du säker?',
	'MailInbox:NoValidEmailsFound' => 'Inga giltiga e-postmeddelanden hittades i denna brevlåda.',
	'MailInbox:Z_DisplayedThereAre_X_Msg_Y_NewInTheMailbox' => '%1$d e-postmeddelande(n) visade. Det finns %2$d e-postmeddelande(n) i brevlådan (%3$d obehandlade och %4$d oläsbara/skadade).',
	'MailInbox:UnprocessableMessages' => 'Det finns %1$s e-postmeddelande(n) som inte kan listas på grund av ett tekniskt problem.',
	'MailInbox:MaxAllowedPacketTooSmall' => 'MySQL-parametern max_allowed_packet i "my.ini" är för liten: %1$s. Rekommenderat värde är minst: %2$s',
	'MailInbox:Status' => 'Status',
	'MailInbox:Subject' => 'Ämne',
	'MailInbox:From' => 'Från',
	'MailInbox:Date' => 'Datum',
	'MailInbox:RelatedTicket' => 'Relaterat ärende',
	'MailInbox:ErrorMessage' => 'Felmeddelande',
	'MailInbox:Status/Processed' => 'Redan behandlad',
	'MailInbox:Status/New' => 'Ny',
	'MailInbox:Status/Error' => 'Fel',
    'MailInbox:Status/Undesired' => 'Oönskad',
	'MailInbox:Status/Ignored' => 'Ignorerad',
	'MailInbox:ReplicaLink' => 'Replik',
	'MailInbox:InternalId' => 'Internt ID',
	'MailInbox:UID' => 'UID',

	'MailInbox:Login/ServerMustBeUnique' => 'Kombinationen av inloggning (%1$s) och server (%2$s) är redan konfigurerad för en annan brevlåda.',
	'MailInbox:Login/Server/MailboxMustBeUnique' => 'Kombinationen av inloggning (%1$s), server (%2$s) och brevlåda (%3$s) är redan konfigurerad för en annan brevlåda',
	'MailInbox:Display_X_eMailsStartingFrom_Y' => 'Visar %1$s e-postmeddelande(n), med start från %2$s.',
	'MailInbox:WithSelectedDo' => 'Med de valda e-postmeddelandena: ',
	'MailInbox:ResetStatus' => 'Återställ status',
	'MailInbox:DeleteMessage' => 'Radera e-post',
	'MailInbox:IgnoreMessage' => 'Ignorera e-post',

	'MailInbox:MessageDetails' => 'Meddelandedetaljer',
	'MailInbox:DownloadEml' => 'Ladda ned .eml-fil',
	'Class:TriggerOnMailUpdate' => 'Utlösare (vid uppdatering via e-post)',
	'Class:TriggerOnMailUpdate+' => 'Utlösare som aktiveras när ett ärende uppdateras genom behandling av ett inkommande e-postmeddelande',

	'MailInbox:EmptyMailbox' => 'Tom brevlåda',

	'Class:EmailReplica' => 'E-postreplik',
	'Class:EmailReplica/Attribute:ticket_id' => 'Ärende-ID',
	'Class:EmailReplica/Attribute:uidl' => 'UIDL',
	'Class:EmailReplica/Attribute:mailbox_path' => 'Brevlådans sökväg',
	'Class:EmailReplica/Attribute:message_id' => 'Meddelande-ID',
	'Class:EmailReplica/Attribute:message_text' => 'Meddelandetext',
	'Class:EmailReplica/Attribute:references' => 'Referenser',
	'Class:EmailReplica/Attribute:thread_index' => 'Trådindex',
	'Class:EmailReplica/Attribute:message_date' => 'Meddelandedatum',
	'Class:EmailReplica/Attribute:last_seen' => 'Senast sedd',
	'Class:EmailReplica/Attribute:status' => 'Status',
	'Class:EmailReplica/Attribute:status/Value:error' => 'Fel',
	'Class:EmailReplica/Attribute:status/Value:ignored' => 'Ignorerad',
	'Class:EmailReplica/Attribute:status/Value:ok' => 'OK',
	'Class:EmailReplica/Attribute:status/Value:undesired' => 'Oönskad',
	'Class:EmailReplica/Attribute:error_message' => 'Felmeddelande',
	'Class:EmailReplica/Attribute:error_trace' => 'Felspårning',
	'Class:EmailReplica/Attribute:contents' => 'Innehåll',
	'Class:EmailReplica/Attribute:mailbox_id' => 'Brevlåde-ID',


));

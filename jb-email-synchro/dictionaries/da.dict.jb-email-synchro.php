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

Dict::Add('DA DK', 'Danish', 'Dansk', array(
	// Dictionary entries go here
	'Class:MailInboxBase' => 'Mailboks',
	'Class:MailInboxBase+' => 'Kilde til indgående e-mails',

	'Class:MailInboxBase/Attribute:server' => 'Mailserver',
	'Class:MailInboxBase/Attribute:server+' => 'IP-adressen eller det fuldt kvalificerede værtsnavn på mailserveren',
	'Class:MailInboxBase/Attribute:mailbox' => 'Mailboksmappe (til IMAP)',
	'Class:MailInboxBase/Attribute:mailbox+' => 'Bemærk: IMAP-mapper skelner mellem store og små bogstaver. Angiv undermapper med skråstreg: Slettet post/Eksempel. Hvis udeladt, gennemsøges standardmailboksen (roden)',
	'Class:MailInboxBase/Attribute:login' => 'Login',
	'Class:MailInboxBase/Attribute:login+' => 'Navnet på den mailkonto, der bruges til at oprette forbindelse til mailboksen',
	'Class:MailInboxBase/Attribute:password' => 'Adgangskode',
	'Class:MailInboxBase/Attribute:protocol' => 'Protokol',
	'Class:MailInboxBase/Attribute:protocol/Value:imap' => 'IMAP',
	'Class:MailInboxBase/Attribute:port' => 'Port',
	'Class:MailInboxBase/Attribute:port+' => 'Standardporte: 143 for IMAP - 993 for sikret IMAP.',
	'Class:MailInboxBase/Attribute:active' => 'Aktiv',
	'Class:MailInboxBase/Attribute:active+' => 'Kun hvis sat til "Ja" bliver indbakken tjekket. Ellers bliver den ikke tjekket.',
	'Class:MailInboxBase/Attribute:active/Value:yes' => 'Ja',
	'Class:MailInboxBase/Attribute:active/Value:no' => 'Nej',
	'Class:MailInboxBase/Attribute:imap_options' => 'IMAP-indstillinger',
	'Class:MailInboxBase/Attribute:imap_options+' => 'IMAP-indstillinger kan angives linje for linje. De behandles i den rækkefølge.',
	'Class:MailInboxBase/Attribute:disable_authenticator' => 'Deaktiver autentificering',
	'Class:MailInboxBase/Attribute:disable_authenticator+' => 'I den ældre implementering er det muligt at deaktivere visse autentificeringsmekanismer. Lad som regel dette felt være tomt.',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI' => 'GSSAPI',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI+' => 'GSSAPI (Kerberos)',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM+' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN' => 'PLAIN',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN+' => 'PLAIN',
	'Class:MailInboxBase/Attribute:folder_delimiter' => 'Mappeafgrænser',
	'Class:MailInboxBase/Attribute:folder_delimiter+' => 'Tegnet som mailudbyderen bruger til at angive undermapper. Normalt "/" (Google, Microsoft) eller "." (Hetzner).',

	'MailInbox:MailboxContent' => 'Mailboksens indhold',
	'MailInbox:MailboxContent:ConfirmMessage' => 'Er du sikker?',
	'MailInbox:NoValidEmailsFound' => 'Ingen gyldige e-mails fundet i denne mailboks.',
	'MailInbox:Z_DisplayedThereAre_X_Msg_Y_NewInTheMailbox' => '%1$d e-mail(s) vist. Der er %2$d e-mail(s) i mailboksen (%3$d ubehandlede og %4$d ulæselige/beskadigede).',
	'MailInbox:UnprocessableMessages' => 'Der er %1$s e-mail(s), som ikke kan vises på grund af et teknisk problem.',
	'MailInbox:MaxAllowedPacketTooSmall' => 'MySQL-parameteren max_allowed_packet i "my.ini" er for lille: %1$s. Den anbefalede værdi er mindst: %2$s',
	'MailInbox:Status' => 'Status',
	'MailInbox:Subject' => 'Emne',
	'MailInbox:From' => 'Fra',
	'MailInbox:Date' => 'Dato',
	'MailInbox:RelatedTicket' => 'Relateret sag',
	'MailInbox:ErrorMessage' => 'Fejlmeddelelse',
	'MailInbox:Status/Processed' => 'Allerede behandlet',
	'MailInbox:Status/New' => 'Ny',
	'MailInbox:Status/Error' => 'Fejl',
    'MailInbox:Status/Undesired' => 'Uønsket',
	'MailInbox:Status/Ignored' => 'Ignoreret',
	'MailInbox:ReplicaLink' => 'Replika',
	'MailInbox:InternalId' => 'Internt ID',
	'MailInbox:UID' => 'UID',

	'MailInbox:Login/ServerMustBeUnique' => 'Kombinationen af login (%1$s) og server (%2$s) er allerede konfigureret for en anden mailboks.',
	'MailInbox:Login/Server/MailboxMustBeUnique' => 'Kombinationen af login (%1$s), server (%2$s) og mailboks (%3$s) er allerede konfigureret for en anden mailboks',
	'MailInbox:Display_X_eMailsStartingFrom_Y' => 'Viser %1$s e-mail(s), startende fra %2$s.',
	'MailInbox:WithSelectedDo' => 'Med de valgte e-mails: ',
	'MailInbox:ResetStatus' => 'Nulstil status',
	'MailInbox:DeleteMessage' => 'Slet e-mail',
	'MailInbox:IgnoreMessage' => 'Ignorer e-mail',

	'MailInbox:MessageDetails' => 'Detaljer om besked',
	'MailInbox:DownloadEml' => 'Download .eml-fil',
	'Class:TriggerOnMailUpdate' => 'Trigger (ved opdatering via mail)',
	'Class:TriggerOnMailUpdate+' => 'Trigger, der aktiveres, når en sag opdateres ved behandling af en indgående e-mail',

	'MailInbox:EmptyMailbox' => 'Tom mailboks',

	'Class:EmailReplica' => 'E-mail-replika',
	'Class:EmailReplica/Attribute:ticket_id' => 'Sags-ID',
	'Class:EmailReplica/Attribute:uidl' => 'UIDL',
	'Class:EmailReplica/Attribute:mailbox_path' => 'Mailboksens sti',
	'Class:EmailReplica/Attribute:message_id' => 'Besked-ID',
	'Class:EmailReplica/Attribute:message_text' => 'Beskedtekst',
	'Class:EmailReplica/Attribute:references' => 'Referencer',
	'Class:EmailReplica/Attribute:thread_index' => 'Trådindeks',
	'Class:EmailReplica/Attribute:message_date' => 'Beskedens dato',
	'Class:EmailReplica/Attribute:last_seen' => 'Sidst set',
	'Class:EmailReplica/Attribute:status' => 'Status',
	'Class:EmailReplica/Attribute:status/Value:error' => 'Fejl',
	'Class:EmailReplica/Attribute:status/Value:ignored' => 'Ignoreret',
	'Class:EmailReplica/Attribute:status/Value:ok' => 'OK',
	'Class:EmailReplica/Attribute:status/Value:undesired' => 'Uønsket',
	'Class:EmailReplica/Attribute:error_message' => 'Fejlmeddelelse',
	'Class:EmailReplica/Attribute:error_trace' => 'Fejlspor',
	'Class:EmailReplica/Attribute:contents' => 'Indhold',
	'Class:EmailReplica/Attribute:mailbox_id' => 'Mailboks-ID',


));

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

Dict::Add('NL NL', 'Dutch', 'Nederlands', array(
	// Dictionary entries go here
	'Class:MailInboxBase' => 'Mailbox',
	'Class:MailInboxBase+' => 'Bron van inkomende e-mails',

	'Class:MailInboxBase/Attribute:server' => 'Mailserver',
	'Class:MailInboxBase/Attribute:server+' => 'Het IP-adres of de volledig gekwalificeerde hostnaam van de mailserver',
	'Class:MailInboxBase/Attribute:mailbox' => 'Mailboxmap (voor IMAP)',
	'Class:MailInboxBase/Attribute:mailbox+' => 'Let op: IMAP-mappen zijn hoofdlettergevoelig. Geef submappen op met schuine strepen: Verwijderde items/Voorbeeld. Indien leeg gelaten, wordt de standaard (root) mailbox doorzocht',
	'Class:MailInboxBase/Attribute:login' => 'Login',
	'Class:MailInboxBase/Attribute:login+' => 'De naam van het mailaccount dat wordt gebruikt om verbinding te maken met de mailbox',
	'Class:MailInboxBase/Attribute:password' => 'Wachtwoord',
	'Class:MailInboxBase/Attribute:protocol' => 'Protocol',
	'Class:MailInboxBase/Attribute:protocol/Value:imap' => 'IMAP',
	'Class:MailInboxBase/Attribute:port' => 'Poort',
	'Class:MailInboxBase/Attribute:port+' => 'Standaardpoorten: 143 voor IMAP - 993 voor beveiligd IMAP.',
	'Class:MailInboxBase/Attribute:active' => 'Actief',
	'Class:MailInboxBase/Attribute:active+' => 'Alleen als dit op "Ja" staat, wordt de mailbox uitgelezen. Anders niet.',
	'Class:MailInboxBase/Attribute:active/Value:yes' => 'Ja',
	'Class:MailInboxBase/Attribute:active/Value:no' => 'Nee',
	'Class:MailInboxBase/Attribute:imap_options' => 'IMAP-opties',
	'Class:MailInboxBase/Attribute:imap_options+' => 'IMAP-opties kunnen regel per regel worden opgegeven. Ze worden in die volgorde verwerkt.',
	'Class:MailInboxBase/Attribute:disable_authenticator' => 'Authenticator uitschakelen',
	'Class:MailInboxBase/Attribute:disable_authenticator+' => 'In de legacy-implementatie is het mogelijk bepaalde authenticatiemechanismen uit te schakelen. Laat dit in de meeste gevallen leeg.',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI' => 'GSSAPI',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI+' => 'GSSAPI (Kerberos)',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM+' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN' => 'PLAIN',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN+' => 'PLAIN',
	'Class:MailInboxBase/Attribute:folder_delimiter' => 'Mapscheidingsteken',
	'Class:MailInboxBase/Attribute:folder_delimiter+' => 'Het teken dat de mailprovider gebruikt om submappen aan te geven. Meestal "/" (Google, Microsoft) of "." (Hetzner).',

	'MailInbox:MailboxContent' => 'Inhoud van de mailbox',
	'MailInbox:MailboxContent:ConfirmMessage' => 'Weet u het zeker?',
	'MailInbox:NoValidEmailsFound' => 'Geen geldige e-mails gevonden in deze mailbox.',
	'MailInbox:Z_DisplayedThereAre_X_Msg_Y_NewInTheMailbox' => '%1$d e-mail(s) weergegeven. Er zijn %2$d e-mail(s) in de mailbox (%3$d onverwerkt en %4$d onleesbaar/beschadigd).',
	'MailInbox:UnprocessableMessages' => 'Er zijn %1$s e-mail(s) die door een technisch probleem niet kunnen worden weergegeven.',
	'MailInbox:MaxAllowedPacketTooSmall' => 'De MySQL-parameter max_allowed_packet in "my.ini" is te klein: %1$s. De aanbevolen waarde is minstens: %2$s',
	'MailInbox:Status' => 'Status',
	'MailInbox:Subject' => 'Onderwerp',
	'MailInbox:From' => 'Van',
	'MailInbox:Date' => 'Datum',
	'MailInbox:RelatedTicket' => 'Gerelateerd ticket',
	'MailInbox:ErrorMessage' => 'Foutmelding',
	'MailInbox:Status/Processed' => 'Reeds verwerkt',
	'MailInbox:Status/New' => 'Nieuw',
	'MailInbox:Status/Error' => 'Fout',
    'MailInbox:Status/Undesired' => 'Ongewenst',
	'MailInbox:Status/Ignored' => 'Genegeerd',
	'MailInbox:ReplicaLink' => 'Replica',
	'MailInbox:InternalId' => 'Intern ID',
	'MailInbox:UID' => 'UID',

	'MailInbox:Login/ServerMustBeUnique' => 'De combinatie van login (%1$s) en server (%2$s) is al geconfigureerd voor een andere mailbox.',
	'MailInbox:Login/Server/MailboxMustBeUnique' => 'De combinatie van login (%1$s), server (%2$s) en mailbox (%3$s) is al geconfigureerd voor een andere mailbox',
	'MailInbox:Display_X_eMailsStartingFrom_Y' => '%1$s e-mail(s) worden weergegeven, vanaf %2$s.',
	'MailInbox:WithSelectedDo' => 'Met de geselecteerde e-mails: ',
	'MailInbox:ResetStatus' => 'Status resetten',
	'MailInbox:DeleteMessage' => 'E-mail verwijderen',
	'MailInbox:IgnoreMessage' => 'E-mail negeren',

	'MailInbox:MessageDetails' => 'Berichtdetails',
	'MailInbox:DownloadEml' => '.eml-bestand downloaden',
	'Class:TriggerOnMailUpdate' => 'Trigger (bij bijwerken via mail)',
	'Class:TriggerOnMailUpdate+' => 'Trigger die wordt geactiveerd wanneer een ticket wordt bijgewerkt door het verwerken van een inkomende e-mail',

	'MailInbox:EmptyMailbox' => 'Lege mailbox',

	'Class:EmailReplica' => 'E-mailreplica',
	'Class:EmailReplica/Attribute:ticket_id' => 'Ticket-ID',
	'Class:EmailReplica/Attribute:uidl' => 'UIDL',
	'Class:EmailReplica/Attribute:mailbox_path' => 'Pad van de mailbox',
	'Class:EmailReplica/Attribute:message_id' => 'Bericht-ID',
	'Class:EmailReplica/Attribute:message_text' => 'Berichttekst',
	'Class:EmailReplica/Attribute:references' => 'Referenties',
	'Class:EmailReplica/Attribute:thread_index' => 'Threadindex',
	'Class:EmailReplica/Attribute:message_date' => 'Berichtdatum',
	'Class:EmailReplica/Attribute:last_seen' => 'Laatst gezien',
	'Class:EmailReplica/Attribute:status' => 'Status',
	'Class:EmailReplica/Attribute:status/Value:error' => 'Fout',
	'Class:EmailReplica/Attribute:status/Value:ignored' => 'Genegeerd',
	'Class:EmailReplica/Attribute:status/Value:ok' => 'OK',
	'Class:EmailReplica/Attribute:status/Value:undesired' => 'Ongewenst',
	'Class:EmailReplica/Attribute:error_message' => 'Foutmelding',
	'Class:EmailReplica/Attribute:error_trace' => 'Foutopsporing',
	'Class:EmailReplica/Attribute:contents' => 'Inhoud',
	'Class:EmailReplica/Attribute:mailbox_id' => 'Mailbox-ID',


));

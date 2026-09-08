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

Dict::Add('DE DE', 'German', 'Deutsch', array(
	// Dictionary entries go here
	'Class:MailInboxBase' => 'Postfach',
	'Class:MailInboxBase+' => 'Quelle für eingehende E-Mails',

	'Class:MailInboxBase/Attribute:server' => 'Mailserver',
	'Class:MailInboxBase/Attribute:server+' => 'Die IP-Adresse oder der vollständig qualifizierte Hostname des Mailservers',
	'Class:MailInboxBase/Attribute:mailbox' => 'Postfachordner (für IMAP)',
	'Class:MailInboxBase/Attribute:mailbox+' => 'Hinweis: Bei IMAP-Ordnern wird zwischen Groß- und Kleinschreibung unterschieden. Geben Sie Unterordner mit Schrägstrichen an: Gelöschte Objekte/Beispiel. Wenn leer gelassen, wird das Standardpostfach (Root) durchsucht',
	'Class:MailInboxBase/Attribute:login' => 'Anmeldename',
	'Class:MailInboxBase/Attribute:login+' => 'Der Name des E-Mail-Kontos, das für die Verbindung mit dem Postfach verwendet wird',
	'Class:MailInboxBase/Attribute:password' => 'Passwort',
	'Class:MailInboxBase/Attribute:protocol' => 'Protokoll',
	'Class:MailInboxBase/Attribute:protocol/Value:imap' => 'IMAP',
	'Class:MailInboxBase/Attribute:port' => 'Port',
	'Class:MailInboxBase/Attribute:port+' => 'Standardports: 143 für IMAP - 993 für gesichertes IMAP.',
	'Class:MailInboxBase/Attribute:active' => 'Aktiv',
	'Class:MailInboxBase/Attribute:active+' => 'Nur wenn auf "Ja" gesetzt, wird das Postfach abgefragt. Andernfalls wird es nicht abgefragt.',
	'Class:MailInboxBase/Attribute:active/Value:yes' => 'Ja',
	'Class:MailInboxBase/Attribute:active/Value:no' => 'Nein',
	'Class:MailInboxBase/Attribute:imap_options' => 'IMAP-Optionen',
	'Class:MailInboxBase/Attribute:imap_options+' => 'IMAP-Optionen können zeilenweise angegeben werden. Sie werden in dieser Reihenfolge verarbeitet.',
	'Class:MailInboxBase/Attribute:disable_authenticator' => 'Authentifikator deaktivieren',
	'Class:MailInboxBase/Attribute:disable_authenticator+' => 'In der Legacy-Implementierung ist es möglich, bestimmte Authentifizierungsmechanismen zu deaktivieren. In den meisten Fällen leer lassen.',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI' => 'GSSAPI',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI+' => 'GSSAPI (Kerberos)',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM+' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN' => 'PLAIN',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN+' => 'PLAIN',
	'Class:MailInboxBase/Attribute:folder_delimiter' => 'Ordnertrennzeichen',
	'Class:MailInboxBase/Attribute:folder_delimiter+' => 'Das Zeichen, das der Mailanbieter zur Angabe von Unterordnern verwendet. Normalerweise "/" (Google, Microsoft) oder "." (Hetzner).',

	'MailInbox:MailboxContent' => 'Postfachinhalt',
	'MailInbox:MailboxContent:ConfirmMessage' => 'Sind Sie sicher?',
	'MailInbox:NoValidEmailsFound' => 'In diesem Postfach wurden keine gültigen E-Mails gefunden.',
	'MailInbox:Z_DisplayedThereAre_X_Msg_Y_NewInTheMailbox' => '%1$d E-Mail(s) angezeigt. Es gibt %2$d E-Mail(s) im Postfach (%3$d unverarbeitet und %4$d unlesbar/beschädigt).',
	'MailInbox:UnprocessableMessages' => 'Es gibt %1$s E-Mail(s), die aufgrund eines technischen Problems nicht aufgelistet werden können.',
	'MailInbox:MaxAllowedPacketTooSmall' => 'Der MySQL-Parameter max_allowed_packet in "my.ini" ist zu klein: %1$s. Der empfohlene Wert beträgt mindestens: %2$s',
	'MailInbox:Status' => 'Status',
	'MailInbox:Subject' => 'Betreff',
	'MailInbox:From' => 'Von',
	'MailInbox:Date' => 'Datum',
	'MailInbox:RelatedTicket' => 'Zugehöriges Ticket',
	'MailInbox:ErrorMessage' => 'Fehlermeldung',
	'MailInbox:Status/Processed' => 'Bereits verarbeitet',
	'MailInbox:Status/New' => 'Neu',
	'MailInbox:Status/Error' => 'Fehler',
    'MailInbox:Status/Undesired' => 'Unerwünscht',
	'MailInbox:Status/Ignored' => 'Ignoriert',
	'MailInbox:ReplicaLink' => 'Replik',
	'MailInbox:InternalId' => 'Interne ID',
	'MailInbox:UID' => 'UID',

	'MailInbox:Login/ServerMustBeUnique' => 'Die Kombination aus Anmeldename (%1$s) und Server (%2$s) ist bereits für ein anderes Postfach konfiguriert.',
	'MailInbox:Login/Server/MailboxMustBeUnique' => 'Die Kombination aus Anmeldename (%1$s), Server (%2$s) und Postfach (%3$s) ist bereits für ein anderes Postfach konfiguriert',
	'MailInbox:Display_X_eMailsStartingFrom_Y' => 'Zeige %1$s E-Mail(s), beginnend bei %2$s.',
	'MailInbox:WithSelectedDo' => 'Mit den ausgewählten E-Mails: ',
	'MailInbox:ResetStatus' => 'Status zurücksetzen',
	'MailInbox:DeleteMessage' => 'E-Mail löschen',
	'MailInbox:IgnoreMessage' => 'E-Mail ignorieren',

	'MailInbox:MessageDetails' => 'Nachrichtendetails',
	'MailInbox:DownloadEml' => '.eml-Datei herunterladen',
	'Class:TriggerOnMailUpdate' => 'Auslöser (bei Aktualisierung per E-Mail)',
	'Class:TriggerOnMailUpdate+' => 'Auslöser, der aktiviert wird, wenn ein Ticket durch die Verarbeitung einer eingehenden E-Mail aktualisiert wird',

	'MailInbox:EmptyMailbox' => 'Leeres Postfach',

	'Class:EmailReplica' => 'E-Mail-Replik',
	'Class:EmailReplica/Attribute:ticket_id' => 'Ticket-ID',
	'Class:EmailReplica/Attribute:uidl' => 'UIDL',
	'Class:EmailReplica/Attribute:mailbox_path' => 'Postfachpfad',
	'Class:EmailReplica/Attribute:message_id' => 'Nachrichten-ID',
	'Class:EmailReplica/Attribute:message_text' => 'Nachrichtentext',
	'Class:EmailReplica/Attribute:references' => 'Referenzen',
	'Class:EmailReplica/Attribute:thread_index' => 'Thread-Index',
	'Class:EmailReplica/Attribute:message_date' => 'Nachrichtendatum',
	'Class:EmailReplica/Attribute:last_seen' => 'Zuletzt gesehen',
	'Class:EmailReplica/Attribute:status' => 'Status',
	'Class:EmailReplica/Attribute:status/Value:error' => 'Fehler',
	'Class:EmailReplica/Attribute:status/Value:ignored' => 'Ignoriert',
	'Class:EmailReplica/Attribute:status/Value:ok' => 'OK',
	'Class:EmailReplica/Attribute:status/Value:undesired' => 'Unerwünscht',
	'Class:EmailReplica/Attribute:error_message' => 'Fehlermeldung',
	'Class:EmailReplica/Attribute:error_trace' => 'Fehlerverfolgung',
	'Class:EmailReplica/Attribute:contents' => 'Inhalt',
	'Class:EmailReplica/Attribute:mailbox_id' => 'Postfach-ID',


));

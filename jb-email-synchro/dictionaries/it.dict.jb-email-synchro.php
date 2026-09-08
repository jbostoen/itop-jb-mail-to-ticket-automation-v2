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

Dict::Add('IT IT', 'Italian', 'Italiano', array(
	// Dictionary entries go here
	'Class:MailInboxBase' => 'Casella di posta',
	'Class:MailInboxBase+' => 'Origine delle e-mail in entrata',

	'Class:MailInboxBase/Attribute:server' => 'Server di posta',
	'Class:MailInboxBase/Attribute:server+' => 'L\'indirizzo IP o il nome host completo del server di posta',
	'Class:MailInboxBase/Attribute:mailbox' => 'Cartella della casella di posta (per IMAP)',
	'Class:MailInboxBase/Attribute:mailbox+' => 'Nota: le cartelle IMAP distinguono tra maiuscole e minuscole. Specificare le sottocartelle usando le barre: Posta eliminata/Esempio. Se omesso, verrà analizzata la casella di posta predefinita (radice)',
	'Class:MailInboxBase/Attribute:login' => 'Nome utente',
	'Class:MailInboxBase/Attribute:login+' => 'Il nome dell\'account di posta usato per connettersi alla casella di posta',
	'Class:MailInboxBase/Attribute:password' => 'Password',
	'Class:MailInboxBase/Attribute:protocol' => 'Protocollo',
	'Class:MailInboxBase/Attribute:protocol/Value:imap' => 'IMAP',
	'Class:MailInboxBase/Attribute:port' => 'Porta',
	'Class:MailInboxBase/Attribute:port+' => 'Porte predefinite: 143 per IMAP - 993 per IMAP protetto.',
	'Class:MailInboxBase/Attribute:active' => 'Attivo',
	'Class:MailInboxBase/Attribute:active+' => 'Solo se impostato su "Sì" la casella di posta verrà interrogata. Altrimenti non lo sarà.',
	'Class:MailInboxBase/Attribute:active/Value:yes' => 'Sì',
	'Class:MailInboxBase/Attribute:active/Value:no' => 'No',
	'Class:MailInboxBase/Attribute:imap_options' => 'Opzioni IMAP',
	'Class:MailInboxBase/Attribute:imap_options+' => 'Le opzioni IMAP possono essere specificate riga per riga. Vengono elaborate in quell\'ordine.',
	'Class:MailInboxBase/Attribute:disable_authenticator' => 'Disattiva autenticatore',
	'Class:MailInboxBase/Attribute:disable_authenticator+' => 'Nell\'implementazione legacy, è possibile disattivare determinati meccanismi di autenticazione. Nella maggior parte dei casi, lasciare questo campo vuoto.',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI' => 'GSSAPI',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI+' => 'GSSAPI (Kerberos)',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM+' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN' => 'PLAIN',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN+' => 'PLAIN',
	'Class:MailInboxBase/Attribute:folder_delimiter' => 'Delimitatore di cartella',
	'Class:MailInboxBase/Attribute:folder_delimiter+' => 'Il carattere usato dal provider di posta per indicare le sottocartelle. Solitamente "/" (Google, Microsoft) o "." (Hetzner).',

	'MailInbox:MailboxContent' => 'Contenuto della casella di posta',
	'MailInbox:MailboxContent:ConfirmMessage' => 'Sei sicuro?',
	'MailInbox:NoValidEmailsFound' => 'Nessuna e-mail valida trovata in questa casella di posta.',
	'MailInbox:Z_DisplayedThereAre_X_Msg_Y_NewInTheMailbox' => '%1$d e-mail visualizzate. Ci sono %2$d e-mail nella casella di posta (%3$d non elaborate e %4$d illeggibili/corrotte).',
	'MailInbox:UnprocessableMessages' => 'Ci sono %1$s e-mail che non possono essere elencate a causa di un problema tecnico.',
	'MailInbox:MaxAllowedPacketTooSmall' => 'Il parametro MySQL max_allowed_packet in "my.ini" è troppo piccolo: %1$s. Il valore consigliato è almeno: %2$s',
	'MailInbox:Status' => 'Stato',
	'MailInbox:Subject' => 'Oggetto',
	'MailInbox:From' => 'Da',
	'MailInbox:Date' => 'Data',
	'MailInbox:RelatedTicket' => 'Ticket correlato',
	'MailInbox:ErrorMessage' => 'Messaggio di errore',
	'MailInbox:Status/Processed' => 'Già elaborato',
	'MailInbox:Status/New' => 'Nuovo',
	'MailInbox:Status/Error' => 'Errore',
    'MailInbox:Status/Undesired' => 'Indesiderato',
	'MailInbox:Status/Ignored' => 'Ignorato',
	'MailInbox:ReplicaLink' => 'Replica',
	'MailInbox:InternalId' => 'ID interno',
	'MailInbox:UID' => 'UID',

	'MailInbox:Login/ServerMustBeUnique' => 'La combinazione di nome utente (%1$s) e server (%2$s) è già configurata per un\'altra casella di posta.',
	'MailInbox:Login/Server/MailboxMustBeUnique' => 'La combinazione di nome utente (%1$s), server (%2$s) e casella di posta (%3$s) è già configurata per un\'altra casella di posta',
	'MailInbox:Display_X_eMailsStartingFrom_Y' => 'Visualizzazione di %1$s e-mail, a partire da %2$s.',
	'MailInbox:WithSelectedDo' => 'Con le e-mail selezionate: ',
	'MailInbox:ResetStatus' => 'Ripristina stato',
	'MailInbox:DeleteMessage' => 'Elimina e-mail',
	'MailInbox:IgnoreMessage' => 'Ignora e-mail',

	'MailInbox:MessageDetails' => 'Dettagli del messaggio',
	'MailInbox:DownloadEml' => 'Scarica file .eml',
	'Class:TriggerOnMailUpdate' => 'Trigger (all\'aggiornamento tramite posta)',
	'Class:TriggerOnMailUpdate+' => 'Trigger attivato quando un ticket viene aggiornato elaborando un\'e-mail in entrata',

	'MailInbox:EmptyMailbox' => 'Casella di posta vuota',

	'Class:EmailReplica' => 'Replica e-mail',
	'Class:EmailReplica/Attribute:ticket_id' => 'ID ticket',
	'Class:EmailReplica/Attribute:uidl' => 'UIDL',
	'Class:EmailReplica/Attribute:mailbox_path' => 'Percorso della casella di posta',
	'Class:EmailReplica/Attribute:message_id' => 'ID messaggio',
	'Class:EmailReplica/Attribute:message_text' => 'Testo del messaggio',
	'Class:EmailReplica/Attribute:references' => 'Riferimenti',
	'Class:EmailReplica/Attribute:thread_index' => 'Indice del thread',
	'Class:EmailReplica/Attribute:message_date' => 'Data del messaggio',
	'Class:EmailReplica/Attribute:last_seen' => 'Ultima visualizzazione',
	'Class:EmailReplica/Attribute:status' => 'Stato',
	'Class:EmailReplica/Attribute:status/Value:error' => 'Errore',
	'Class:EmailReplica/Attribute:status/Value:ignored' => 'Ignorato',
	'Class:EmailReplica/Attribute:status/Value:ok' => 'OK',
	'Class:EmailReplica/Attribute:status/Value:undesired' => 'Indesiderato',
	'Class:EmailReplica/Attribute:error_message' => 'Messaggio di errore',
	'Class:EmailReplica/Attribute:error_trace' => 'Traccia dell\'errore',
	'Class:EmailReplica/Attribute:contents' => 'Contenuto',
	'Class:EmailReplica/Attribute:mailbox_id' => 'ID casella di posta',


));

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

Dict::Add('CS CZ', 'Czech', 'Čeština', array(
	// Dictionary entries go here
	'Class:MailInboxBase' => 'Poštovní schránka',
	'Class:MailInboxBase+' => 'Zdroj příchozích e-mailů',

	'Class:MailInboxBase/Attribute:server' => 'Poštovní server',
	'Class:MailInboxBase/Attribute:server+' => 'IP adresa nebo plně kvalifikovaný název hostitele poštovního serveru',
	'Class:MailInboxBase/Attribute:mailbox' => 'Složka schránky (pro IMAP)',
	'Class:MailInboxBase/Attribute:mailbox+' => 'Pozn.: u složek IMAP záleží na velikosti písmen. Podsložky zadávejte pomocí lomítek: Odstraněná pošta/Příklad. Pokud ponecháte prázdné, bude prohledávána výchozí (kořenová) schránka',
	'Class:MailInboxBase/Attribute:login' => 'Přihlašovací jméno',
	'Class:MailInboxBase/Attribute:login+' => 'Název poštovního účtu použitého pro připojení ke schránce',
	'Class:MailInboxBase/Attribute:password' => 'Heslo',
	'Class:MailInboxBase/Attribute:protocol' => 'Protokol',
	'Class:MailInboxBase/Attribute:protocol/Value:imap' => 'IMAP',
	'Class:MailInboxBase/Attribute:port' => 'Port',
	'Class:MailInboxBase/Attribute:port+' => 'Výchozí porty: 143 pro IMAP - 993 pro zabezpečený IMAP.',
	'Class:MailInboxBase/Attribute:active' => 'Aktivní',
	'Class:MailInboxBase/Attribute:active+' => 'Pouze pokud je nastaveno na "Ano", bude schránka kontrolována. V opačném případě kontrolována nebude.',
	'Class:MailInboxBase/Attribute:active/Value:yes' => 'Ano',
	'Class:MailInboxBase/Attribute:active/Value:no' => 'Ne',
	'Class:MailInboxBase/Attribute:imap_options' => 'Možnosti IMAP',
	'Class:MailInboxBase/Attribute:imap_options+' => 'Možnosti IMAP lze zadat řádek po řádku. Zpracovávají se v tomto pořadí.',
	'Class:MailInboxBase/Attribute:disable_authenticator' => 'Zakázat autentizátor',
	'Class:MailInboxBase/Attribute:disable_authenticator+' => 'Ve starší implementaci je možné zakázat určité mechanismy ověřování. Ve většině případů ponechte prázdné.',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI' => 'GSSAPI',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI+' => 'GSSAPI (Kerberos)',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM+' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN' => 'PLAIN',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN+' => 'PLAIN',
	'Class:MailInboxBase/Attribute:folder_delimiter' => 'Oddělovač složek',
	'Class:MailInboxBase/Attribute:folder_delimiter+' => 'Znak používaný poskytovatelem pošty k označení podsložek. Obvykle "/" (Google, Microsoft) nebo "." (Hetzner).',

	'MailInbox:MailboxContent' => 'Obsah schránky',
	'MailInbox:MailboxContent:ConfirmMessage' => 'Jste si jisti?',
	'MailInbox:NoValidEmailsFound' => 'V této schránce nebyly nalezeny žádné platné e-maily.',
	'MailInbox:Z_DisplayedThereAre_X_Msg_Y_NewInTheMailbox' => 'Zobrazeno %1$d e-mail(ů). Ve schránce je %2$d e-mail(ů) (%3$d nezpracovaných a %4$d nečitelných/poškozených).',
	'MailInbox:UnprocessableMessages' => 'Existuje %1$s e-mail(ů), které nelze zobrazit kvůli technickému problému.',
	'MailInbox:MaxAllowedPacketTooSmall' => 'Parametr MySQL max_allowed_packet v "my.ini" je příliš malý: %1$s. Doporučená hodnota je alespoň: %2$s',
	'MailInbox:Status' => 'Stav',
	'MailInbox:Subject' => 'Předmět',
	'MailInbox:From' => 'Od',
	'MailInbox:Date' => 'Datum',
	'MailInbox:RelatedTicket' => 'Související tiket',
	'MailInbox:ErrorMessage' => 'Chybová zpráva',
	'MailInbox:Status/Processed' => 'Již zpracováno',
	'MailInbox:Status/New' => 'Nové',
	'MailInbox:Status/Error' => 'Chyba',
    'MailInbox:Status/Undesired' => 'Nežádoucí',
	'MailInbox:Status/Ignored' => 'Ignorováno',
	'MailInbox:ReplicaLink' => 'Replika',
	'MailInbox:InternalId' => 'Interní ID',
	'MailInbox:UID' => 'UID',

	'MailInbox:Login/ServerMustBeUnique' => 'Kombinace přihlašovacího jména (%1$s) a serveru (%2$s) je již nakonfigurována pro jinou poštovní schránku.',
	'MailInbox:Login/Server/MailboxMustBeUnique' => 'Kombinace přihlašovacího jména (%1$s), serveru (%2$s) a schránky (%3$s) je již nakonfigurována pro jinou poštovní schránku',
	'MailInbox:Display_X_eMailsStartingFrom_Y' => 'Zobrazeno %1$s e-mail(ů), počínaje %2$s.',
	'MailInbox:WithSelectedDo' => 'S vybranými e-maily: ',
	'MailInbox:ResetStatus' => 'Obnovit stav',
	'MailInbox:DeleteMessage' => 'Smazat e-mail',
	'MailInbox:IgnoreMessage' => 'Ignorovat e-mail',

	'MailInbox:MessageDetails' => 'Podrobnosti zprávy',
	'MailInbox:DownloadEml' => 'Stáhnout soubor .eml',
	'Class:TriggerOnMailUpdate' => 'Spouštěč (při aktualizaci poštou)',
	'Class:TriggerOnMailUpdate+' => 'Spouštěč aktivovaný při aktualizaci tiketu zpracováním příchozího e-mailu',

	'MailInbox:EmptyMailbox' => 'Prázdná schránka',

	'Class:EmailReplica' => 'Replika e-mailu',
	'Class:EmailReplica/Attribute:ticket_id' => 'ID tiketu',
	'Class:EmailReplica/Attribute:uidl' => 'UIDL',
	'Class:EmailReplica/Attribute:mailbox_path' => 'Cesta ke schránce',
	'Class:EmailReplica/Attribute:message_id' => 'ID zprávy',
	'Class:EmailReplica/Attribute:message_text' => 'Text zprávy',
	'Class:EmailReplica/Attribute:references' => 'Odkazy',
	'Class:EmailReplica/Attribute:thread_index' => 'Index vlákna',
	'Class:EmailReplica/Attribute:message_date' => 'Datum zprávy',
	'Class:EmailReplica/Attribute:last_seen' => 'Naposledy viděno',
	'Class:EmailReplica/Attribute:status' => 'Stav',
	'Class:EmailReplica/Attribute:status/Value:error' => 'Chyba',
	'Class:EmailReplica/Attribute:status/Value:ignored' => 'Ignorováno',
	'Class:EmailReplica/Attribute:status/Value:ok' => 'OK',
	'Class:EmailReplica/Attribute:status/Value:undesired' => 'Nežádoucí',
	'Class:EmailReplica/Attribute:error_message' => 'Chybová zpráva',
	'Class:EmailReplica/Attribute:error_trace' => 'Trasování chyby',
	'Class:EmailReplica/Attribute:contents' => 'Obsah',
	'Class:EmailReplica/Attribute:mailbox_id' => 'ID schránky',


));

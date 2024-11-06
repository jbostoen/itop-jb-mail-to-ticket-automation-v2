<?php
/**
 * Localized data
 *
 * @copyright Copyright (c) 2010-2024 Combodo SARL
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

Dict::Add('FR FR', 'French', 'Français', array(
	// Dictionary entries go here
	'Class:MailInboxBase' => 'Boîte Mail',
	'Class:MailInboxBase+' => 'Source d\'eMails',

	'Class:MailInboxBase/Attribute:server' => 'Serveur d\'eMails',
	'Class:MailInboxBase/Attribute:server+' => 'L\'adresse IP ou nom complet (DNS) du Serveur d\'eMails',
	'Class:MailInboxBase/Attribute:mailbox' => 'Boîte Mail (pour IMAP)',
	'Class:MailInboxBase/Attribute:mailbox+' => 'Info : Les dossiers IMAP sont sensibles à la casse. Pour un sous dossier, utilisez le slash : Elements supprimés/Exemple. S\'il n\'est pas fourni, on accède la racine',
	'Class:MailInboxBase/Attribute:login' => 'Identifiant',
	'Class:MailInboxBase/Attribute:login+' => 'L\'identifiant du compte applicatif pour se connecter à la boite mail',
	'Class:MailInboxBase/Attribute:password' => 'Mot de passe',
	'Class:MailInboxBase/Attribute:protocol' => 'Protocole',
	'Class:MailInboxBase/Attribute:protocol/Value:imap' => 'IMAP',
	'Class:MailInboxBase/Attribute:port' => 'Port',
	'Class:MailInboxBase/Attribute:port+' => '143 (securisé: 993) pour IMAP',
	'Class:MailInboxBase/Attribute:active' => 'Boîte Activée',
	'Class:MailInboxBase/Attribute:active+' => 'Si renseigné à "Oui", la boite mail est interrogée, sinon elle ne l\'est pas',
	'Class:MailInboxBase/Attribute:active/Value:yes' => 'Oui',
	'Class:MailInboxBase/Attribute:active/Value:no' => 'Non',
	'Class:MailInboxBase/Attribute:imap_options' => 'IMAP options~~',
	'Class:MailInboxBase/Attribute:imap_options+' => 'IMAP options can be specified, line by line. They are processed in that order.~~',
	'Class:MailInboxBase/Attribute:disable_authenticator' => 'Disable authenticator~~',
	'Class:MailInboxBase/Attribute:disable_authenticator+' => 'In the legacy implementation, it is possible to disable certain authentication mechanisms. In most cases, leave this empty.~~',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI' => 'GSSAPI~~',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI+' => 'GSSAPI (Kerberos)~~',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM' => 'NTLM~~',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM+' => 'NTLM~~',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN' => 'PLAIN~~',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN+' => 'PLAIN~~',

	'MailInbox:MailboxContent' => 'Contenu de la boîte mail',
	'MailInbox:MailboxContent:ConfirmMessage' => 'Etes-vous sûr(e)?',
	'MailInbox:NoValidEmailsFound' => 'La boîte mail ne contient pas d\'eMails valides.',
	'MailInbox:Z_DisplayedThereAre_X_Msg_Y_NewInTheMailbox' => '%1$d eMail(s) affichés. Il y a au total %2$d eMail(s) dans la boîte (dont %3$d nouveaux et %4$d corrompus).',
	'MailInbox:UnprocessableMessages' => 'There are %1$s e-mail(s) which can not be listed due to a technical issue.~~',
	'MailInbox:MaxAllowedPacketTooSmall' => 'Le paramètre MySQL max_allowed_packet dans le fichier "my.ini" est trop petit : %1$s. La valeur recommandée est d\'au minimum : %2$s',
	'MailInbox:Status' => 'Etat',
	'MailInbox:Subject' => 'Objet',
	'MailInbox:From' => 'De',
	'MailInbox:Date' => 'Date',
	'MailInbox:RelatedTicket' => 'Ticket Lié',
	'MailInbox:ErrorMessage' => 'Message d\'Erreur',
	'MailInbox:Status/Processed' => 'Déjà Traité',
	'MailInbox:Status/New' => 'Nouveau',
	'MailInbox:Status/Error' => 'Erreur',
	'MailInbox:Status/Undesired' => 'Indésirable',
	'MailInbox:Status/Ignored' => 'Ignoré',

	'MailInbox:Login/ServerMustBeUnique' => 'La combinaison Identifiant (%1$s) et Serveur (%2$s) est déjà utilisée par une Boîte Mail.',
	'MailInbox:Login/Server/MailboxMustBeUnique' => 'La combinaison Identifiant (%1$s), Serveur (%2$s) et boîte mail (%3$s) est déjà utilisée par une Boîte Mail.',
	'MailInbox:Display_X_eMailsStartingFrom_Y' => 'Afficher %1$s eMail(s), à partir du numéro %2$s',
	'MailInbox:WithSelectedDo' => 'Pour les éléments sélectionnés : ',
	'MailInbox:ResetStatus' => 'RàZ de l\'état',
	'MailInbox:DeleteMessage' => 'Effacer l\'email',
	'MailInbox:IgnoreMessage' => 'Ignoer l\'email',

	'MailInbox:MessageDetails' => 'Details du message',
	'MailInbox:DownloadEml' => 'Télécharger l\'eml',
	'Class:TriggerOnMailUpdate' => 'Déclencheur sur mise à jour par mail',
	'Class:TriggerOnMailUpdate+' => 'Déclencheur activé sur la mise à jour de tickets par mail',
	
	'MailInbox:EmptyMailbox' => 'La boîte mail est vide.',
	
	'Class:EmailReplica' => 'Email Replica~~',
	'Class:EmailReplica/Attribute:ticket_id' => 'Ticket ID~~',
	'Class:EmailReplica/Attribute:uidl' => 'UIDL~~',
	'Class:EmailReplica/Attribute:mailbox_path' => 'Mailbox path~~',
	'Class:EmailReplica/Attribute:message_id' => 'Message ID~~',
	'Class:EmailReplica/Attribute:message_text' => 'Message text~~',
	'Class:EmailReplica/Attribute:references' => 'References~~',
	'Class:EmailReplica/Attribute:thread_index' => 'Thread index~~',
	'Class:EmailReplica/Attribute:message_date' => 'Message date~~',
	'Class:EmailReplica/Attribute:last_seen' => 'Last seen~~',
	'Class:EmailReplica/Attribute:status' => 'Status~~',
	'Class:EmailReplica/Attribute:status/Value:error' => 'Error~~',
	'Class:EmailReplica/Attribute:status/Value:ignored' => 'Ignored~~',
	'Class:EmailReplica/Attribute:status/Value:ok' => 'OK~~',
	'Class:EmailReplica/Attribute:status/Value:undesired' => 'Undesired~~',
	'Class:EmailReplica/Attribute:error_message' => 'Error message~~',
	'Class:EmailReplica/Attribute:error_trace' => 'Error trace~~',
	'Class:EmailReplica/Attribute:contents' => 'Contents~~',
	'Class:EmailReplica/Attribute:mailbox_id' => 'Mailbox ID',
	
));

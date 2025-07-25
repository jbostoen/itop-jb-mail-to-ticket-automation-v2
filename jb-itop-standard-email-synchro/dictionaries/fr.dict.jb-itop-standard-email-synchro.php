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

Dict::Add('FR FR', 'French', 'Français', array(

	// Dictionary entries go here
	'Class:MailInboxStandard' => 'Boîte Mail Standard',
	'Class:MailInboxStandard+' => 'Source d\'eMails',
	'Class:MailInboxStandard/Attribute:behavior' => 'Comportement',
	'Class:MailInboxStandard/Attribute:behavior/Value:create_only' => 'Créer des Tickets',
	'Class:MailInboxStandard/Attribute:behavior/Value:update_only' => 'Mettre à jour des Tickets existants',
	'Class:MailInboxStandard/Attribute:behavior/Value:both' => 'Créer ou mettre à jour des Tickets',

	'Class:MailInboxStandard/Attribute:email_storage' => 'Après traitement de l\'eMail',
	'Class:MailInboxStandard/Attribute:email_storage+' => 'Action to take after processing the e-mail. For best performance: if archiving is desired, it is recommended to move successfully processed e-mails to another folder.',
	'Class:MailInboxStandard/Attribute:email_storage/Value:keep' => 'Conserver l\'eMail sur le même dossier',
	'Class:MailInboxStandard/Attribute:email_storage/Value:delete' => 'Effacer immédiatement l\'eMail',
	'Class:MailInboxStandard/Attribute:email_storage/Value:move' => 'Déplacer vers un autre dossier',

	'Class:MailInboxStandard/Attribute:target_class' => 'Type de Ticket',
	'Class:MailInboxStandard/Attribute:target_class/Value:Incident' => 'Incident',
	'Class:MailInboxStandard/Attribute:target_class/Value:UserRequest' => 'Demande utilisateur',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change' => 'Ticket de Changement',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange' => 'Ticket de Changement de Routine',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange' => 'Ticket de Changement Normal',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange' => 'Ticket de Changement d\'urgence',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem' => 'Problem',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem+' => '',
	'Class:MailInboxStandard/Attribute:debug_trace' => 'Debug trace',
	'Class:MailInboxStandard/Attribute:debug_trace+' => '',
	'Class:MailInboxStandard/Attribute:target_folder' => 'Target folder',
	'Class:MailInboxStandard/Attribute:target_folder+' => 'Use to move an email with the IMAP protocol',

	'Class:MailInboxStandard/Attribute:ticket_default_values' => 'Valeurs par défaut du Ticket',
	'Class:MailInboxStandard/Attribute:ticket_default_title' => 'Titre par défaut (en cas de sujet vide)',
	'Class:MailInboxStandard/Attribute:title_pattern+' => 'Expression régulière à rechercher dans l\'objet de l\'eMail',
	'Class:MailInboxStandard/Attribute:title_pattern' => 'Recherche dans l\'objet du mail (RegExp)',
	'Class:MailInboxStandard/Attribute:title_pattern?' => 'Utilisez la syntaxe PCRE avec les délimiteurs de début et de fin',

	'Class:MailInboxStandard/Attribute:title_pattern_ignore_patterns' => 'Schémas à ignorer dans l\'objet (Schémas en exp. rég., un par ligne)',
	
	'Class:MailInboxStandard/Attribute:stimuli' => 'Stimuli à appliquer',
	'Class:MailInboxStandard/Attribute:stimuli+' => 'Appliquer un stimulus quand le ticket est dans un état donné',
	'Class:MailInboxStandard/Attribute:stimuli?' => 'Utilisez le format <code_etat>:<code_stimulus>',


	'Class:MailInboxStandard/Attribute:trace' => 'Activer la trace',
	'Class:MailInboxStandard/Attribute:trace/Value:yes' => 'Oui',
	'Class:MailInboxStandard/Attribute:trace/Value:no' => 'Non',
	   
	'Class:MailInboxStandard/Attribute:debug_log' => 'Traces de Debug',
	
	'Class:MailInboxStandard/Attribute:error_behavior' => 'Comportement en cas d\'erreur',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:delete' => 'Supprimer l\'eMail de la boîte mail',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:mark_as_error' => 'Garder l\'eMail dans la boîte mail',
	'Class:MailInboxStandard/Attribute:notify_errors_to' => 'Faire suivre l\'eMail à',
	'Class:MailInboxStandard/Attribute:notify_errors_from' => '(De)',
	
	'Class:MailInboxStandard/Attribute:mail_aliases' => 'Alias d\'email',
	'Class:MailInboxStandard/Attribute:mail_aliases+' => 'Alias d\'email : un par ligne. Les schémas en exp. rég. sont autorisés.',

	'Class:MailInboxStandard/Attribute:oauth_provider' => 'Oauth provider',
	'Class:MailInboxStandard/Attribute:oauth_provider+' => '',
	'Class:MailInboxStandard/Attribute:oauth_client_id' => 'OAuth client',
	'Class:MailInboxStandard/Attribute:oauth_client_id+' => '',
	
	// Attachments - criteria
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width' => 'Largeur min. (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width+' => 'Largeur min. (px). Doit être d\'au minimum 1.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width' => 'Largeur max. (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width+' => 'Largeur max. (px). Mettre à 0 pour ne pas limiter.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height' => 'Hauteur min. (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height+' => 'Hauteur min. (px). Doit être d\'au minimum 1.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height' => 'Hauteur max. (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height+' => 'hauteur max. (px). Mettre à 0 pour ne pas limiter.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes' => 'Exclude MIME types',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes+' => 'Attachments of these MIME types will not be processed. Specify one per line.',
	
	// mail size too large
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior' => 'Comportement en cas d\'infraction',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_delete' => 'Renvoyer à l\'expéditeur et supprimer',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_mark_as_undesired' => 'Renvoyer à l\'expéditeur et marquer comme indésirable',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:delete' => 'Supprimer',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:do_nothing' => 'Ne rien faire',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:mark_as_undesired' => 'Marquer comme indésirable / Garder l\'email temporairement',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_subject' => 'Rejeter l\'objet',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_notification' => 'Rejeter le message',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB' => 'Taille max. (Mo)',
	
	// attachment - forbidden MimeType
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior' => 'Comportement en cas d\'infraction',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:fallback_ignore_forbidden_attachments' => 'Solution de secours : ignorer les PJs interdites',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_delete' => 'Renvoyer à l\'expéditeur et supprimer',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_mark_as_undesired' => 'Renvoyer à l\'expéditeur et marquer comme indésirable',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:delete' => 'Supprimer',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:do_nothing' => 'Ne rien faire',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:mark_as_undesired' => 'Marquer comme indésirable / Garder l\'email temporairement',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_subject' => 'Rejeter l\'objet',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_notification' => 'Rejeter le message',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_mimetypes' => 'Types MIME (un par ligne)',
	
	// Policy: attachment - ignore MIME type
	'Class:MailInboxStandard/Attribute:step_attachment_ignore_mimetypes' => 'Ignore MIME Types (one per line)~~',
	
	// no subject
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior' => 'Comportement en cas d\'infraction',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:fallback_default_subject' => 'Solution de secours : utiliser l\'objet par défaut',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_delete' => 'Renvoyer à l\'expéditeur et supprimer',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_mark_as_undesired' => 'Renvoyer à l\'expéditeur et marquer comme indésirable',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:delete' => 'Supprimer',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:do_nothing' => 'Ne rien faire',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:mark_as_undesired' => 'Marquer comme indésirable / Garder l\'email temporairement',
	'Class:MailInboxStandard/Attribute:policy_no_subject_subject' => 'Rejeter l\'objet',
	'Class:MailInboxStandard/Attribute:policy_no_subject_notification' => 'Rejeter le message',
	'Class:MailInboxStandard/Attribute:policy_no_subject_default_value' => 'Objet par défaut',
	
	// unknown caller
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior' => 'Comportement en cas d\'infraction',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:fallback_create_person' => 'Solution de secours : créer la personne',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_delete' => 'Renvoyer à l\'expéditeur et supprimer',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_mark_as_undesired' => 'Renvoyer à l\'expéditeur et marquer comme indésirable',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:delete' => 'Supprimer',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:mark_as_undesired' =>  'Marquer comme indésirable / Garder l\'email temporairement',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_subject' => 'Rejeter l\'objet',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_notification' => 'Rejeter le message',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_default_values' => 'Valeurs par défaut pour une nouvelle personne (une par ligne, example : org_id:1)',
	
	// other recipients
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior' => 'Comportement en cas d\'infraction',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_existing_other_contacts' => 'Solution de secours : ajouter un contact existant',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_other_contacts' => 'Solution de secours : ajouter le contact / le créer si besoin',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_ignore_other_contacts' => 'Solution de secours : ignorer les autres contacts',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_delete' => 'Renvoyer à l\'expéditeur et supprimer',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_mark_as_undesired' => 'Renvoyer à l\'expéditeur et marquer comme indésirable',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:delete' => 'Supprimer',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:do_nothing' => 'Ne rien faire',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:mark_as_undesired' => 'Marquer comme indésirable / Garder l\'email temporairement',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_subject' => 'Rejeter l\'objet',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_notification' => 'Rejeter le message',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_default_values' => 'Valeurs par défaut pour une nouvelle personne (une par ligne, example : org_id:1)',
	
	// closed ticket
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior' => 'Comportement en cas d\'infraction',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:fallback_reopen' => 'Solution de secours : réouvrir le ticket',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_delete' => 'Renvoyer à l\'expéditeur et supprimer',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_mark_as_undesired' => 'Renvoyer à l\'expéditeur et marquer comme indésirable',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:delete' => 'Supprimer',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:do_nothing' => 'Ne rien faire',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:mark_as_undesired' => 'Marquer comme indésirable / Garder l\'email temporairement',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_subject' => 'Rejeter l\'objet',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_notification' => 'Rejeter le message',
	
	// resolved ticket
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior' => 'Comportement en cas d\'infraction',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:fallback_reopen' => 'Solution de secours : réouvrir le ticket',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_delete' => 'Renvoyer à l\'expéditeur et supprimer',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_mark_as_undesired' => 'Renvoyer à l\'expéditeur et marquer comme indésirable',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:delete' => 'Supprimer',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:do_nothing' => 'Ne rien faire',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:mark_as_undesired' => 'Marquer comme indésirable / Garder l\'email temporairement',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_subject' => 'Rejeter l\'objet',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_notification' => 'Rejeter le message',
	 
	// unknown ticket
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior' => 'Comportement en cas d\'infraction',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_delete' => 'Renvoyer à l\'expéditeur et supprimer',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_mark_as_undesired' => 'Renvoyer à l\'expéditeur et marquer comme indésirable',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:delete' => 'Supprimer',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:do_nothing' => 'Ne rien faire',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:mark_as_undesired' => 'Marquer comme indésirable / Garder l\'email temporairement',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_subject' => 'Rejeter l\'objet',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_notification' => 'Rejeter le message',
	
	// undesired title patterns
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior' => 'Comportement en cas d\'infraction',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_delete' => 'Renvoyer à l\'expéditeur et supprimer',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_mark_as_undesired' => 'Renvoyer à l\'expéditeur et marquer comme indésirable',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:delete' => 'Supprimer',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:do_nothing' => 'Ne rien faire',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:mark_as_undesired' => 'Marquer comme indésirable / Garder l\'email temporairement',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_subject' => 'Rejeter l\'objet',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_notification' => 'Rejeter le message',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_patterns' => 'Schémas indésirables dans l\'objet (Schémas en exp. rég., un par ligne)',
	
	
	// remove parts of title
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior' => 'Retirer les schémas de l\'objet',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:do_nothing' => 'Ne rien faire',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:fallback_remove' => 'Retirer des parties de l\'objet',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_patterns' => 'Schémas à retirer du sujet (Schémas en exp. rég., un par ligne)',
	
	// Auto reply
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior' => 'Behavior on violation',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:delete' => 'Delete the message from the mailbox',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:do_nothing' => 'Do nothing',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:inactive' => 'Inactive',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:mark_as_undesired' => 'Mark as Undesired / Temporarily keep the e-mail',
	
	// Policy: Non Delivery Report
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior' => 'Behavior~~',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:delete' => 'Delete~~',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:do_nothing' => 'Do nothing~~',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:inactive' => 'Inactive~~',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:mark_as_undesired' => 'Mark as undesired~~',
	
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive' => 'Mark caller as inactive~~',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive+' => 'The caller will be marked as inactive, if the mail delivery failure seems to be permanent and there is high confidence the recipient is no longer reachable through this e-mail address.~~',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:yes' => 'Yes~~',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:no' => 'No~~',
	
	// Headers
	'MailInbox:Server' => 'Configuration de la boîte mail',
	'MailInbox:Behavior' => 'Comportement pour les nouveaux emails',
	'MailInbox:Errors' => 'E-mails en erreur',
	'MailInbox:Settings' => 'Configuration', 
	
	// Steps
	'MailInbox:StepAttachmentCriteria' => 'Pièce jointe - criteria',
	'MailInbox:PolicyMailSize' => 'Taille de l\'email',
	'MailInbox:PolicyAttachmentIgnoredMimeType' => 'Pièce jointe - type MIME ignoré',
	'MailInbox:PolicyAttachmentForbiddenMimeType' => 'Pièce jointe - type MIME interdit',
	'MailInbox:PolicyUndesiredPattern' => 'Schémas indésirables dans l\'objet',
	'MailInbox:PolicyRemovePattern' => 'Schémas retirés dans l\'objet',
	'MailInbox:PolicyIgnorePattern' => 'Schémas ignorés dans l\'objet',
	'MailInbox:PolicyResolvedTicket' => 'Tickets résolus',
	'MailInbox:PolicyClosedTicket' => 'Tickets clos',
	'MailInbox:PolicyUnknownTicket' => 'Tickets inconnus',
	'MailInbox:PolicyNoSubject' => 'Pas d\'objet',
	'MailInbox:PolicyUnknownCaller' => 'Bénéficiaire inconnu',
	'MailInbox:PolicyOtherRecipients' => 'Autres destinataires',
	'MailInbox:PolicyAutoReply' => 'Auto réponse',
	'MailInbox:PolicyNonDeliveryReport' => 'Non Delivery Reports~~',
	
	'Menu:MailInboxes' => 'Boîtes emails de réception',
	'Menu:MailInboxes+' => 'Configuration des boîtes emails à scanner pour de nouveaux messages',
	 
	'MailInboxStandard:DebugTrace' => 'Trace de debug',
	'MailInboxStandard:DebugTraceNotActive' => 'Activer la trace de debug de cette boîte email pour avoir un journal détaillé de ce qui se produit.',
	
	'MailPolicy:CreateOrUpdateTicket:NoDescriptionProvided' => 'Aucune description',
	
	// lnkEmailUidToTicket
	'Class:lnkEmailUidToTicket' => 'Link Email UID / Ticket~~',
	'Class:lnkEmailUidToTicket/Attribute:message_uid' => 'Message UID~~',
	'Class:lnkEmailUidToTicket/Attribute:ticket_id' => 'Ticket ID~~',
	'Class:lnkEmailUidToTicket/Attribute:mailbox_id' => 'Mailbox ID~~',
	'Class:lnkEmailUidToTicket/UniquenessRule:unique_message_uid_and_mailbox_id_and_ticket_id' => 'The combination of mailbox ID, message UID and ticket ID must be unique.~~',
	
));

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
	'Class:MailInboxStandard' => 'Casella di posta IMAP',
	'Class:MailInboxStandard+' => 'Origine delle e-mail in entrata',
	'Class:MailInboxStandard/Attribute:behavior' => 'Comportamento durante l\'elaborazione di un\'e-mail',
	'Class:MailInboxStandard/Attribute:behavior/Value:create_only' => 'Creare nuovi ticket',
	'Class:MailInboxStandard/Attribute:behavior/Value:update_only' => 'Aggiornare i ticket esistenti',
	'Class:MailInboxStandard/Attribute:behavior/Value:both' => 'Creare o aggiornare i ticket',

	'Class:MailInboxStandard/Attribute:email_storage' => 'Dopo l\'elaborazione dell\'e-mail',
	'Class:MailInboxStandard/Attribute:email_storage+' => 'Azione da eseguire dopo l\'elaborazione dell\'e-mail. Per prestazioni ottimali: se si desidera archiviare, si consiglia di spostare le e-mail elaborate correttamente in un\'altra cartella.',
	'Class:MailInboxStandard/Attribute:email_storage/Value:keep' => 'Mantenerla nella stessa cartella',
	'Class:MailInboxStandard/Attribute:email_storage/Value:delete' => 'Eliminarla immediatamente',
	'Class:MailInboxStandard/Attribute:email_storage/Value:move' => 'Spostarla in un\'altra cartella',

	'Class:MailInboxStandard/Attribute:target_class' => 'Classe del ticket',
	'Class:MailInboxStandard/Attribute:target_class/Value:Incident' => 'Incidente',
	'Class:MailInboxStandard/Attribute:target_class/Value:UserRequest' => 'Richiesta utente',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change' => 'Cambiamento',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange' => 'Cambiamento di routine',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange' => 'Cambiamento normale',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange' => 'Cambiamento d\'emergenza',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem' => 'Problema',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem+' => '',
	'Class:MailInboxStandard/Attribute:debug_trace' => 'Traccia di debug',
	'Class:MailInboxStandard/Attribute:debug_trace+' => '',
	'Class:MailInboxStandard/Attribute:target_folder' => 'Cartella di destinazione',
	'Class:MailInboxStandard/Attribute:target_folder+' => 'L\'e-mail verrà spostata (protocollo IMAP) in questa cartella di destinazione dopo essere stata elaborata. Ricordarsi di aggiornare l\'impostazione "Dopo l\'elaborazione dell\'e-mail" su "Spostarla in un\'altra cartella".',

	'Class:MailInboxStandard/Attribute:attcode_description' => 'Attributo di descrizione',
	'Class:MailInboxStandard/Attribute:attcode_description+' => 'Codice dell\'attributo della classe di destinazione che deve ricevere la descrizione iniziale del ticket. Predefinito: "description" se lasciato vuoto.',
	'Class:MailInboxStandard/Attribute:attcode_caselog' => 'Attributo del diario (case log)',
	'Class:MailInboxStandard/Attribute:attcode_caselog+' => 'Codice dell\'attributo della classe di destinazione (diario) che deve ricevere le nuove voci alla creazione e/o all\'aggiornamento del ticket. Predefinito: "public_log" se lasciato vuoto o non valido.',

	'Class:MailInboxStandard/Attribute:ticket_default_values' => 'Valori predefiniti per il nuovo ticket',
	'Class:MailInboxStandard/Attribute:ticket_default_title' => 'Titolo predefinito (se l\'oggetto è vuoto)',
	'Class:MailInboxStandard/Attribute:title_pattern+' => 'Pattern da cercare nell\'oggetto',
	'Class:MailInboxStandard/Attribute:title_pattern' => 'Pattern del titolo',
	'Class:MailInboxStandard/Attribute:title_pattern?' => 'Usare la sintassi PCRE, inclusi i delimitatori di inizio e fine, per specificare l\'aspetto del riferimento al ticket (pattern), in modo che le e-mail possano essere collegate ai ticket.',

	'Class:MailInboxStandard/Attribute:title_pattern_ignore_patterns' => 'Ignora pattern nell\'oggetto (pattern di espressione regolare, uno per riga)',

	'Class:MailInboxStandard/Attribute:stimuli' => 'Stimoli da applicare',
	'Class:MailInboxStandard/Attribute:stimuli+' => 'Applicare uno stimolo quando il ticket si trova in un determinato stato',
	'Class:MailInboxStandard/Attribute:stimuli?' => 'Un elenco di codice_stato:codice_stimolo (uno per riga) per definire lo stimolo da applicare (solo dopo l\'aggiornamento di un ticket esistente), per lo stato indicato del ticket. Ciò è utile, ad esempio, per riassegnare automaticamente un ticket che si trova nello stato "in attesa". Usare il formato <codice_stato>:<codice_stimolo>',


	'Class:MailInboxStandard/Attribute:trace' => 'Traccia di debug',
	'Class:MailInboxStandard/Attribute:trace/Value:yes' => 'Sì',
	'Class:MailInboxStandard/Attribute:trace/Value:no' => 'No',

	'Class:MailInboxStandard/Attribute:debug_log' => 'Registro di debug',

	'Class:MailInboxStandard/Attribute:error_behavior' => 'Comportamento in caso di errore durante l\'elaborazione',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:delete' => 'Eliminare il messaggio dalla casella di posta',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:mark_as_error' => 'Contrassegnare come errore',
	'Class:MailInboxStandard/Attribute:notify_errors_to' => 'Contatti da notificare in caso di errore',
	'Class:MailInboxStandard/Attribute:notify_errors_to+' => 'Query OQL che restituisce la/e Persona/e (es. "SELECT Person WHERE email = \'admin@example.com\'") a cui verranno inoltrate le e-mail in errore.',
	'Class:MailInboxStandard/Attribute:notify_from' => 'Indirizzo del mittente',

	'Class:MailInboxStandard/Attribute:mail_aliases' => 'Alias e-mail',
	'Class:MailInboxStandard/Attribute:mail_aliases+' => 'Alias e-mail: uno per riga. Sono ammessi pattern di espressione regolare.',

	'Class:MailInboxStandard/Attribute:authentication_results_authserv_id' => 'ID del server Authentication-Results attendibile',
	'Class:MailInboxStandard/Attribute:authentication_results_authserv_id+' => 'Nome host (authserv-id) del server di posta che autentica effettivamente la posta in entrata per questa casella di posta (es. "mx.google.com"). Se impostato, i controlli SPF/DKIM (usati per fidarsi dell\'indirizzo del mittente) considerano solo l\'intestazione "Authentication-Results" aggiunta da questo server, ignorando qualsiasi altra occorrenza, che potrebbe altrimenti essere falsificata dal mittente. Lasciare vuoto per fidarsi della prima occorrenza riscontrata (comportamento precedente).',

	'Class:MailInboxStandard/Attribute:oauth_provider' => 'Provider OAuth',
	'Class:MailInboxStandard/Attribute:oauth_provider+' => '',
	'Class:MailInboxStandard/Attribute:oauth_client_id' => 'Client OAuth',
	'Class:MailInboxStandard/Attribute:oauth_client_id+' => '',

	// Politica: Criteri degli allegati
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width' => 'Larghezza min. (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width+' => 'Larghezza minima dell\'immagine (px). Deve essere almeno 1. Le immagini troppo piccole non verranno elaborate.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width' => 'Larghezza max. (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width+' => 'Larghezza massima dell\'immagine (px). Impostare su 0 per accettare qualsiasi larghezza. Se l\'estensione php-gd è installata, le immagini più grandi verranno ridimensionate. Altrimenti non verranno elaborate.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height' => 'Altezza min. (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height+' => 'Altezza minima (px). Deve essere almeno 1.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height' => 'Altezza max. (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height+' => 'Altezza massima (px). Impostare su 0 per accettare qualsiasi altezza. Se l\'estensione php-gd è installata, le immagini più grandi verranno ridimensionate. Altrimenti non verranno elaborate.',

	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes' => 'Escludi tipi MIME',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes+' => 'Gli allegati di questi tipi MIME non verranno elaborati. Specificarne uno per riga.',

	// Politica: verifica DKIM
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior' => 'Comportamento in caso di violazione',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:bounce_delete' => 'Respingere al mittente ed eliminare',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:bounce_mark_as_undesired' => 'Respingere al mittente e contrassegnare come indesiderata',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:delete' => 'Eliminare il messaggio dalla casella di posta',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:do_nothing' => 'Non fare nulla',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:inactive' => 'Inattivo',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:mark_as_undesired' => 'Contrassegnare come indesiderata / Mantenere l\'e-mail temporaneamente',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_subject' => 'Oggetto del respingimento',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_notification' => 'Messaggio di respingimento',

	// Politica: e-mail troppo grande
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior' => 'Comportamento in caso di violazione',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_delete' => 'Respingere al mittente ed eliminare',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_mark_as_undesired' => 'Respingere al mittente e contrassegnare come indesiderata',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:delete' => 'Eliminare il messaggio dalla casella di posta',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:do_nothing' => 'Non fare nulla',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:inactive' => 'Inattivo',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:mark_as_undesired' => 'Contrassegnare come indesiderata / Mantenere l\'e-mail temporaneamente',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_subject' => 'Oggetto del respingimento',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_notification' => 'Messaggio di respingimento',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB' => 'Dimensione max. (MB)',
	'Error:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB+' => 'Dimensione massima dell\'e-mail e dei suoi allegati. Le e-mail più grandi non verranno elaborate. Impostare su 0 per disattivare.',

	// Politica: allegato - tipo MIME vietato
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior' => 'Comportamento in caso di violazione',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:fallback_ignore_forbidden_attachments' => 'Soluzione alternativa: ignorare gli allegati vietati',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_delete' => 'Respingere al mittente ed eliminare',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_mark_as_undesired' => 'Respingere al mittente e contrassegnare come indesiderata',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:delete' => 'Eliminare il messaggio dalla casella di posta',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:do_nothing' => 'Non fare nulla',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:inactive' => 'Inattivo',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:mark_as_undesired' => 'Contrassegnare come indesiderata / Mantenere l\'e-mail temporaneamente',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_subject' => 'Oggetto del respingimento',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_notification' => 'Messaggio di respingimento',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_mimetypes' => 'Tipi MIME (uno per riga)',

	// Politica: nessun oggetto
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior' => 'Comportamento in caso di violazione',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:fallback_default_subject' => 'Soluzione alternativa: usare l\'oggetto predefinito',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_delete' => 'Respingere al mittente ed eliminare',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_mark_as_undesired' => 'Respingere al mittente e contrassegnare come indesiderata',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:delete' => 'Eliminare il messaggio dalla casella di posta',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:do_nothing' => 'Non fare nulla',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:inactive' => 'Inattivo',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:mark_as_undesired' => 'Contrassegnare come indesiderata / Mantenere l\'e-mail temporaneamente',
	'Class:MailInboxStandard/Attribute:policy_no_subject_subject' => 'Oggetto del respingimento',
	'Class:MailInboxStandard/Attribute:policy_no_subject_notification' => 'Messaggio di respingimento',
	'Class:MailInboxStandard/Attribute:policy_no_subject_default_value' => 'Oggetto predefinito',

	// Politica: mittente sconosciuto
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior' => 'Comportamento in caso di violazione',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:fallback_create_person' => 'Soluzione alternativa: creare la persona',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_delete' => 'Respingere al mittente ed eliminare',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_mark_as_undesired' => 'Respingere al mittente e contrassegnare come indesiderata',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:delete' => 'Eliminare il messaggio dalla casella di posta',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:mark_as_undesired' => 'Contrassegnare come indesiderata / Mantenere l\'e-mail temporaneamente',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_subject' => 'Oggetto del respingimento',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_notification' => 'Messaggio di respingimento',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_default_values' => 'Valori predefiniti per la nuova persona (uno per riga, esempio: org_id:1)',

	// Politica: altri destinatari
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior' => 'Comportamento in caso di violazione',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_existing_other_contacts' => 'Soluzione alternativa: collegare solo i contatti esistenti',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_other_contacts' => 'Soluzione alternativa: collegare sempre il contatto, creandolo se necessario',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_ignore_other_contacts' => 'Soluzione alternativa: ignorare tutti gli altri contatti',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_delete' => 'Respingere al mittente ed eliminare',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_mark_as_undesired' => 'Respingere al mittente e contrassegnare come indesiderata',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:delete' => 'Eliminare il messaggio dalla casella di posta',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:do_nothing' => 'Non fare nulla',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:inactive' => 'Inattivo',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:mark_as_undesired' => 'Contrassegnare come indesiderata / Mantenere l\'e-mail temporaneamente',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_subject' => 'Oggetto del respingimento',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_notification' => 'Messaggio di respingimento',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_default_values' => 'Valori predefiniti per la nuova persona (uno per riga, esempio: org_id:1)',

	// Politica: ticket chiuso
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior' => 'Comportamento in caso di violazione',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior+' => 'Nota: per impostazione predefinita i ticket chiusi non possono essere riaperti. Ciò richiede modifiche al modello dati.',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:fallback_reopen' => 'Soluzione alternativa: riaprire il ticket',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_delete' => 'Respingere al mittente ed eliminare',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_mark_as_undesired' => 'Respingere al mittente e contrassegnare come indesiderata',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:delete' => 'Eliminare il messaggio dalla casella di posta',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:do_nothing' => 'Non fare nulla',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:inactive' => 'Inattivo',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:mark_as_undesired' => 'Contrassegnare come indesiderata / Mantenere l\'e-mail temporaneamente',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_subject' => 'Oggetto del respingimento',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_notification' => 'Messaggio di respingimento',

	// Politica: ticket risolto
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior' => 'Comportamento in caso di violazione',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:fallback_reopen' => 'Soluzione alternativa: riaprire il ticket',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_delete' => 'Respingere al mittente ed eliminare',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_mark_as_undesired' => 'Respingere al mittente e contrassegnare come indesiderata',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:delete' => 'Eliminare il messaggio dalla casella di posta',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:do_nothing' => 'Non fare nulla',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:inactive' => 'Inattivo',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:mark_as_undesired' => 'Contrassegnare come indesiderata / Mantenere l\'e-mail temporaneamente',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_subject' => 'Oggetto del respingimento',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_notification' => 'Messaggio di respingimento',

	// Politica: ticket sconosciuto
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior' => 'Comportamento in caso di violazione',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_delete' => 'Respingere al mittente ed eliminare',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_mark_as_undesired' => 'Respingere al mittente e contrassegnare come indesiderata',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:delete' => 'Eliminare il messaggio dalla casella di posta',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:do_nothing' => 'Non fare nulla',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:inactive' => 'Inattivo',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:mark_as_undesired' => 'Contrassegnare come indesiderata / Mantenere l\'e-mail temporaneamente',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_subject' => 'Oggetto del respingimento',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_notification' => 'Messaggio di respingimento',

	// Politica: pattern del titolo indesiderati
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior' => 'Comportamento in caso di violazione',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:delete' => 'Eliminare il messaggio dalla casella di posta',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_delete' => 'Respingere al mittente ed eliminare',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_mark_as_undesired' => 'Respingere al mittente e contrassegnare come indesiderata',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:do_nothing' => 'Non fare nulla',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:inactive' => 'Inattivo',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:mark_as_undesired' => 'Contrassegnare come indesiderata / Mantenere l\'e-mail temporaneamente',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_subject' => 'Oggetto del respingimento',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_notification' => 'Messaggio di respingimento',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_patterns' => 'Pattern indesiderati nell\'oggetto (pattern di espressione regolare, uno per riga)',


	// Politica: rimuovere parti del titolo
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior' => 'Rimuovere pattern dall\'oggetto',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:do_nothing' => 'Non fare nulla',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:inactive' => 'Inattivo',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:fallback_remove' => 'Rimuovere parte/i dell\'oggetto',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_patterns' => 'Pattern da rimuovere dall\'oggetto (pattern di espressione regolare, uno per riga)',

	// Politica: il mittente deve essere lo stesso del ticket originale
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior' => 'Comportamento in caso di violazione',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:delete' => 'Eliminare il messaggio dalla casella di posta',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_delete' => 'Respingere al mittente ed eliminare',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_mark_as_undesired' => 'Respingere al mittente e contrassegnare come indesiderata',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:do_nothing' => 'Non fare nulla',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:inactive' => 'Inattivo',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:mark_as_undesired' => 'Contrassegnare come indesiderata / Mantenere l\'e-mail temporaneamente',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_subject' => 'Oggetto del respingimento',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_notification' => 'Messaggio di respingimento',

	// Politica: risposta automatica
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior' => 'Comportamento in caso di violazione',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:delete' => 'Eliminare il messaggio dalla casella di posta',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:do_nothing' => 'Non fare nulla',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:inactive' => 'Inattivo',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:mark_as_undesired' => 'Contrassegnare come indesiderata / Mantenere l\'e-mail temporaneamente',

	// Politica: avviso di mancato recapito
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior' => 'Comportamento in caso di violazione',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:delete' => 'Eliminare il messaggio dalla casella di posta',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:do_nothing' => 'Non fare nulla',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:inactive' => 'Inattivo',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:mark_as_undesired' => 'Contrassegnare come indesiderata',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive' => 'Contrassegnare il mittente come inattivo',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive+' => 'Il mittente verrà contrassegnato come inattivo se l\'errore di consegna della posta sembra permanente e c\'è un\'elevata sicurezza che il destinatario non sia più raggiungibile tramite questo indirizzo e-mail.',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:yes' => 'Sì',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:no' => 'No',

	// Passo: aggiornare gli attributi del mittente
	'Class:MailInboxStandard/Attribute:step_update_caller_attributes' => 'Aggiornare gli attributi del mittente (uno per riga, esempio: status:active)',
	'Class:MailInboxStandard/Attribute:step_update_caller_attributes+' => 'Applicato al record Persona del mittente ogni volta che un\'e-mail elaborata correttamente viene abbinata a un contatto esistente. Lasciare vuoto per non aggiornare nulla. Nei valori è possibile usare i segnaposto della posta (es. $mail->caller_email$).',

	// Politica: indirizzo e-mail del mittente
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior' => 'Comportamento in caso di violazione',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_delete' => 'Respingere al mittente ed eliminare',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_mark_as_undesired' => 'Respingere al mittente e contrassegnare come indesiderata',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:delete' => 'Eliminare il messaggio dalla casella di posta',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:do_nothing' => 'Non fare nulla',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:inactive' => 'Inattivo',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:mark_as_undesired' => 'Contrassegnare come indesiderata',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_subject' => 'Oggetto del respingimento',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_notification' => 'Messaggio di respingimento',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns' => 'Pattern',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns+' => 'Qualsiasi e-mail il cui indirizzo del mittente corrisponda a uno dei pattern di espressione regolare definiti (uno per riga) sarà considerata una violazione.',



	// Intestazioni
	'MailInbox:Server' => 'Configurazione della casella di posta',
	'MailInbox:Behavior' => 'Comportamento per le e-mail in entrata',
	'MailInbox:Errors' => 'E-mail in errore',
	'MailInbox:Settings' => 'Impostazioni',

	// Messaggi di convalida
	'MailInbox:Error:TargetFolderRequired' => 'La cartella di destinazione deve essere specificata per una casella di posta attiva.',
	'MailInbox:Error:CaseLogAttCodeRequired' => 'Il codice dell\'attributo del diario deve essere un attributo valido della classe di destinazione \'%1$s\'.',
	'MailInbox:Error:DescriptionOrCaseLogAttCodeRequired' => 'Il codice dell\'attributo di descrizione o il codice dell\'attributo del diario deve essere un attributo valido della classe di destinazione \'%1$s\'.',
	'MailInbox:Error:DescriptionAttCodeMustHaveMaxSize' => 'L\'attributo di descrizione \'%1$s\' della classe di destinazione \'%2$s\' non ha una dimensione massima e non può essere usato per memorizzare la descrizione iniziale del ticket.',

	// Passi
	'MailInbox:StepAttachmentCriteria' => 'Immagini incorporate nell\'e-mail',
	'MailInbox:PolicyDkimCheck' => 'Verifica DKIM',
	'MailInbox:PolicyMailSize' => 'Dimensione dell\'e-mail',
	'MailInbox:PolicyAttachmentForbiddenMimeType' => 'Allegato - Tipo MIME vietato',
	'MailInbox:PolicyAttachmentIgnoredMimeType' => 'Allegato - Tipo MIME ignorato',
	'MailInbox:PolicyUndesiredPattern' => 'Pattern del titolo indesiderati',
	'MailInbox:PolicyRemovePattern' => 'Rimuovere pattern del titolo indesiderati',
	'MailInbox:PolicyIgnorePattern' => 'Ignorare pattern del titolo indesiderati',
	'MailInbox:PolicyResolvedTicket' => 'Ticket risolti',
	'MailInbox:PolicyClosedTicket' => 'Ticket chiusi',
	'MailInbox:PolicyUnknownTicket' => 'Ticket sconosciuti',
	'MailInbox:PolicyNoSubject' => 'Nessun oggetto',
	'MailInbox:PolicyUnknownCaller' => 'Mittente sconosciuto',
	'MailInbox:PolicyOtherRecipients' => 'Altri destinatari specificati in A: o CC:',
	'MailInbox:PolicyBounceOtherEmailCallerThanTicketCaller' => 'Limitare le risposte e-mail accettate all\'indirizzo e-mail del mittente originale del ticket',
	'MailInbox:PolicyAutoReply' => 'Risposta automatica',
	'MailInbox:PolicyNonDeliveryReport' => 'Avvisi di mancato recapito',
	'MailInbox:StepUpdateCallerAttributes' => 'Aggiornare gli attributi del mittente',
	'MailInbox:PolicySenderEmailAddress' => 'Bloccare i mittenti usando pattern di indirizzo e-mail',

	// Messaggi di convalida
	'MailInbox:Error:NotifyErrorsToMustTargetContact' => 'La query per selezionare %1$s deve avere come destinazione la classe \'Contact\' (o una sottoclasse come \'Person\' o \'Team\'), non \'%2$s\'.',

	'Menu:MailInboxes' => 'Caselle di posta in entrata',
	'Menu:MailInboxes+' => 'Configurazione delle caselle di posta da analizzare per le e-mail in entrata',

	'MailInboxStandard:DebugTrace' => 'Traccia di debug',
	'MailInboxStandard:DebugTraceNotActive' => 'Attivare la traccia di debug su questa casella di posta per visualizzare un registro dettagliato di ciò che accade.',
	'MailInboxStandard:DebugTraceAccessDenied' => 'Non si dispone dell\'autorizzazione per visualizzare la traccia di debug di questa casella di posta.',

	'MailPolicy:CreateOrUpdateTicket:NoDescriptionProvided' => 'Nessuna descrizione fornita',

	// OAuth2
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Title' => 'Creare una casella di posta',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Description' => 'Creare una nuova casella di posta per recuperare le e-mail da un provider di posta remoto usando questa connessione OAuth come metodo di autenticazione',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:CreateNewMailbox' => 'Creare una nuova casella di posta',
	'UI:OAuthEmailSynchro:Error:UnknownVendor' => 'Il provider OAuth %1$s non esiste',

	// lnkEmailUidToTicket
	'Class:lnkEmailUidToTicket' => 'Collegamento UID e-mail / Ticket',
	'Class:lnkEmailUidToTicket/Attribute:message_uid' => 'UID del messaggio',
	'Class:lnkEmailUidToTicket/Attribute:ticket_id' => 'ID ticket',
	'Class:lnkEmailUidToTicket/Attribute:mailbox_id' => 'ID casella di posta',
	'Class:lnkEmailUidToTicket/UniquenessRule:unique_message_uid_and_mailbox_id_and_ticket_id' => 'La combinazione di ID casella di posta, UID messaggio e ID ticket deve essere univoca.',


));

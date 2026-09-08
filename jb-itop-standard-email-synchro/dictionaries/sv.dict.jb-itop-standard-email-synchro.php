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
	'Class:MailInboxStandard' => 'IMAP-brevlåda',
	'Class:MailInboxStandard+' => 'Källa för inkommande e-post',
	'Class:MailInboxStandard/Attribute:behavior' => 'Beteende vid behandling av ett e-postmeddelande',
	'Class:MailInboxStandard/Attribute:behavior/Value:create_only' => 'Skapa nya ärenden',
	'Class:MailInboxStandard/Attribute:behavior/Value:update_only' => 'Uppdatera befintliga ärenden',
	'Class:MailInboxStandard/Attribute:behavior/Value:both' => 'Skapa eller uppdatera ärenden',

	'Class:MailInboxStandard/Attribute:email_storage' => 'Efter behandling av e-postmeddelandet',
	'Class:MailInboxStandard/Attribute:email_storage+' => 'Åtgärd som ska utföras efter behandling av e-postmeddelandet. För bästa prestanda: om arkivering önskas rekommenderas det att flytta framgångsrikt behandlade e-postmeddelanden till en annan mapp.',
	'Class:MailInboxStandard/Attribute:email_storage/Value:keep' => 'Behåll det i samma mapp',
	'Class:MailInboxStandard/Attribute:email_storage/Value:delete' => 'Radera det omedelbart',
	'Class:MailInboxStandard/Attribute:email_storage/Value:move' => 'Flytta till en annan mapp',

	'Class:MailInboxStandard/Attribute:target_class' => 'Ärendeklass',
	'Class:MailInboxStandard/Attribute:target_class/Value:Incident' => 'Incident',
	'Class:MailInboxStandard/Attribute:target_class/Value:UserRequest' => 'Användarförfrågan',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change' => 'Ändring',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange' => 'Rutinändring',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange' => 'Normal ändring',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange' => 'Akut ändring',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem' => 'Problem',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem+' => '',
	'Class:MailInboxStandard/Attribute:debug_trace' => 'Felsökningsspår',
	'Class:MailInboxStandard/Attribute:debug_trace+' => '',
	'Class:MailInboxStandard/Attribute:target_folder' => 'Målmapp',
	'Class:MailInboxStandard/Attribute:target_folder+' => 'E-postmeddelandet flyttas (IMAP-protokoll) till denna målmapp efter behandling. Kom ihåg att uppdatera inställningen "Efter behandling av e-postmeddelandet" till "Flytta till en annan mapp".',

	'Class:MailInboxStandard/Attribute:attcode_description' => 'Beskrivningsattribut',
	'Class:MailInboxStandard/Attribute:attcode_description+' => 'Attributkoden för målklassen som ska ta emot ärendets ursprungliga beskrivning. Standard: "description" om tomt.',
	'Class:MailInboxStandard/Attribute:attcode_caselog' => 'Ärendeloggens attribut',
	'Class:MailInboxStandard/Attribute:attcode_caselog+' => 'Attributkoden för målklassen (ärendelogg) som ska ta emot nya poster när ärendet skapas och/eller uppdateras. Standard: "public_log" om tomt eller ogiltigt.',

	'Class:MailInboxStandard/Attribute:ticket_default_values' => 'Standardvärden för nytt ärende',
	'Class:MailInboxStandard/Attribute:ticket_default_title' => 'Standardtitel (om ämnet är tomt)',
	'Class:MailInboxStandard/Attribute:title_pattern+' => 'Mönster att matcha i ämnet',
	'Class:MailInboxStandard/Attribute:title_pattern' => 'Titelmönster',
	'Class:MailInboxStandard/Attribute:title_pattern?' => 'Använd PCRE-syntax, inklusive start- och slutavgränsare, för att ange hur ärendereferensen (mönstret) ser ut så att e-postmeddelanden kan kopplas till ärenden.',

	'Class:MailInboxStandard/Attribute:title_pattern_ignore_patterns' => 'Ignorera mönster i ämnet (reguljära uttryck, ett per rad)',

	'Class:MailInboxStandard/Attribute:stimuli' => 'Stimuli att tillämpa',
	'Class:MailInboxStandard/Attribute:stimuli+' => 'Tillämpa en stimulus när ärendet är i ett visst tillstånd',
	'Class:MailInboxStandard/Attribute:stimuli?' => 'En lista med tillståndskod:stimulikod (en per rad) för att definiera vilken stimulus som ska tillämpas (endast efter uppdatering av ett befintligt ärende), för det givna tillståndet på ärendet. Detta är till exempel användbart för att automatiskt omtilldela ett ärende som är i tillståndet "väntande". Använd formatet <tillståndskod>:<stimulikod>',


	'Class:MailInboxStandard/Attribute:trace' => 'Felsökningsspår',
	'Class:MailInboxStandard/Attribute:trace/Value:yes' => 'Ja',
	'Class:MailInboxStandard/Attribute:trace/Value:no' => 'Nej',

	'Class:MailInboxStandard/Attribute:debug_log' => 'Felsökningslogg',

	'Class:MailInboxStandard/Attribute:error_behavior' => 'Beteende när ett fel uppstår under behandling',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:delete' => 'Radera meddelandet från brevlådan',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:mark_as_error' => 'Markera som fel',
	'Class:MailInboxStandard/Attribute:notify_errors_to' => 'Kontakter att meddela vid fel',
	'Class:MailInboxStandard/Attribute:notify_errors_to+' => 'OQL-fråga som returnerar den/de person(er) (t.ex. "SELECT Person WHERE email = \'admin@example.com\'") som e-postmeddelanden med fel vidarebefordras till.',
	'Class:MailInboxStandard/Attribute:notify_from' => 'Avsändaradress',

	'Class:MailInboxStandard/Attribute:mail_aliases' => 'E-postalias',
	'Class:MailInboxStandard/Attribute:mail_aliases+' => 'E-postalias: ett per rad. Reguljära uttryck tillåts.',

	'Class:MailInboxStandard/Attribute:authentication_results_authserv_id' => 'Betrott Authentication-Results-server-ID',
	'Class:MailInboxStandard/Attribute:authentication_results_authserv_id+' => 'Värdnamn (authserv-id) för den e-postserver som faktiskt autentiserar inkommande post för denna brevlåda (t.ex. "mx.google.com"). Om angivet tar SPF/DKIM-kontroller (används för att lita på avsändarens adress) endast hänsyn till "Authentication-Results"-huvudet som lagts till av denna server, och ignorerar alla andra förekomster, som annars skulle kunna förfalskas av avsändaren. Lämna tomt för att lita på den första förekomsten (tidigare beteende).',

	'Class:MailInboxStandard/Attribute:oauth_provider' => 'OAuth-leverantör',
	'Class:MailInboxStandard/Attribute:oauth_provider+' => '',
	'Class:MailInboxStandard/Attribute:oauth_client_id' => 'OAuth-klient',
	'Class:MailInboxStandard/Attribute:oauth_client_id+' => '',

	// Princip: Bilagekriterier
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width' => 'Min. bredd (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width+' => 'Minsta bildbredd (px). Måste vara minst 1. Bilder som är för små behandlas inte.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width' => 'Max. bredd (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width+' => 'Största bildbredd (px). Ange 0 för att acceptera vilken bredd som helst. Om php-gd-tillägget är installerat kommer större bilder att ändras i storlek. Annars behandlas de inte.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height' => 'Min. höjd (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height+' => 'Minsta höjd (px). Måste vara minst 1.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height' => 'Max. höjd (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height+' => 'Största höjd (px). Ange 0 för att acceptera vilken höjd som helst. Om php-gd-tillägget är installerat kommer större bilder att ändras i storlek. Annars behandlas de inte.',

	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes' => 'Uteslut MIME-typer',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes+' => 'Bilagor av dessa MIME-typer kommer inte att behandlas. Ange en per rad.',

	// Princip: DKIM-kontroll
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior' => 'Beteende vid överträdelse',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:bounce_delete' => 'Avvisa till avsändaren och radera',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:bounce_mark_as_undesired' => 'Avvisa till avsändaren och markera som oönskad',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:delete' => 'Radera meddelandet från brevlådan',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:do_nothing' => 'Gör ingenting',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:mark_as_undesired' => 'Markera som oönskad / Behåll e-postmeddelandet tillfälligt',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_subject' => 'Avvisningsämne',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_notification' => 'Avvisningsmeddelande',

	// Princip: e-postmeddelande för stort
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior' => 'Beteende vid överträdelse',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_delete' => 'Avvisa till avsändaren och radera',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_mark_as_undesired' => 'Avvisa till avsändaren och markera som oönskad',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:delete' => 'Radera meddelandet från brevlådan',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:do_nothing' => 'Gör ingenting',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:mark_as_undesired' => 'Markera som oönskad / Behåll e-postmeddelandet tillfälligt',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_subject' => 'Avvisningsämne',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_notification' => 'Avvisningsmeddelande',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB' => 'Max. storlek (MB)',
	'Error:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB+' => 'Maximal storlek på e-postmeddelandet och dess bilagor. Större e-postmeddelanden behandlas inte. Ange 0 för att inaktivera.',

	// Princip: bilaga - förbjuden MIME-typ
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior' => 'Beteende vid överträdelse',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:fallback_ignore_forbidden_attachments' => 'Reservlösning: ignorera förbjudna bilagor',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_delete' => 'Avvisa till avsändaren och radera',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_mark_as_undesired' => 'Avvisa till avsändaren och markera som oönskad',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:delete' => 'Radera meddelandet från brevlådan',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:do_nothing' => 'Gör ingenting',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:mark_as_undesired' => 'Markera som oönskad / Behåll e-postmeddelandet tillfälligt',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_subject' => 'Avvisningsämne',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_notification' => 'Avvisningsmeddelande',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_mimetypes' => 'MIME-typer (en per rad)',

	// Princip: inget ämne
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior' => 'Beteende vid överträdelse',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:fallback_default_subject' => 'Reservlösning: använd standardämne',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_delete' => 'Avvisa till avsändaren och radera',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_mark_as_undesired' => 'Avvisa till avsändaren och markera som oönskad',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:delete' => 'Radera meddelandet från brevlådan',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:do_nothing' => 'Gör ingenting',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:mark_as_undesired' => 'Markera som oönskad / Behåll e-postmeddelandet tillfälligt',
	'Class:MailInboxStandard/Attribute:policy_no_subject_subject' => 'Avvisningsämne',
	'Class:MailInboxStandard/Attribute:policy_no_subject_notification' => 'Avvisningsmeddelande',
	'Class:MailInboxStandard/Attribute:policy_no_subject_default_value' => 'Standardämne',

	// Princip: okänd avsändare
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior' => 'Beteende vid överträdelse',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:fallback_create_person' => 'Reservlösning: skapa person',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_delete' => 'Avvisa till avsändaren och radera',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_mark_as_undesired' => 'Avvisa till avsändaren och markera som oönskad',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:delete' => 'Radera meddelandet från brevlådan',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:mark_as_undesired' => 'Markera som oönskad / Behåll e-postmeddelandet tillfälligt',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_subject' => 'Avvisningsämne',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_notification' => 'Avvisningsmeddelande',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_default_values' => 'Standardvärden för ny person (en per rad, exempel: org_id:1)',

	// Princip: andra mottagare
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior' => 'Beteende vid överträdelse',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_existing_other_contacts' => 'Reservlösning: koppla endast befintliga kontakter',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_other_contacts' => 'Reservlösning: koppla alltid kontakt, skapa kontakt vid behov',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_ignore_other_contacts' => 'Reservlösning: ignorera alla andra kontakter',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_delete' => 'Avvisa till avsändaren och radera',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_mark_as_undesired' => 'Avvisa till avsändaren och markera som oönskad',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:delete' => 'Radera meddelandet från brevlådan',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:do_nothing' => 'Gör ingenting',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:mark_as_undesired' => 'Markera som oönskad / Behåll e-postmeddelandet tillfälligt',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_subject' => 'Avvisningsämne',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_notification' => 'Avvisningsmeddelande',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_default_values' => 'Standardvärden för ny person (en per rad, exempel: org_id:1)',

	// Princip: stängt ärende
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior' => 'Beteende vid överträdelse',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior+' => 'Obs: som standard kan stängda ärenden inte återöppnas. Detta kräver ändringar i datamodellen.',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:fallback_reopen' => 'Reservlösning: återöppna ärendet',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_delete' => 'Avvisa till avsändaren och radera',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_mark_as_undesired' => 'Avvisa till avsändaren och markera som oönskad',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:delete' => 'Radera meddelandet från brevlådan',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:do_nothing' => 'Gör ingenting',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:mark_as_undesired' => 'Markera som oönskad / Behåll e-postmeddelandet tillfälligt',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_subject' => 'Avvisningsämne',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_notification' => 'Avvisningsmeddelande',

	// Princip: löst ärende
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior' => 'Beteende vid överträdelse',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:fallback_reopen' => 'Reservlösning: återöppna ärendet',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_delete' => 'Avvisa till avsändaren och radera',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_mark_as_undesired' => 'Avvisa till avsändaren och markera som oönskad',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:delete' => 'Radera meddelandet från brevlådan',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:do_nothing' => 'Gör ingenting',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:mark_as_undesired' => 'Markera som oönskad / Behåll e-postmeddelandet tillfälligt',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_subject' => 'Avvisningsämne',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_notification' => 'Avvisningsmeddelande',

	// Princip: okänt ärende
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior' => 'Beteende vid överträdelse',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_delete' => 'Avvisa till avsändaren och radera',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_mark_as_undesired' => 'Avvisa till avsändaren och markera som oönskad',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:delete' => 'Radera meddelandet från brevlådan',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:do_nothing' => 'Gör ingenting',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:mark_as_undesired' => 'Markera som oönskad / Behåll e-postmeddelandet tillfälligt',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_subject' => 'Avvisningsämne',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_notification' => 'Avvisningsmeddelande',

	// Princip: oönskade titelmönster
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior' => 'Beteende vid överträdelse',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:delete' => 'Radera meddelandet från brevlådan',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_delete' => 'Avvisa till avsändaren och radera',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_mark_as_undesired' => 'Avvisa till avsändaren och markera som oönskad',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:do_nothing' => 'Gör ingenting',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:mark_as_undesired' => 'Markera som oönskad / Behåll e-postmeddelandet tillfälligt',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_subject' => 'Avvisningsämne',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_notification' => 'Avvisningsmeddelande',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_patterns' => 'Oönskade mönster i ämnet (reguljära uttryck, ett per rad)',


	// Princip: ta bort delar av titeln
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior' => 'Ta bort mönster från ämnet',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:do_nothing' => 'Gör ingenting',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:fallback_remove' => 'Ta bort del(ar) av ämnet',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_patterns' => 'Mönster att ta bort från ämnet (reguljära uttryck, ett per rad)',

	// Princip: avsändaren måste vara densamma som ärendets ursprungliga avsändare
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior' => 'Beteende vid överträdelse',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:delete' => 'Radera meddelandet från brevlådan',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_delete' => 'Avvisa till avsändaren och radera',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_mark_as_undesired' => 'Avvisa till avsändaren och markera som oönskad',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:do_nothing' => 'Gör ingenting',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:mark_as_undesired' => 'Markera som oönskad / Behåll e-postmeddelandet tillfälligt',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_subject' => 'Avvisningsämne',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_notification' => 'Avvisningsmeddelande',

	// Princip: automatiskt svar
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior' => 'Beteende vid överträdelse',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:delete' => 'Radera meddelandet från brevlådan',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:do_nothing' => 'Gör ingenting',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:mark_as_undesired' => 'Markera som oönskad / Behåll e-postmeddelandet tillfälligt',

	// Princip: kvitto om utebliven leverans
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior' => 'Beteende vid överträdelse',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:delete' => 'Radera meddelandet från brevlådan',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:do_nothing' => 'Gör ingenting',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:mark_as_undesired' => 'Markera som oönskad',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive' => 'Markera avsändaren som inaktiv',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive+' => 'Avsändaren markeras som inaktiv om leveransfelet verkar vara permanent och det finns hög tillförsikt att mottagaren inte längre kan nås via denna e-postadress.',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:yes' => 'Ja',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:no' => 'Nej',

	// Steg: uppdatera avsändarens attribut
	'Class:MailInboxStandard/Attribute:step_update_caller_attributes' => 'Uppdatera avsändarens attribut (en per rad, exempel: status:active)',
	'Class:MailInboxStandard/Attribute:step_update_caller_attributes+' => 'Tillämpas på avsändarens personpost varje gång ett framgångsrikt behandlat e-postmeddelande matchas mot en befintlig kontakt. Lämna tomt för att inte uppdatera något. E-postplatshållare (t.ex. $mail->caller_email$) kan användas i värdena.',

	// Princip: avsändarens e-postadress
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior' => 'Beteende vid överträdelse',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_delete' => 'Avvisa till avsändaren och radera',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_mark_as_undesired' => 'Avvisa till avsändaren och markera som oönskad',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:delete' => 'Radera meddelandet från brevlådan',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:do_nothing' => 'Gör ingenting',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:mark_as_undesired' => 'Markera som oönskad',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_subject' => 'Avvisningsämne',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_notification' => 'Avvisningsmeddelande',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns' => 'Mönster',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns+' => 'Alla e-postmeddelanden där avsändarens e-postadress matchar ett av de definierade reguljära uttrycken (ett per rad) betraktas som en överträdelse.',



	// Rubriker
	'MailInbox:Server' => 'Brevlådekonfiguration',
	'MailInbox:Behavior' => 'Beteende för inkommande e-post',
	'MailInbox:Errors' => 'E-postmeddelanden med fel',
	'MailInbox:Settings' => 'Inställningar',

	// Valideringsmeddelanden
	'MailInbox:Error:TargetFolderRequired' => 'Målmappen måste anges för en aktiv brevlåda.',
	'MailInbox:Error:CaseLogAttCodeRequired' => 'Ärendeloggens attributkod måste vara ett giltigt attribut för målklassen \'%1$s\'.',
	'MailInbox:Error:DescriptionOrCaseLogAttCodeRequired' => 'Antingen beskrivningsattributkoden eller ärendeloggens attributkod måste vara ett giltigt attribut för målklassen \'%1$s\'.',
	'MailInbox:Error:DescriptionAttCodeMustHaveMaxSize' => 'Beskrivningsattributet \'%1$s\' för målklassen \'%2$s\' har ingen maximal storlek och kan inte användas för att lagra ärendets ursprungliga beskrivning.',

	// Steg
	'MailInbox:StepAttachmentCriteria' => 'Inbäddade e-postbilder',
	'MailInbox:PolicyDkimCheck' => 'DKIM-kontroll',
	'MailInbox:PolicyMailSize' => 'E-poststorlek',
	'MailInbox:PolicyAttachmentForbiddenMimeType' => 'Bilaga - Förbjuden MIME-typ',
	'MailInbox:PolicyAttachmentIgnoredMimeType' => 'Bilaga - Ignorerad MIME-typ',
	'MailInbox:PolicyUndesiredPattern' => 'Oönskade titelmönster',
	'MailInbox:PolicyRemovePattern' => 'Ta bort oönskade titelmönster',
	'MailInbox:PolicyIgnorePattern' => 'Ignorera oönskade titelmönster',
	'MailInbox:PolicyResolvedTicket' => 'Lösta ärenden',
	'MailInbox:PolicyClosedTicket' => 'Stängda ärenden',
	'MailInbox:PolicyUnknownTicket' => 'Okända ärenden',
	'MailInbox:PolicyNoSubject' => 'Inget ämne',
	'MailInbox:PolicyUnknownCaller' => 'Okänd avsändare',
	'MailInbox:PolicyOtherRecipients' => 'Andra mottagare angivna i Till: eller Kopia:',
	'MailInbox:PolicyBounceOtherEmailCallerThanTicketCaller' => 'Begränsa accepterade e-postsvar till ärendets ursprungliga avsändares e-postadress',
	'MailInbox:PolicyAutoReply' => 'Automatiskt svar',
	'MailInbox:PolicyNonDeliveryReport' => 'Kvitton om utebliven leverans',
	'MailInbox:StepUpdateCallerAttributes' => 'Uppdatera avsändarens attribut',
	'MailInbox:PolicySenderEmailAddress' => 'Blockera avsändare med mönster för e-postadresser',

	// Valideringsmeddelanden
	'MailInbox:Error:NotifyErrorsToMustTargetContact' => 'Frågan för att välja %1$s måste rikta sig mot klassen \'Contact\' (eller en underklass som \'Person\' eller \'Team\'), inte \'%2$s\'.',

	'Menu:MailInboxes' => 'Brevlådor för inkommande e-post',
	'Menu:MailInboxes+' => 'Konfiguration av brevlådor som ska genomsökas efter inkommande e-post',

	'MailInboxStandard:DebugTrace' => 'Felsökningsspår',
	'MailInboxStandard:DebugTraceNotActive' => 'Aktivera felsökningsspåret på denna brevlåda för att se en detaljerad logg över vad som händer.',
	'MailInboxStandard:DebugTraceAccessDenied' => 'Du har inte behörighet att visa felsökningsspåret för denna brevlåda.',

	'MailPolicy:CreateOrUpdateTicket:NoDescriptionProvided' => 'Ingen beskrivning angiven',

	// OAuth2
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Title' => 'Skapa en brevlåda',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Description' => 'Skapa en ny brevlåda för att hämta e-post från en fjärransluten e-postleverantör med denna OAuth-anslutning som autentiseringsmetod',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:CreateNewMailbox' => 'Skapa en ny brevlåda',
	'UI:OAuthEmailSynchro:Error:UnknownVendor' => 'OAuth-leverantören %1$s finns inte',

	// lnkEmailUidToTicket
	'Class:lnkEmailUidToTicket' => 'Koppling e-post-UID / ärende',
	'Class:lnkEmailUidToTicket/Attribute:message_uid' => 'Meddelandets UID',
	'Class:lnkEmailUidToTicket/Attribute:ticket_id' => 'Ärende-ID',
	'Class:lnkEmailUidToTicket/Attribute:mailbox_id' => 'Brevlåde-ID',
	'Class:lnkEmailUidToTicket/UniquenessRule:unique_message_uid_and_mailbox_id_and_ticket_id' => 'Kombinationen av brevlåde-ID, meddelande-UID och ärende-ID måste vara unik.',


));

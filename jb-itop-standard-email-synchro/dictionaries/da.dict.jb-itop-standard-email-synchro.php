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
	'Class:MailInboxStandard' => 'IMAP-mailboks',
	'Class:MailInboxStandard+' => 'Kilde til indgående e-mails',
	'Class:MailInboxStandard/Attribute:behavior' => 'Adfærd ved behandling af en e-mail',
	'Class:MailInboxStandard/Attribute:behavior/Value:create_only' => 'Opret nye sager',
	'Class:MailInboxStandard/Attribute:behavior/Value:update_only' => 'Opdater eksisterende sager',
	'Class:MailInboxStandard/Attribute:behavior/Value:both' => 'Opret eller opdater sager',

	'Class:MailInboxStandard/Attribute:email_storage' => 'Efter behandling af e-mailen',
	'Class:MailInboxStandard/Attribute:email_storage+' => 'Handling der skal udføres efter behandling af e-mailen. For bedst ydeevne: hvis arkivering ønskes, anbefales det at flytte succesfuldt behandlede e-mails til en anden mappe.',
	'Class:MailInboxStandard/Attribute:email_storage/Value:keep' => 'Behold den i samme mappe',
	'Class:MailInboxStandard/Attribute:email_storage/Value:delete' => 'Slet den med det samme',
	'Class:MailInboxStandard/Attribute:email_storage/Value:move' => 'Flyt til en anden mappe',

	'Class:MailInboxStandard/Attribute:target_class' => 'Sagstype',
	'Class:MailInboxStandard/Attribute:target_class/Value:Incident' => 'Hændelse',
	'Class:MailInboxStandard/Attribute:target_class/Value:UserRequest' => 'Brugeranmodning',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change' => 'Ændring',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange' => 'Rutineændring',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange' => 'Normal ændring',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange' => 'Nødændring',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem' => 'Problem',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem+' => '',
	'Class:MailInboxStandard/Attribute:debug_trace' => 'Fejlfindingsspor',
	'Class:MailInboxStandard/Attribute:debug_trace+' => '',
	'Class:MailInboxStandard/Attribute:target_folder' => 'Målmappe',
	'Class:MailInboxStandard/Attribute:target_folder+' => 'E-mailen flyttes (IMAP-protokol) til denne målmappe, efter den er behandlet. Husk at opdatere indstillingen "Efter behandling af e-mailen" til "Flyt til en anden mappe".',

	'Class:MailInboxStandard/Attribute:attcode_description' => 'Beskrivelsesattribut',
	'Class:MailInboxStandard/Attribute:attcode_description+' => 'Attributkoden for målklassen, der skal modtage sagens oprindelige beskrivelse. Standard: "description", hvis tomt.',
	'Class:MailInboxStandard/Attribute:attcode_caselog' => 'Sagsloggens attribut',
	'Class:MailInboxStandard/Attribute:attcode_caselog+' => 'Attributkoden for målklassen (sagslog), der skal modtage nye posteringer, når sagen oprettes og/eller opdateres. Standard: "public_log", hvis tomt eller ugyldigt.',

	'Class:MailInboxStandard/Attribute:ticket_default_values' => 'Standardværdier for ny sag',
	'Class:MailInboxStandard/Attribute:ticket_default_title' => 'Standardtitel (hvis emnet er tomt)',
	'Class:MailInboxStandard/Attribute:title_pattern+' => 'Mønster, der skal matches i emnet',
	'Class:MailInboxStandard/Attribute:title_pattern' => 'Titelmønster',
	'Class:MailInboxStandard/Attribute:title_pattern?' => 'Brug PCRE-syntaks, inklusive start- og slutafgrænsere, for at angive hvordan sagsreferencen (mønsteret) ser ud, så e-mails kan knyttes til sager.',

	'Class:MailInboxStandard/Attribute:title_pattern_ignore_patterns' => 'Ignorer mønstre i emnet (regex-mønstre, ét pr. linje)',

	'Class:MailInboxStandard/Attribute:stimuli' => 'Stimuli der skal anvendes',
	'Class:MailInboxStandard/Attribute:stimuli+' => 'Anvend en stimulus, når sagen er i en given tilstand',
	'Class:MailInboxStandard/Attribute:stimuli?' => 'En liste af tilstandskode:stimuluskode (én pr. linje), der definerer hvilken stimulus der skal anvendes (kun efter opdatering af en eksisterende sag), for den givne tilstand af sagen. Dette er f.eks. nyttigt til automatisk at gentildele en sag, der er i tilstanden "afventer". Brug formatet <tilstandskode>:<stimuluskode>',


	'Class:MailInboxStandard/Attribute:trace' => 'Fejlfindingsspor',
	'Class:MailInboxStandard/Attribute:trace/Value:yes' => 'Ja',
	'Class:MailInboxStandard/Attribute:trace/Value:no' => 'Nej',

	'Class:MailInboxStandard/Attribute:debug_log' => 'Fejlfindingslog',

	'Class:MailInboxStandard/Attribute:error_behavior' => 'Adfærd når der opstår en fejl under behandling',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:delete' => 'Slet beskeden fra mailboksen',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:mark_as_error' => 'Marker som fejl',
	'Class:MailInboxStandard/Attribute:notify_errors_to' => 'Kontakter der skal underrettes ved fejl',
	'Class:MailInboxStandard/Attribute:notify_errors_to+' => 'OQL-forespørgsel der returnerer den/de person(er) (fx "SELECT Person WHERE email = \'admin@example.com\'"), som fejlede e-mails skal videresendes til.',
	'Class:MailInboxStandard/Attribute:notify_from' => 'Afsenderadresse',

	'Class:MailInboxStandard/Attribute:mail_aliases' => 'E-mail-aliaser',
	'Class:MailInboxStandard/Attribute:mail_aliases+' => 'E-mail-aliaser: ét pr. linje. Regex-mønstre er tilladt.',

	'Class:MailInboxStandard/Attribute:authentication_results_authserv_id' => 'Betroet Authentication-Results-server-ID',
	'Class:MailInboxStandard/Attribute:authentication_results_authserv_id+' => 'Værtsnavn (authserv-id) for den mailserver, der reelt autentificerer indgående post for denne mailboks (fx "mx.google.com"). Hvis angivet, tager SPF/DKIM-kontroller (bruges til at stole på afsenderens adresse) kun hensyn til "Authentication-Results"-headeren tilføjet af denne server og ignorerer alle andre forekomster, som ellers kunne forfalskes af afsenderen. Lad stå tomt for at stole på den første forekomst (tidligere adfærd).',

	'Class:MailInboxStandard/Attribute:oauth_provider' => 'OAuth-udbyder',
	'Class:MailInboxStandard/Attribute:oauth_provider+' => '',
	'Class:MailInboxStandard/Attribute:oauth_client_id' => 'OAuth-klient',
	'Class:MailInboxStandard/Attribute:oauth_client_id+' => '',

	// Politik: Vedhæftningskriterier
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width' => 'Min. bredde (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width+' => 'Minimal billedbredde (px). Skal være mindst 1. For små billeder behandles ikke.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width' => 'Maks. bredde (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width+' => 'Maksimal billedbredde (px). Sæt til 0 for at acceptere enhver bredde. Hvis php-gd-udvidelsen er installeret, ændres størrelsen på større billeder. Ellers behandles de ikke.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height' => 'Min. højde (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height+' => 'Minimal højde (px). Skal være mindst 1.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height' => 'Maks. højde (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height+' => 'Maksimal højde (px). Sæt til 0 for at acceptere enhver højde. Hvis php-gd-udvidelsen er installeret, ændres størrelsen på større billeder. Ellers behandles de ikke.',

	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes' => 'Udeluk MIME-typer',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes+' => 'Vedhæftninger af disse MIME-typer behandles ikke. Angiv én pr. linje.',

	// Politik: DKIM-kontrol
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior' => 'Adfærd ved overtrædelse',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:bounce_delete' => 'Afvis til afsender og slet',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:bounce_mark_as_undesired' => 'Afvis til afsender og marker som uønsket',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:delete' => 'Slet beskeden fra mailboksen',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:do_nothing' => 'Gør intet',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:mark_as_undesired' => 'Marker som uønsket / Behold e-mailen midlertidigt',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_subject' => 'Emne for afvisning',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_notification' => 'Afvisningsbesked',

	// Politik: for stor e-mail
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior' => 'Adfærd ved overtrædelse',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_delete' => 'Afvis til afsender og slet',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_mark_as_undesired' => 'Afvis til afsender og marker som uønsket',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:delete' => 'Slet beskeden fra mailboksen',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:do_nothing' => 'Gør intet',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:mark_as_undesired' => 'Marker som uønsket / Behold e-mailen midlertidigt',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_subject' => 'Emne for afvisning',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_notification' => 'Afvisningsbesked',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB' => 'Maks. størrelse (MB)',
	'Error:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB+' => 'Maksimal størrelse på e-mailen og dens vedhæftninger. Større e-mails behandles ikke. Sæt til 0 for at deaktivere.',

	// Politik: vedhæftning - forbudt MIME-type
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior' => 'Adfærd ved overtrædelse',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:fallback_ignore_forbidden_attachments' => 'Reserveløsning: ignorer forbudte vedhæftninger',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_delete' => 'Afvis til afsender og slet',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_mark_as_undesired' => 'Afvis til afsender og marker som uønsket',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:delete' => 'Slet beskeden fra mailboksen',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:do_nothing' => 'Gør intet',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:mark_as_undesired' => 'Marker som uønsket / Behold e-mailen midlertidigt',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_subject' => 'Emne for afvisning',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_notification' => 'Afvisningsbesked',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_mimetypes' => 'MIME-typer (én pr. linje)',

	// Politik: intet emne
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior' => 'Adfærd ved overtrædelse',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:fallback_default_subject' => 'Reserveløsning: brug standardemne',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_delete' => 'Afvis til afsender og slet',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_mark_as_undesired' => 'Afvis til afsender og marker som uønsket',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:delete' => 'Slet beskeden fra mailboksen',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:do_nothing' => 'Gør intet',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:mark_as_undesired' => 'Marker som uønsket / Behold e-mailen midlertidigt',
	'Class:MailInboxStandard/Attribute:policy_no_subject_subject' => 'Emne for afvisning',
	'Class:MailInboxStandard/Attribute:policy_no_subject_notification' => 'Afvisningsbesked',
	'Class:MailInboxStandard/Attribute:policy_no_subject_default_value' => 'Standardemne',

	// Politik: ukendt afsender
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior' => 'Adfærd ved overtrædelse',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:fallback_create_person' => 'Reserveløsning: opret person',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_delete' => 'Afvis til afsender og slet',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_mark_as_undesired' => 'Afvis til afsender og marker som uønsket',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:delete' => 'Slet beskeden fra mailboksen',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:mark_as_undesired' => 'Marker som uønsket / Behold e-mailen midlertidigt',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_subject' => 'Emne for afvisning',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_notification' => 'Afvisningsbesked',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_default_values' => 'Standardværdier for ny person (én pr. linje, eksempel: org_id:1)',

	// Politik: andre modtagere
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior' => 'Adfærd ved overtrædelse',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_existing_other_contacts' => 'Reserveløsning: knyt kun eksisterende kontakter',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_other_contacts' => 'Reserveløsning: knyt altid kontakt, opret kontakt om nødvendigt',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_ignore_other_contacts' => 'Reserveløsning: ignorer alle andre kontakter',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_delete' => 'Afvis til afsender og slet',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_mark_as_undesired' => 'Afvis til afsender og marker som uønsket',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:delete' => 'Slet beskeden fra mailboksen',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:do_nothing' => 'Gør intet',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:mark_as_undesired' => 'Marker som uønsket / Behold e-mailen midlertidigt',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_subject' => 'Emne for afvisning',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_notification' => 'Afvisningsbesked',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_default_values' => 'Standardværdier for ny person (én pr. linje, eksempel: org_id:1)',

	// Politik: lukket sag
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior' => 'Adfærd ved overtrædelse',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior+' => 'Bemærk: som standard kan lukkede sager ikke genåbnes. Dette kræver ændringer i datamodellen.',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:fallback_reopen' => 'Reserveløsning: genåbn sag',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_delete' => 'Afvis til afsender og slet',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_mark_as_undesired' => 'Afvis til afsender og marker som uønsket',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:delete' => 'Slet beskeden fra mailboksen',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:do_nothing' => 'Gør intet',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:mark_as_undesired' => 'Marker som uønsket / Behold e-mailen midlertidigt',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_subject' => 'Emne for afvisning',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_notification' => 'Afvisningsbesked',

	// Politik: løst sag
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior' => 'Adfærd ved overtrædelse',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:fallback_reopen' => 'Reserveløsning: genåbn sag',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_delete' => 'Afvis til afsender og slet',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_mark_as_undesired' => 'Afvis til afsender og marker som uønsket',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:delete' => 'Slet beskeden fra mailboksen',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:do_nothing' => 'Gør intet',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:mark_as_undesired' => 'Marker som uønsket / Behold e-mailen midlertidigt',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_subject' => 'Emne for afvisning',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_notification' => 'Afvisningsbesked',

	// Politik: ukendt sag
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior' => 'Adfærd ved overtrædelse',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_delete' => 'Afvis til afsender og slet',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_mark_as_undesired' => 'Afvis til afsender og marker som uønsket',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:delete' => 'Slet beskeden fra mailboksen',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:do_nothing' => 'Gør intet',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:mark_as_undesired' => 'Marker som uønsket / Behold e-mailen midlertidigt',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_subject' => 'Emne for afvisning',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_notification' => 'Afvisningsbesked',

	// Politik: uønskede titelmønstre
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior' => 'Adfærd ved overtrædelse',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:delete' => 'Slet beskeden fra mailboksen',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_delete' => 'Afvis til afsender og slet',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_mark_as_undesired' => 'Afvis til afsender og marker som uønsket',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:do_nothing' => 'Gør intet',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:mark_as_undesired' => 'Marker som uønsket / Behold e-mailen midlertidigt',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_subject' => 'Emne for afvisning',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_notification' => 'Afvisningsbesked',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_patterns' => 'Uønskede mønstre i emnet (regex-mønstre, ét pr. linje)',


	// Politik: fjern dele af titlen
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior' => 'Fjern mønstre fra emnet',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:do_nothing' => 'Gør intet',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:fallback_remove' => 'Fjern del(e) af emnet',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_patterns' => 'Mønstre der skal fjernes fra emnet (regex-mønstre, ét pr. linje)',

	// Politik: afsender skal være den samme som sagens oprindelige afsender
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior' => 'Adfærd ved overtrædelse',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:delete' => 'Slet beskeden fra mailboksen',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_delete' => 'Afvis til afsender og slet',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_mark_as_undesired' => 'Afvis til afsender og marker som uønsket',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:do_nothing' => 'Gør intet',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:mark_as_undesired' => 'Marker som uønsket / Behold e-mailen midlertidigt',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_subject' => 'Emne for afvisning',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_notification' => 'Afvisningsbesked',

	// Politik: autosvar
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior' => 'Adfærd ved overtrædelse',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:delete' => 'Slet beskeden fra mailboksen',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:do_nothing' => 'Gør intet',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:mark_as_undesired' => 'Marker som uønsket / Behold e-mailen midlertidigt',

	// Politik: kvitteringsrapport for manglende levering
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior' => 'Adfærd ved overtrædelse',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:delete' => 'Slet beskeden fra mailboksen',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:do_nothing' => 'Gør intet',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:mark_as_undesired' => 'Marker som uønsket',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive' => 'Marker afsender som inaktiv',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive+' => 'Afsenderen markeres som inaktiv, hvis leveringsfejlen ser ud til at være permanent, og der er høj tillid til, at modtageren ikke længere kan nås via denne e-mailadresse.',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:yes' => 'Ja',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:no' => 'Nej',

	// Trin: opdater afsenderens attributter
	'Class:MailInboxStandard/Attribute:step_update_caller_attributes' => 'Opdater afsenderens attributter (én pr. linje, eksempel: status:active)',
	'Class:MailInboxStandard/Attribute:step_update_caller_attributes+' => 'Anvendes på afsenderens person-post, hver gang en succesfuldt behandlet e-mail matches til en eksisterende kontakt. Lad stå tomt for ikke at opdatere noget. Mail-pladsholdere (fx $mail->caller_email$) kan bruges i værdierne.',

	// Politik: afsenderens e-mailadresse
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior' => 'Adfærd ved overtrædelse',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_delete' => 'Afvis til afsender og slet',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_mark_as_undesired' => 'Afvis til afsender og marker som uønsket',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:delete' => 'Slet beskeden fra mailboksen',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:do_nothing' => 'Gør intet',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:mark_as_undesired' => 'Marker som uønsket',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_subject' => 'Emne for afvisning',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_notification' => 'Afvisningsbesked',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns' => 'Mønstre',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns+' => 'Enhver e-mail, hvor afsenderens e-mailadresse matcher et af de definerede regex-mønstre (ét pr. linje), betragtes som en overtrædelse.',



	// Overskrifter
	'MailInbox:Server' => 'Mailbokskonfiguration',
	'MailInbox:Behavior' => 'Adfærd for indgående e-mails',
	'MailInbox:Errors' => 'E-mails med fejl',
	'MailInbox:Settings' => 'Indstillinger',

	// Valideringsbeskeder
	'MailInbox:Error:TargetFolderRequired' => 'Målmappen skal angives for en aktiv mailboks.',
	'MailInbox:Error:CaseLogAttCodeRequired' => 'Sagsloggens attributkode skal være en gyldig attribut for målklassen \'%1$s\'.',
	'MailInbox:Error:DescriptionOrCaseLogAttCodeRequired' => 'Enten beskrivelsesattributkoden eller sagsloggens attributkode skal være en gyldig attribut for målklassen \'%1$s\'.',
	'MailInbox:Error:DescriptionAttCodeMustHaveMaxSize' => 'Beskrivelsesattributten \'%1$s\' for målklassen \'%2$s\' har ikke en maksimal størrelse og kan ikke bruges til at gemme sagens oprindelige beskrivelse.',

	// Trin
	'MailInbox:StepAttachmentCriteria' => 'Indlejrede billeder i e-mail',
	'MailInbox:PolicyDkimCheck' => 'DKIM-kontrol',
	'MailInbox:PolicyMailSize' => 'E-mailstørrelse',
	'MailInbox:PolicyAttachmentForbiddenMimeType' => 'Vedhæftning - Forbudt MIME-type',
	'MailInbox:PolicyAttachmentIgnoredMimeType' => 'Vedhæftning - Ignoreret MIME-type',
	'MailInbox:PolicyUndesiredPattern' => 'Uønskede titelmønstre',
	'MailInbox:PolicyRemovePattern' => 'Fjern uønskede titelmønstre',
	'MailInbox:PolicyIgnorePattern' => 'Ignorer uønskede titelmønstre',
	'MailInbox:PolicyResolvedTicket' => 'Løste sager',
	'MailInbox:PolicyClosedTicket' => 'Lukkede sager',
	'MailInbox:PolicyUnknownTicket' => 'Ukendte sager',
	'MailInbox:PolicyNoSubject' => 'Intet emne',
	'MailInbox:PolicyUnknownCaller' => 'Ukendt afsender',
	'MailInbox:PolicyOtherRecipients' => 'Andre modtagere angivet i Til: eller CC:',
	'MailInbox:PolicyBounceOtherEmailCallerThanTicketCaller' => 'Begræns accepterede e-mailsvar til den oprindelige afsenders e-mailadresse',
	'MailInbox:PolicyAutoReply' => 'Autosvar',
	'MailInbox:PolicyNonDeliveryReport' => 'Kvitteringsrapporter for manglende levering',
	'MailInbox:StepUpdateCallerAttributes' => 'Opdater afsenderens attributter',
	'MailInbox:PolicySenderEmailAddress' => 'Bloker afsendere ved hjælp af mønstre for e-mailadresser',

	// Valideringsbeskeder
	'MailInbox:Error:NotifyErrorsToMustTargetContact' => 'Forespørgslen til at vælge %1$s skal målrette klassen \'Contact\' (eller en underklasse som \'Person\' eller \'Team\'), ikke \'%2$s\'.',

	'Menu:MailInboxes' => 'Mailbokse for indgående e-mail',
	'Menu:MailInboxes+' => 'Konfiguration af mailbokse, der skal gennemsøges for indgående e-mails',

	'MailInboxStandard:DebugTrace' => 'Fejlfindingsspor',
	'MailInboxStandard:DebugTraceNotActive' => 'Aktiver fejlfindingssporet på denne mailboks for at se en detaljeret log over, hvad der sker.',
	'MailInboxStandard:DebugTraceAccessDenied' => 'Du har ikke tilladelse til at se fejlfindingssporet for denne mailboks.',

	'MailPolicy:CreateOrUpdateTicket:NoDescriptionProvided' => 'Ingen beskrivelse angivet',

	// OAuth2
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Title' => 'Opret en mailboks',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Description' => 'Opret en ny mailboks til at hente e-mails fra en ekstern mailudbyder ved hjælp af denne OAuth-forbindelse som autentificeringsmetode',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:CreateNewMailbox' => 'Opret en ny mailboks',
	'UI:OAuthEmailSynchro:Error:UnknownVendor' => 'OAuth-udbyderen %1$s findes ikke',

	// lnkEmailUidToTicket
	'Class:lnkEmailUidToTicket' => 'Kobling af e-mail-UID / sag',
	'Class:lnkEmailUidToTicket/Attribute:message_uid' => 'Beskedens UID',
	'Class:lnkEmailUidToTicket/Attribute:ticket_id' => 'Sags-ID',
	'Class:lnkEmailUidToTicket/Attribute:mailbox_id' => 'Mailboks-ID',
	'Class:lnkEmailUidToTicket/UniquenessRule:unique_message_uid_and_mailbox_id_and_ticket_id' => 'Kombinationen af mailboks-ID, besked-UID og sags-ID skal være unik.',


));

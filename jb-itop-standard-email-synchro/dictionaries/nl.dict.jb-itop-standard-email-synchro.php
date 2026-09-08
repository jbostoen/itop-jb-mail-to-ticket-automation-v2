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
	'Class:MailInboxStandard' => 'IMAP-mailbox',
	'Class:MailInboxStandard+' => 'Bron van inkomende e-mails',
	'Class:MailInboxStandard/Attribute:behavior' => 'Gedrag bij het verwerken van een e-mail',
	'Class:MailInboxStandard/Attribute:behavior/Value:create_only' => 'Nieuwe tickets aanmaken',
	'Class:MailInboxStandard/Attribute:behavior/Value:update_only' => 'Bestaande tickets bijwerken',
	'Class:MailInboxStandard/Attribute:behavior/Value:both' => 'Tickets aanmaken of bijwerken',

	'Class:MailInboxStandard/Attribute:email_storage' => 'Na verwerking van de e-mail',
	'Class:MailInboxStandard/Attribute:email_storage+' => 'Actie die wordt uitgevoerd na verwerking van de e-mail. Voor de beste prestaties: als archivering gewenst is, wordt aanbevolen succesvol verwerkte e-mails naar een andere map te verplaatsen.',
	'Class:MailInboxStandard/Attribute:email_storage/Value:keep' => 'Behouden in dezelfde map',
	'Class:MailInboxStandard/Attribute:email_storage/Value:delete' => 'Onmiddellijk verwijderen',
	'Class:MailInboxStandard/Attribute:email_storage/Value:move' => 'Verplaatsen naar een andere map',

	'Class:MailInboxStandard/Attribute:target_class' => 'Ticketklasse',
	'Class:MailInboxStandard/Attribute:target_class/Value:Incident' => 'Incident',
	'Class:MailInboxStandard/Attribute:target_class/Value:UserRequest' => 'Gebruikersverzoek',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change' => 'Wijziging',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange' => 'Routinewijziging',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange' => 'Normale wijziging',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange' => 'Noodwijziging',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem' => 'Probleem',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem+' => '',
	'Class:MailInboxStandard/Attribute:debug_trace' => 'Foutopsporingslog',
	'Class:MailInboxStandard/Attribute:debug_trace+' => '',
	'Class:MailInboxStandard/Attribute:target_folder' => 'Doelmap',
	'Class:MailInboxStandard/Attribute:target_folder+' => 'De e-mail wordt (via het IMAP-protocol) na verwerking naar deze doelmap verplaatst. Vergeet niet de instelling "Na verwerking van de e-mail" bij te werken naar "Verplaatsen naar een andere map".',

	'Class:MailInboxStandard/Attribute:attcode_description' => 'Omschrijvingsattribuut',
	'Class:MailInboxStandard/Attribute:attcode_description+' => 'Attribuutcode van de doelklasse die de initiële ticketomschrijving moet ontvangen. Standaard: "description" indien leeg.',
	'Class:MailInboxStandard/Attribute:attcode_caselog' => 'Attribuut van het logboek (case log)',
	'Class:MailInboxStandard/Attribute:attcode_caselog+' => 'Attribuutcode van de doelklasse (logboek) die nieuwe vermeldingen moet ontvangen bij het aanmaken en/of bijwerken van het ticket. Standaard: "public_log" indien leeg of ongeldig.',

	'Class:MailInboxStandard/Attribute:ticket_default_values' => 'Standaardwaarden voor nieuw ticket',
	'Class:MailInboxStandard/Attribute:ticket_default_title' => 'Standaardtitel (indien onderwerp leeg is)',
	'Class:MailInboxStandard/Attribute:title_pattern+' => 'Patroon om te zoeken in het onderwerp',
	'Class:MailInboxStandard/Attribute:title_pattern' => 'Titelpatroon',
	'Class:MailInboxStandard/Attribute:title_pattern?' => 'Gebruik PCRE-syntaxis, inclusief begin- en eindafbakeningen, om aan te geven hoe de ticketreferentie (patroon) eruitziet, zodat e-mails aan tickets kunnen worden gekoppeld.',

	'Class:MailInboxStandard/Attribute:title_pattern_ignore_patterns' => 'Patronen negeren in het onderwerp (reguliere expressies, één per regel)',

	'Class:MailInboxStandard/Attribute:stimuli' => 'Toe te passen stimuli',
	'Class:MailInboxStandard/Attribute:stimuli+' => 'Pas een stimulus toe wanneer het ticket in een bepaalde status verkeert',
	'Class:MailInboxStandard/Attribute:stimuli?' => 'Een lijst van statuscode:stimuluscode (één per regel) om de toe te passen stimulus te bepalen (alleen na het bijwerken van een bestaand ticket), voor de gegeven status van het ticket. Dit is bijvoorbeeld nuttig om een ticket in de status "in afwachting" automatisch opnieuw toe te wijzen. Gebruik het formaat <statuscode>:<stimuluscode>',


	'Class:MailInboxStandard/Attribute:trace' => 'Foutopsporingslog',
	'Class:MailInboxStandard/Attribute:trace/Value:yes' => 'Ja',
	'Class:MailInboxStandard/Attribute:trace/Value:no' => 'Nee',

	'Class:MailInboxStandard/Attribute:debug_log' => 'Foutopsporingslogboek',

	'Class:MailInboxStandard/Attribute:error_behavior' => 'Gedrag wanneer een fout optreedt tijdens verwerking',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:delete' => 'Het bericht uit de mailbox verwijderen',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:mark_as_error' => 'Markeren als fout',
	'Class:MailInboxStandard/Attribute:notify_errors_to' => 'Contacten die bij een fout op de hoogte moeten worden gebracht',
	'Class:MailInboxStandard/Attribute:notify_errors_to+' => 'OQL-query die de Persoon/Personen retourneert (bijv. "SELECT Person WHERE email = \'admin@example.com\'") naar wie e-mails met fouten worden doorgestuurd.',
	'Class:MailInboxStandard/Attribute:notify_from' => 'Afzenderadres',

	'Class:MailInboxStandard/Attribute:mail_aliases' => 'E-mailaliassen',
	'Class:MailInboxStandard/Attribute:mail_aliases+' => 'E-mailaliassen: één per regel. Reguliere expressies zijn toegestaan.',

	'Class:MailInboxStandard/Attribute:authentication_results_authserv_id' => 'Vertrouwd Authentication-Results-server-ID',
	'Class:MailInboxStandard/Attribute:authentication_results_authserv_id+' => 'Hostnaam (authserv-id) van de mailserver die inkomende post voor deze mailbox daadwerkelijk authenticeert (bijv. "mx.google.com"). Indien ingesteld, houden SPF/DKIM-controles (gebruikt om het afzenderadres te vertrouwen) alleen rekening met de door deze server toegevoegde "Authentication-Results"-header, en negeren ze elke andere voorkomst, die anders door de afzender vervalst zou kunnen worden. Laat leeg om de eerst aangetroffen voorkomst te vertrouwen (voorheen gedrag).',

	'Class:MailInboxStandard/Attribute:oauth_provider' => 'OAuth-provider',
	'Class:MailInboxStandard/Attribute:oauth_provider+' => '',
	'Class:MailInboxStandard/Attribute:oauth_client_id' => 'OAuth-client',
	'Class:MailInboxStandard/Attribute:oauth_client_id+' => '',

	// Beleid: Bijlagecriteria
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width' => 'Min. breedte (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width+' => 'Minimale afbeeldingsbreedte (px). Moet minstens 1 zijn. Te kleine afbeeldingen worden niet verwerkt.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width' => 'Max. breedte (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width+' => 'Maximale afbeeldingsbreedte (px). Zet op 0 om elke breedte te accepteren. Als de php-gd-extensie is geïnstalleerd, worden grotere afbeeldingen verkleind. Anders worden ze niet verwerkt.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height' => 'Min. hoogte (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height+' => 'Minimale hoogte (px). Moet minstens 1 zijn.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height' => 'Max. hoogte (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height+' => 'Maximale hoogte (px). Zet op 0 om elke hoogte te accepteren. Als de php-gd-extensie is geïnstalleerd, worden grotere afbeeldingen verkleind. Anders worden ze niet verwerkt.',

	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes' => 'MIME-types uitsluiten',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes+' => 'Bijlagen van deze MIME-types worden niet verwerkt. Geef er één per regel op.',

	// Beleid: DKIM-controle
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior' => 'Gedrag bij overtreding',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:bounce_delete' => 'Terugsturen naar afzender en verwijderen',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:bounce_mark_as_undesired' => 'Terugsturen naar afzender en markeren als ongewenst',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:delete' => 'Het bericht uit de mailbox verwijderen',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:do_nothing' => 'Niets doen',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:inactive' => 'Inactief',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:mark_as_undesired' => 'Markeren als ongewenst / E-mail tijdelijk behouden',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_subject' => 'Onderwerp van terugsturen',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_notification' => 'Bericht bij terugsturen',

	// Beleid: e-mail te groot
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior' => 'Gedrag bij overtreding',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_delete' => 'Terugsturen naar afzender en verwijderen',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_mark_as_undesired' => 'Terugsturen naar afzender en markeren als ongewenst',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:delete' => 'Het bericht uit de mailbox verwijderen',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:do_nothing' => 'Niets doen',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:inactive' => 'Inactief',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:mark_as_undesired' => 'Markeren als ongewenst / E-mail tijdelijk behouden',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_subject' => 'Onderwerp van terugsturen',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_notification' => 'Bericht bij terugsturen',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB' => 'Max. grootte (MB)',
	'Error:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB+' => 'Maximale grootte van de e-mail en de bijlagen ervan. Grotere e-mails worden niet verwerkt. Zet op 0 om uit te schakelen.',

	// Beleid: bijlage - verboden MIME-type
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior' => 'Gedrag bij overtreding',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:fallback_ignore_forbidden_attachments' => 'Terugvaloptie: verboden bijlagen negeren',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_delete' => 'Terugsturen naar afzender en verwijderen',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_mark_as_undesired' => 'Terugsturen naar afzender en markeren als ongewenst',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:delete' => 'Het bericht uit de mailbox verwijderen',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:do_nothing' => 'Niets doen',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:inactive' => 'Inactief',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:mark_as_undesired' => 'Markeren als ongewenst / E-mail tijdelijk behouden',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_subject' => 'Onderwerp van terugsturen',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_notification' => 'Bericht bij terugsturen',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_mimetypes' => 'MIME-types (één per regel)',

	// Beleid: geen onderwerp
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior' => 'Gedrag bij overtreding',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:fallback_default_subject' => 'Terugvaloptie: standaardonderwerp gebruiken',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_delete' => 'Terugsturen naar afzender en verwijderen',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_mark_as_undesired' => 'Terugsturen naar afzender en markeren als ongewenst',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:delete' => 'Het bericht uit de mailbox verwijderen',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:do_nothing' => 'Niets doen',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:inactive' => 'Inactief',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:mark_as_undesired' => 'Markeren als ongewenst / E-mail tijdelijk behouden',
	'Class:MailInboxStandard/Attribute:policy_no_subject_subject' => 'Onderwerp van terugsturen',
	'Class:MailInboxStandard/Attribute:policy_no_subject_notification' => 'Bericht bij terugsturen',
	'Class:MailInboxStandard/Attribute:policy_no_subject_default_value' => 'Standaardonderwerp',

	// Beleid: onbekende afzender
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior' => 'Gedrag bij overtreding',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:fallback_create_person' => 'Terugvaloptie: persoon aanmaken',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_delete' => 'Terugsturen naar afzender en verwijderen',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_mark_as_undesired' => 'Terugsturen naar afzender en markeren als ongewenst',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:delete' => 'Het bericht uit de mailbox verwijderen',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:mark_as_undesired' => 'Markeren als ongewenst / E-mail tijdelijk behouden',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_subject' => 'Onderwerp van terugsturen',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_notification' => 'Bericht bij terugsturen',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_default_values' => 'Standaardwaarden voor nieuwe persoon (één per regel, voorbeeld: org_id:1)',

	// Beleid: andere ontvangers
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior' => 'Gedrag bij overtreding',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_existing_other_contacts' => 'Terugvaloptie: alleen bestaande contacten koppelen',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_other_contacts' => 'Terugvaloptie: contact altijd koppelen, indien nodig aanmaken',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_ignore_other_contacts' => 'Terugvaloptie: alle andere contacten negeren',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_delete' => 'Terugsturen naar afzender en verwijderen',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_mark_as_undesired' => 'Terugsturen naar afzender en markeren als ongewenst',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:delete' => 'Het bericht uit de mailbox verwijderen',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:do_nothing' => 'Niets doen',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:inactive' => 'Inactief',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:mark_as_undesired' => 'Markeren als ongewenst / E-mail tijdelijk behouden',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_subject' => 'Onderwerp van terugsturen',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_notification' => 'Bericht bij terugsturen',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_default_values' => 'Standaardwaarden voor nieuwe persoon (één per regel, voorbeeld: org_id:1)',

	// Beleid: gesloten ticket
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior' => 'Gedrag bij overtreding',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior+' => 'Let op: standaard kunnen gesloten tickets niet worden heropend. Dit vereist wijzigingen in het datamodel.',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:fallback_reopen' => 'Terugvaloptie: ticket heropenen',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_delete' => 'Terugsturen naar afzender en verwijderen',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_mark_as_undesired' => 'Terugsturen naar afzender en markeren als ongewenst',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:delete' => 'Het bericht uit de mailbox verwijderen',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:do_nothing' => 'Niets doen',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:inactive' => 'Inactief',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:mark_as_undesired' => 'Markeren als ongewenst / E-mail tijdelijk behouden',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_subject' => 'Onderwerp van terugsturen',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_notification' => 'Bericht bij terugsturen',

	// Beleid: opgelost ticket
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior' => 'Gedrag bij overtreding',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:fallback_reopen' => 'Terugvaloptie: ticket heropenen',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_delete' => 'Terugsturen naar afzender en verwijderen',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_mark_as_undesired' => 'Terugsturen naar afzender en markeren als ongewenst',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:delete' => 'Het bericht uit de mailbox verwijderen',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:do_nothing' => 'Niets doen',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:inactive' => 'Inactief',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:mark_as_undesired' => 'Markeren als ongewenst / E-mail tijdelijk behouden',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_subject' => 'Onderwerp van terugsturen',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_notification' => 'Bericht bij terugsturen',

	// Beleid: onbekend ticket
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior' => 'Gedrag bij overtreding',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_delete' => 'Terugsturen naar afzender en verwijderen',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_mark_as_undesired' => 'Terugsturen naar afzender en markeren als ongewenst',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:delete' => 'Het bericht uit de mailbox verwijderen',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:do_nothing' => 'Niets doen',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:inactive' => 'Inactief',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:mark_as_undesired' => 'Markeren als ongewenst / E-mail tijdelijk behouden',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_subject' => 'Onderwerp van terugsturen',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_notification' => 'Bericht bij terugsturen',

	// Beleid: ongewenste titelpatronen
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior' => 'Gedrag bij overtreding',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:delete' => 'Het bericht uit de mailbox verwijderen',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_delete' => 'Terugsturen naar afzender en verwijderen',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_mark_as_undesired' => 'Terugsturen naar afzender en markeren als ongewenst',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:do_nothing' => 'Niets doen',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:inactive' => 'Inactief',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:mark_as_undesired' => 'Markeren als ongewenst / E-mail tijdelijk behouden',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_subject' => 'Onderwerp van terugsturen',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_notification' => 'Bericht bij terugsturen',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_patterns' => 'Ongewenste patronen in het onderwerp (reguliere expressies, één per regel)',


	// Beleid: delen van de titel verwijderen
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior' => 'Patronen uit het onderwerp verwijderen',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:do_nothing' => 'Niets doen',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:inactive' => 'Inactief',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:fallback_remove' => 'Deel/delen van het onderwerp verwijderen',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_patterns' => 'Patronen die uit het onderwerp moeten worden verwijderd (reguliere expressies, één per regel)',

	// Beleid: afzender moet dezelfde zijn als de oorspronkelijke afzender van het ticket
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior' => 'Gedrag bij overtreding',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:delete' => 'Het bericht uit de mailbox verwijderen',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_delete' => 'Terugsturen naar afzender en verwijderen',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_mark_as_undesired' => 'Terugsturen naar afzender en markeren als ongewenst',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:do_nothing' => 'Niets doen',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:inactive' => 'Inactief',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:mark_as_undesired' => 'Markeren als ongewenst / E-mail tijdelijk behouden',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_subject' => 'Onderwerp van terugsturen',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_notification' => 'Bericht bij terugsturen',

	// Beleid: automatisch antwoord
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior' => 'Gedrag bij overtreding',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:delete' => 'Het bericht uit de mailbox verwijderen',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:do_nothing' => 'Niets doen',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:inactive' => 'Inactief',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:mark_as_undesired' => 'Markeren als ongewenst / E-mail tijdelijk behouden',

	// Beleid: kennisgeving van niet-bezorging
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior' => 'Gedrag bij overtreding',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:delete' => 'Het bericht uit de mailbox verwijderen',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:do_nothing' => 'Niets doen',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:inactive' => 'Inactief',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:mark_as_undesired' => 'Markeren als ongewenst',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive' => 'Afzender markeren als inactief',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive+' => 'De afzender wordt als inactief gemarkeerd als de bezorgingsfout permanent lijkt en er een grote zekerheid is dat de ontvanger niet langer via dit e-mailadres bereikbaar is.',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:yes' => 'Ja',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:no' => 'Nee',

	// Stap: attributen van de afzender bijwerken
	'Class:MailInboxStandard/Attribute:step_update_caller_attributes' => 'Attributen van de afzender bijwerken (één per regel, voorbeeld: status:active)',
	'Class:MailInboxStandard/Attribute:step_update_caller_attributes+' => 'Wordt toegepast op de persoonsrecord van de afzender telkens wanneer een succesvol verwerkte e-mail wordt gekoppeld aan een bestaand contact. Laat leeg om niets bij te werken. Mail-plaatshouders (bijv. $mail->caller_email$) kunnen in de waarden worden gebruikt.',

	// Beleid: e-mailadres van de afzender
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior' => 'Gedrag bij overtreding',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_delete' => 'Terugsturen naar afzender en verwijderen',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_mark_as_undesired' => 'Terugsturen naar afzender en markeren als ongewenst',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:delete' => 'Het bericht uit de mailbox verwijderen',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:do_nothing' => 'Niets doen',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:inactive' => 'Inactief',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:mark_as_undesired' => 'Markeren als ongewenst',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_subject' => 'Onderwerp van terugsturen',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_notification' => 'Bericht bij terugsturen',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns' => 'Patronen',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns+' => 'Elke e-mail waarvan het e-mailadres van de afzender overeenkomt met een van de gedefinieerde reguliere expressies (één per regel), wordt beschouwd als een overtreding.',



	// Kopteksten
	'MailInbox:Server' => 'Mailboxconfiguratie',
	'MailInbox:Behavior' => 'Gedrag bij inkomende e-mails',
	'MailInbox:Errors' => 'E-mails met fouten',
	'MailInbox:Settings' => 'Instellingen',

	// Validatieberichten
	'MailInbox:Error:TargetFolderRequired' => 'De doelmap moet worden opgegeven voor een actieve mailbox.',
	'MailInbox:Error:CaseLogAttCodeRequired' => 'De attribuutcode van het logboek moet een geldig attribuut zijn van de doelklasse \'%1$s\'.',
	'MailInbox:Error:DescriptionOrCaseLogAttCodeRequired' => 'Ofwel de attribuutcode van de omschrijving, ofwel die van het logboek moet een geldig attribuut zijn van de doelklasse \'%1$s\'.',
	'MailInbox:Error:DescriptionAttCodeMustHaveMaxSize' => 'Het omschrijvingsattribuut \'%1$s\' van de doelklasse \'%2$s\' heeft geen maximale grootte en kan niet worden gebruikt om de initiële ticketomschrijving op te slaan.',

	// Stappen
	'MailInbox:StepAttachmentCriteria' => 'Ingesloten e-mailafbeeldingen',
	'MailInbox:PolicyDkimCheck' => 'DKIM-controle',
	'MailInbox:PolicyMailSize' => 'E-mailgrootte',
	'MailInbox:PolicyAttachmentForbiddenMimeType' => 'Bijlage - Verboden MIME-type',
	'MailInbox:PolicyAttachmentIgnoredMimeType' => 'Bijlage - Genegeerd MIME-type',
	'MailInbox:PolicyUndesiredPattern' => 'Ongewenste titelpatronen',
	'MailInbox:PolicyRemovePattern' => 'Ongewenste titelpatronen verwijderen',
	'MailInbox:PolicyIgnorePattern' => 'Ongewenste titelpatronen negeren',
	'MailInbox:PolicyResolvedTicket' => 'Opgeloste tickets',
	'MailInbox:PolicyClosedTicket' => 'Gesloten tickets',
	'MailInbox:PolicyUnknownTicket' => 'Onbekende tickets',
	'MailInbox:PolicyNoSubject' => 'Geen onderwerp',
	'MailInbox:PolicyUnknownCaller' => 'Onbekende afzender',
	'MailInbox:PolicyOtherRecipients' => 'Andere ontvangers opgegeven in Aan: of CC:',
	'MailInbox:PolicyBounceOtherEmailCallerThanTicketCaller' => 'Geaccepteerde e-mailantwoorden beperken tot het e-mailadres van de oorspronkelijke afzender van het ticket',
	'MailInbox:PolicyAutoReply' => 'Automatisch antwoord',
	'MailInbox:PolicyNonDeliveryReport' => 'Kennisgevingen van niet-bezorging',
	'MailInbox:StepUpdateCallerAttributes' => 'Attributen van de afzender bijwerken',
	'MailInbox:PolicySenderEmailAddress' => 'Afzenders blokkeren met behulp van patronen voor e-mailadressen',

	// Validatieberichten
	'MailInbox:Error:NotifyErrorsToMustTargetContact' => 'De query om %1$s te selecteren moet gericht zijn op de klasse \'Contact\' (of een subklasse zoals \'Person\' of \'Team\'), niet op \'%2$s\'.',

	'Menu:MailInboxes' => 'Mailboxen voor inkomende e-mail',
	'Menu:MailInboxes+' => 'Configuratie van de mailboxen die worden doorzocht op inkomende e-mails',

	'MailInboxStandard:DebugTrace' => 'Foutopsporingslog',
	'MailInboxStandard:DebugTraceNotActive' => 'Activeer het foutopsporingslog op deze mailbox om een gedetailleerd logboek te zien van wat er gebeurt.',
	'MailInboxStandard:DebugTraceAccessDenied' => 'U heeft geen toestemming om het foutopsporingslog van deze mailbox te bekijken.',

	'MailPolicy:CreateOrUpdateTicket:NoDescriptionProvided' => 'Geen omschrijving opgegeven',

	// OAuth2
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Title' => 'Een mailbox aanmaken',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Description' => 'Een nieuwe mailbox aanmaken om e-mails op te halen van een externe mailprovider met deze OAuth-verbinding als authenticatiemethode',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:CreateNewMailbox' => 'Een nieuwe mailbox aanmaken',
	'UI:OAuthEmailSynchro:Error:UnknownVendor' => 'OAuth-provider %1$s bestaat niet',

	// lnkEmailUidToTicket
	'Class:lnkEmailUidToTicket' => 'Koppeling e-mail-UID / ticket',
	'Class:lnkEmailUidToTicket/Attribute:message_uid' => 'UID van het bericht',
	'Class:lnkEmailUidToTicket/Attribute:ticket_id' => 'Ticket-ID',
	'Class:lnkEmailUidToTicket/Attribute:mailbox_id' => 'Mailbox-ID',
	'Class:lnkEmailUidToTicket/UniquenessRule:unique_message_uid_and_mailbox_id_and_ticket_id' => 'De combinatie van mailbox-ID, bericht-UID en ticket-ID moet uniek zijn.',


));

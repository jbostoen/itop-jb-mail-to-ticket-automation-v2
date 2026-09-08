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
	'Class:MailInboxStandard' => 'IMAP-Postfach',
	'Class:MailInboxStandard+' => 'Quelle für eingehende E-Mails',
	'Class:MailInboxStandard/Attribute:behavior' => 'Verhalten bei der Verarbeitung einer E-Mail',
	'Class:MailInboxStandard/Attribute:behavior/Value:create_only' => 'Neue Tickets erstellen',
	'Class:MailInboxStandard/Attribute:behavior/Value:update_only' => 'Bestehende Tickets aktualisieren',
	'Class:MailInboxStandard/Attribute:behavior/Value:both' => 'Tickets erstellen oder aktualisieren',

	'Class:MailInboxStandard/Attribute:email_storage' => 'Nach der Verarbeitung der E-Mail',
	'Class:MailInboxStandard/Attribute:email_storage+' => 'Aktion, die nach der Verarbeitung der E-Mail ausgeführt werden soll. Für beste Leistung: wenn eine Archivierung gewünscht ist, wird empfohlen, erfolgreich verarbeitete E-Mails in einen anderen Ordner zu verschieben.',
	'Class:MailInboxStandard/Attribute:email_storage/Value:keep' => 'Im selben Ordner belassen',
	'Class:MailInboxStandard/Attribute:email_storage/Value:delete' => 'Sofort löschen',
	'Class:MailInboxStandard/Attribute:email_storage/Value:move' => 'In einen anderen Ordner verschieben',

	'Class:MailInboxStandard/Attribute:target_class' => 'Ticketklasse',
	'Class:MailInboxStandard/Attribute:target_class/Value:Incident' => 'Störung',
	'Class:MailInboxStandard/Attribute:target_class/Value:UserRequest' => 'Benutzeranfrage',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change' => 'Änderung',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange' => 'Routineänderung',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange' => 'Normale Änderung',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange' => 'Notfalländerung',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem' => 'Problem',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem+' => '',
	'Class:MailInboxStandard/Attribute:debug_trace' => 'Debug-Trace',
	'Class:MailInboxStandard/Attribute:debug_trace+' => '',
	'Class:MailInboxStandard/Attribute:target_folder' => 'Zielordner',
	'Class:MailInboxStandard/Attribute:target_folder+' => 'Die E-Mail wird nach der Verarbeitung (per IMAP-Protokoll) in diesen Zielordner verschoben. Denken Sie daran, die Einstellung "Nach der Verarbeitung der E-Mail" auf "In einen anderen Ordner verschieben" zu setzen.',

	'Class:MailInboxStandard/Attribute:attcode_description' => 'Beschreibungsattribut',
	'Class:MailInboxStandard/Attribute:attcode_description+' => 'Attributcode der Zielklasse, der die anfängliche Ticketbeschreibung erhalten soll. Standard: "description", wenn leer gelassen.',
	'Class:MailInboxStandard/Attribute:attcode_caselog' => 'Attribut des Journals (Case Log)',
	'Class:MailInboxStandard/Attribute:attcode_caselog+' => 'Attributcode der Zielklasse (Journal), der neue Einträge erhalten soll, wenn das Ticket erstellt und/oder aktualisiert wird. Standard: "public_log", wenn leer oder ungültig.',

	'Class:MailInboxStandard/Attribute:ticket_default_values' => 'Standardwerte für neues Ticket',
	'Class:MailInboxStandard/Attribute:ticket_default_title' => 'Standardtitel (wenn Betreff leer ist)',
	'Class:MailInboxStandard/Attribute:title_pattern+' => 'Im Betreff zu suchendes Muster',
	'Class:MailInboxStandard/Attribute:title_pattern' => 'Titelmuster',
	'Class:MailInboxStandard/Attribute:title_pattern?' => 'Verwenden Sie PCRE-Syntax einschließlich Start- und End-Trennzeichen, um festzulegen, wie die Ticketreferenz (Muster) aussieht, damit E-Mails mit Tickets verknüpft werden können.',

	'Class:MailInboxStandard/Attribute:title_pattern_ignore_patterns' => 'Muster im Betreff ignorieren (reguläre Ausdrücke, einer pro Zeile)',

	'Class:MailInboxStandard/Attribute:stimuli' => 'Anzuwendende Stimuli',
	'Class:MailInboxStandard/Attribute:stimuli+' => 'Einen Stimulus anwenden, wenn sich das Ticket in einem bestimmten Zustand befindet',
	'Class:MailInboxStandard/Attribute:stimuli?' => 'Eine Liste von Zustandscode:Stimuluscode (einer pro Zeile), um den anzuwendenden Stimulus festzulegen (nur nach der Aktualisierung eines bestehenden Tickets) für den jeweiligen Zustand des Tickets. Dies ist zum Beispiel nützlich, um ein Ticket, das sich im Zustand "wartend" befindet, automatisch neu zuzuweisen. Verwenden Sie das Format <zustandscode>:<stimuluscode>',


	'Class:MailInboxStandard/Attribute:trace' => 'Debug-Trace',
	'Class:MailInboxStandard/Attribute:trace/Value:yes' => 'Ja',
	'Class:MailInboxStandard/Attribute:trace/Value:no' => 'Nein',

	'Class:MailInboxStandard/Attribute:debug_log' => 'Debug-Protokoll',

	'Class:MailInboxStandard/Attribute:error_behavior' => 'Verhalten bei einem Fehler während der Verarbeitung',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:delete' => 'Die Nachricht aus dem Postfach löschen',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:mark_as_error' => 'Als Fehler markieren',
	'Class:MailInboxStandard/Attribute:notify_errors_to' => 'Bei Fehlern zu benachrichtigende Kontakte',
	'Class:MailInboxStandard/Attribute:notify_errors_to+' => 'OQL-Abfrage, die die Person(en) zurückgibt (z. B. "SELECT Person WHERE email = \'admin@example.com\'"), an die fehlerhafte E-Mails weitergeleitet werden.',
	'Class:MailInboxStandard/Attribute:notify_from' => 'Absenderadresse',

	'Class:MailInboxStandard/Attribute:mail_aliases' => 'E-Mail-Aliasse',
	'Class:MailInboxStandard/Attribute:mail_aliases+' => 'E-Mail-Aliasse: einer pro Zeile. Reguläre Ausdrücke sind zulässig.',

	'Class:MailInboxStandard/Attribute:authentication_results_authserv_id' => 'Vertrauenswürdige Authentication-Results-Server-ID',
	'Class:MailInboxStandard/Attribute:authentication_results_authserv_id+' => 'Hostname (authserv-id) des Mailservers, der eingehende Post für dieses Postfach tatsächlich authentifiziert (z. B. "mx.google.com"). Wenn festgelegt, berücksichtigen SPF/DKIM-Prüfungen (mit denen der Absenderadresse vertraut wird) nur den von diesem Server hinzugefügten "Authentication-Results"-Header und ignorieren jedes andere Vorkommen, das andernfalls vom Absender gefälscht werden könnte. Leer lassen, um dem zuerst gefundenen Vorkommen zu vertrauen (bisheriges Verhalten).',

	'Class:MailInboxStandard/Attribute:oauth_provider' => 'OAuth-Anbieter',
	'Class:MailInboxStandard/Attribute:oauth_provider+' => '',
	'Class:MailInboxStandard/Attribute:oauth_client_id' => 'OAuth-Client',
	'Class:MailInboxStandard/Attribute:oauth_client_id+' => '',

	// Richtlinie: Anhangskriterien
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width' => 'Min. Breite (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width+' => 'Mindestbildbreite (px). Muss mindestens 1 sein. Zu kleine Bilder werden nicht verarbeitet.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width' => 'Max. Breite (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width+' => 'Maximale Bildbreite (px). Auf 0 setzen, um jede Breite zu akzeptieren. Wenn die php-gd-Erweiterung installiert ist, werden größere Bilder verkleinert. Andernfalls werden sie nicht verarbeitet.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height' => 'Min. Höhe (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height+' => 'Mindesthöhe (px). Muss mindestens 1 sein.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height' => 'Max. Höhe (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height+' => 'Maximale Höhe (px). Auf 0 setzen, um jede Höhe zu akzeptieren. Wenn die php-gd-Erweiterung installiert ist, werden größere Bilder verkleinert. Andernfalls werden sie nicht verarbeitet.',

	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes' => 'MIME-Typen ausschließen',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes+' => 'Anhänge dieser MIME-Typen werden nicht verarbeitet. Geben Sie einen pro Zeile an.',

	// Richtlinie: DKIM-Prüfung
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior' => 'Verhalten bei Verstoß',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:bounce_delete' => 'An Absender zurückweisen und löschen',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:bounce_mark_as_undesired' => 'An Absender zurückweisen und als unerwünscht markieren',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:delete' => 'Die Nachricht aus dem Postfach löschen',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:do_nothing' => 'Nichts tun',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:mark_as_undesired' => 'Als unerwünscht markieren / E-Mail vorübergehend behalten',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_subject' => 'Betreff der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_notification' => 'Nachricht der Zurückweisung',

	// Richtlinie: E-Mail zu groß
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior' => 'Verhalten bei Verstoß',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_delete' => 'An Absender zurückweisen und löschen',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_mark_as_undesired' => 'An Absender zurückweisen und als unerwünscht markieren',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:delete' => 'Die Nachricht aus dem Postfach löschen',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:do_nothing' => 'Nichts tun',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:mark_as_undesired' => 'Als unerwünscht markieren / E-Mail vorübergehend behalten',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_subject' => 'Betreff der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_notification' => 'Nachricht der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB' => 'Max. Größe (MB)',
	'Error:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB+' => 'Maximale Größe der E-Mail und ihrer Anhänge. Größere E-Mails werden nicht verarbeitet. Auf 0 setzen, um zu deaktivieren.',

	// Richtlinie: Anhang - verbotener MIME-Typ
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior' => 'Verhalten bei Verstoß',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:fallback_ignore_forbidden_attachments' => 'Rückfalllösung: verbotene Anhänge ignorieren',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_delete' => 'An Absender zurückweisen und löschen',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_mark_as_undesired' => 'An Absender zurückweisen und als unerwünscht markieren',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:delete' => 'Die Nachricht aus dem Postfach löschen',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:do_nothing' => 'Nichts tun',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:mark_as_undesired' => 'Als unerwünscht markieren / E-Mail vorübergehend behalten',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_subject' => 'Betreff der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_notification' => 'Nachricht der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_mimetypes' => 'MIME-Typen (einer pro Zeile)',

	// Richtlinie: kein Betreff
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior' => 'Verhalten bei Verstoß',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:fallback_default_subject' => 'Rückfalllösung: Standardbetreff verwenden',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_delete' => 'An Absender zurückweisen und löschen',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_mark_as_undesired' => 'An Absender zurückweisen und als unerwünscht markieren',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:delete' => 'Die Nachricht aus dem Postfach löschen',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:do_nothing' => 'Nichts tun',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:mark_as_undesired' => 'Als unerwünscht markieren / E-Mail vorübergehend behalten',
	'Class:MailInboxStandard/Attribute:policy_no_subject_subject' => 'Betreff der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_no_subject_notification' => 'Nachricht der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_no_subject_default_value' => 'Standardbetreff',

	// Richtlinie: unbekannter Absender
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior' => 'Verhalten bei Verstoß',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:fallback_create_person' => 'Rückfalllösung: Person erstellen',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_delete' => 'An Absender zurückweisen und löschen',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_mark_as_undesired' => 'An Absender zurückweisen und als unerwünscht markieren',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:delete' => 'Die Nachricht aus dem Postfach löschen',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:mark_as_undesired' => 'Als unerwünscht markieren / E-Mail vorübergehend behalten',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_subject' => 'Betreff der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_notification' => 'Nachricht der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_default_values' => 'Standardwerte für neue Person (einer pro Zeile, Beispiel: org_id:1)',

	// Richtlinie: andere Empfänger
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior' => 'Verhalten bei Verstoß',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_existing_other_contacts' => 'Rückfalllösung: nur bestehende Kontakte verknüpfen',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_other_contacts' => 'Rückfalllösung: Kontakt immer verknüpfen, bei Bedarf erstellen',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_ignore_other_contacts' => 'Rückfalllösung: alle anderen Kontakte ignorieren',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_delete' => 'An Absender zurückweisen und löschen',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_mark_as_undesired' => 'An Absender zurückweisen und als unerwünscht markieren',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:delete' => 'Die Nachricht aus dem Postfach löschen',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:do_nothing' => 'Nichts tun',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:mark_as_undesired' => 'Als unerwünscht markieren / E-Mail vorübergehend behalten',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_subject' => 'Betreff der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_notification' => 'Nachricht der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_default_values' => 'Standardwerte für neue Person (einer pro Zeile, Beispiel: org_id:1)',

	// Richtlinie: geschlossenes Ticket
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior' => 'Verhalten bei Verstoß',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior+' => 'Hinweis: standardmäßig können geschlossene Tickets nicht wieder geöffnet werden. Dies erfordert Änderungen am Datenmodell.',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:fallback_reopen' => 'Rückfalllösung: Ticket wieder öffnen',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_delete' => 'An Absender zurückweisen und löschen',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_mark_as_undesired' => 'An Absender zurückweisen und als unerwünscht markieren',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:delete' => 'Die Nachricht aus dem Postfach löschen',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:do_nothing' => 'Nichts tun',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:mark_as_undesired' => 'Als unerwünscht markieren / E-Mail vorübergehend behalten',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_subject' => 'Betreff der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_notification' => 'Nachricht der Zurückweisung',

	// Richtlinie: gelöstes Ticket
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior' => 'Verhalten bei Verstoß',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:fallback_reopen' => 'Rückfalllösung: Ticket wieder öffnen',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_delete' => 'An Absender zurückweisen und löschen',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_mark_as_undesired' => 'An Absender zurückweisen und als unerwünscht markieren',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:delete' => 'Die Nachricht aus dem Postfach löschen',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:do_nothing' => 'Nichts tun',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:mark_as_undesired' => 'Als unerwünscht markieren / E-Mail vorübergehend behalten',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_subject' => 'Betreff der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_notification' => 'Nachricht der Zurückweisung',

	// Richtlinie: unbekanntes Ticket
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior' => 'Verhalten bei Verstoß',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_delete' => 'An Absender zurückweisen und löschen',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_mark_as_undesired' => 'An Absender zurückweisen und als unerwünscht markieren',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:delete' => 'Die Nachricht aus dem Postfach löschen',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:do_nothing' => 'Nichts tun',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:mark_as_undesired' => 'Als unerwünscht markieren / E-Mail vorübergehend behalten',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_subject' => 'Betreff der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_notification' => 'Nachricht der Zurückweisung',

	// Richtlinie: unerwünschte Titelmuster
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior' => 'Verhalten bei Verstoß',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:delete' => 'Die Nachricht aus dem Postfach löschen',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_delete' => 'An Absender zurückweisen und löschen',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_mark_as_undesired' => 'An Absender zurückweisen und als unerwünscht markieren',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:do_nothing' => 'Nichts tun',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:mark_as_undesired' => 'Als unerwünscht markieren / E-Mail vorübergehend behalten',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_subject' => 'Betreff der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_notification' => 'Nachricht der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_patterns' => 'Unerwünschte Muster im Betreff (reguläre Ausdrücke, einer pro Zeile)',


	// Richtlinie: Teile des Titels entfernen
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior' => 'Muster aus dem Betreff entfernen',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:do_nothing' => 'Nichts tun',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:fallback_remove' => 'Teil(e) aus dem Betreff entfernen',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_patterns' => 'Aus dem Betreff zu entfernende Muster (reguläre Ausdrücke, einer pro Zeile)',

	// Richtlinie: Absender muss mit dem des ursprünglichen Tickets übereinstimmen
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior' => 'Verhalten bei Verstoß',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:delete' => 'Die Nachricht aus dem Postfach löschen',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_delete' => 'An Absender zurückweisen und löschen',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_mark_as_undesired' => 'An Absender zurückweisen und als unerwünscht markieren',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:do_nothing' => 'Nichts tun',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:mark_as_undesired' => 'Als unerwünscht markieren / E-Mail vorübergehend behalten',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_subject' => 'Betreff der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_notification' => 'Nachricht der Zurückweisung',

	// Richtlinie: automatische Antwort
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior' => 'Verhalten bei Verstoß',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:delete' => 'Die Nachricht aus dem Postfach löschen',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:do_nothing' => 'Nichts tun',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:mark_as_undesired' => 'Als unerwünscht markieren / E-Mail vorübergehend behalten',

	// Richtlinie: Unzustellbarkeitsbenachrichtigung
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior' => 'Verhalten bei Verstoß',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:delete' => 'Die Nachricht aus dem Postfach löschen',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:do_nothing' => 'Nichts tun',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:mark_as_undesired' => 'Als unerwünscht markieren',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive' => 'Absender als inaktiv markieren',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive+' => 'Der Absender wird als inaktiv markiert, wenn der Zustellfehler dauerhaft zu sein scheint und mit hoher Sicherheit davon ausgegangen werden kann, dass der Empfänger über diese E-Mail-Adresse nicht mehr erreichbar ist.',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:yes' => 'Ja',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:no' => 'Nein',

	// Schritt: Attribute des Absenders aktualisieren
	'Class:MailInboxStandard/Attribute:step_update_caller_attributes' => 'Attribute des Absenders aktualisieren (einer pro Zeile, Beispiel: status:active)',
	'Class:MailInboxStandard/Attribute:step_update_caller_attributes+' => 'Wird auf den Personendatensatz des Absenders angewendet, wann immer eine erfolgreich verarbeitete E-Mail einem bestehenden Kontakt zugeordnet wird. Leer lassen, um nichts zu aktualisieren. In den Werten können Mail-Platzhalter (z. B. $mail->caller_email$) verwendet werden.',

	// Richtlinie: E-Mail-Adresse des Absenders
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior' => 'Verhalten bei Verstoß',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_delete' => 'An Absender zurückweisen und löschen',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_mark_as_undesired' => 'An Absender zurückweisen und als unerwünscht markieren',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:delete' => 'Die Nachricht aus dem Postfach löschen',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:do_nothing' => 'Nichts tun',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:inactive' => 'Inaktiv',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:mark_as_undesired' => 'Als unerwünscht markieren',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_subject' => 'Betreff der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_notification' => 'Nachricht der Zurückweisung',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns' => 'Muster',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns+' => 'Jede E-Mail, deren Absenderadresse mit einem der definierten regulären Ausdrücke (einer pro Zeile) übereinstimmt, wird als Verstoß betrachtet.',



	// Überschriften
	'MailInbox:Server' => 'Postfachkonfiguration',
	'MailInbox:Behavior' => 'Verhalten bei eingehenden E-Mails',
	'MailInbox:Errors' => 'E-Mails mit Fehlern',
	'MailInbox:Settings' => 'Einstellungen',

	// Validierungsmeldungen
	'MailInbox:Error:TargetFolderRequired' => 'Für ein aktives Postfach muss der Zielordner angegeben werden.',
	'MailInbox:Error:CaseLogAttCodeRequired' => 'Der Attributcode des Journals muss ein gültiges Attribut der Zielklasse \'%1$s\' sein.',
	'MailInbox:Error:DescriptionOrCaseLogAttCodeRequired' => 'Entweder der Attributcode der Beschreibung oder der Attributcode des Journals muss ein gültiges Attribut der Zielklasse \'%1$s\' sein.',
	'MailInbox:Error:DescriptionAttCodeMustHaveMaxSize' => 'Das Beschreibungsattribut \'%1$s\' der Zielklasse \'%2$s\' hat keine maximale Größe und kann nicht zur Speicherung der anfänglichen Ticketbeschreibung verwendet werden.',

	// Schritte
	'MailInbox:StepAttachmentCriteria' => 'Eingebettete E-Mail-Bilder',
	'MailInbox:PolicyDkimCheck' => 'DKIM-Prüfung',
	'MailInbox:PolicyMailSize' => 'E-Mail-Größe',
	'MailInbox:PolicyAttachmentForbiddenMimeType' => 'Anhang - Verbotener MIME-Typ',
	'MailInbox:PolicyAttachmentIgnoredMimeType' => 'Anhang - Ignorierter MIME-Typ',
	'MailInbox:PolicyUndesiredPattern' => 'Unerwünschte Titelmuster',
	'MailInbox:PolicyRemovePattern' => 'Unerwünschte Titelmuster entfernen',
	'MailInbox:PolicyIgnorePattern' => 'Unerwünschte Titelmuster ignorieren',
	'MailInbox:PolicyResolvedTicket' => 'Gelöste Tickets',
	'MailInbox:PolicyClosedTicket' => 'Geschlossene Tickets',
	'MailInbox:PolicyUnknownTicket' => 'Unbekannte Tickets',
	'MailInbox:PolicyNoSubject' => 'Kein Betreff',
	'MailInbox:PolicyUnknownCaller' => 'Unbekannter Absender',
	'MailInbox:PolicyOtherRecipients' => 'Andere Empfänger, die in An: oder CC: angegeben sind',
	'MailInbox:PolicyBounceOtherEmailCallerThanTicketCaller' => 'Akzeptierte E-Mail-Antworten auf die E-Mail-Adresse des ursprünglichen Ticket-Absenders beschränken',
	'MailInbox:PolicyAutoReply' => 'Automatische Antwort',
	'MailInbox:PolicyNonDeliveryReport' => 'Unzustellbarkeitsbenachrichtigungen',
	'MailInbox:StepUpdateCallerAttributes' => 'Attribute des Absenders aktualisieren',
	'MailInbox:PolicySenderEmailAddress' => 'Absender anhand von E-Mail-Adressmustern blockieren',

	// Validierungsmeldungen
	'MailInbox:Error:NotifyErrorsToMustTargetContact' => 'Die Abfrage zur Auswahl von %1$s muss auf die Klasse \'Contact\' (oder eine Unterklasse wie \'Person\' oder \'Team\') abzielen, nicht auf \'%2$s\'.',

	'Menu:MailInboxes' => 'Postfächer für eingehende E-Mail',
	'Menu:MailInboxes+' => 'Konfiguration der Postfächer, die nach eingehenden E-Mails durchsucht werden',

	'MailInboxStandard:DebugTrace' => 'Debug-Trace',
	'MailInboxStandard:DebugTraceNotActive' => 'Aktivieren Sie den Debug-Trace für dieses Postfach, um ein detailliertes Protokoll der Vorgänge zu sehen.',
	'MailInboxStandard:DebugTraceAccessDenied' => 'Sie sind nicht berechtigt, den Debug-Trace dieses Postfachs anzuzeigen.',

	'MailPolicy:CreateOrUpdateTicket:NoDescriptionProvided' => 'Keine Beschreibung angegeben',

	// OAuth2
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Title' => 'Ein Postfach erstellen',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Description' => 'Ein neues Postfach erstellen, um E-Mails von einem entfernten Mailanbieter mit dieser OAuth-Verbindung als Authentifizierungsmethode abzurufen',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:CreateNewMailbox' => 'Ein neues Postfach erstellen',
	'UI:OAuthEmailSynchro:Error:UnknownVendor' => 'Der OAuth-Anbieter %1$s existiert nicht',

	// lnkEmailUidToTicket
	'Class:lnkEmailUidToTicket' => 'Verknüpfung E-Mail-UID / Ticket',
	'Class:lnkEmailUidToTicket/Attribute:message_uid' => 'Nachrichten-UID',
	'Class:lnkEmailUidToTicket/Attribute:ticket_id' => 'Ticket-ID',
	'Class:lnkEmailUidToTicket/Attribute:mailbox_id' => 'Postfach-ID',
	'Class:lnkEmailUidToTicket/UniquenessRule:unique_message_uid_and_mailbox_id_and_ticket_id' => 'Die Kombination aus Postfach-ID, Nachrichten-UID und Ticket-ID muss eindeutig sein.',


));

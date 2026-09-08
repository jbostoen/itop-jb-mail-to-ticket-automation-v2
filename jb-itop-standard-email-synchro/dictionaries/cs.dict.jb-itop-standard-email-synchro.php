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
	'Class:MailInboxStandard' => 'Poštovní schránka IMAP',
	'Class:MailInboxStandard+' => 'Zdroj příchozích e-mailů',
	'Class:MailInboxStandard/Attribute:behavior' => 'Chování při zpracování e-mailu',
	'Class:MailInboxStandard/Attribute:behavior/Value:create_only' => 'Vytvářet nové tikety',
	'Class:MailInboxStandard/Attribute:behavior/Value:update_only' => 'Aktualizovat existující tikety',
	'Class:MailInboxStandard/Attribute:behavior/Value:both' => 'Vytvářet nebo aktualizovat tikety',

	'Class:MailInboxStandard/Attribute:email_storage' => 'Po zpracování e-mailu',
	'Class:MailInboxStandard/Attribute:email_storage+' => 'Akce, která se provede po zpracování e-mailu. Pro nejlepší výkon: pokud je požadována archivace, doporučuje se přesunout úspěšně zpracované e-maily do jiné složky.',
	'Class:MailInboxStandard/Attribute:email_storage/Value:keep' => 'Ponechat ve stejné složce',
	'Class:MailInboxStandard/Attribute:email_storage/Value:delete' => 'Okamžitě smazat',
	'Class:MailInboxStandard/Attribute:email_storage/Value:move' => 'Přesunout do jiné složky',

	'Class:MailInboxStandard/Attribute:target_class' => 'Třída tiketu',
	'Class:MailInboxStandard/Attribute:target_class/Value:Incident' => 'Incident',
	'Class:MailInboxStandard/Attribute:target_class/Value:UserRequest' => 'Uživatelský požadavek',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change' => 'Změna',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange' => 'Rutinní změna',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange' => 'Standardní změna',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange' => 'Nouzová změna',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem' => 'Problém',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem+' => '',
	'Class:MailInboxStandard/Attribute:debug_trace' => 'Ladicí trasování',
	'Class:MailInboxStandard/Attribute:debug_trace+' => '',
	'Class:MailInboxStandard/Attribute:target_folder' => 'Cílová složka',
	'Class:MailInboxStandard/Attribute:target_folder+' => 'E-mail bude po zpracování přesunut (protokolem IMAP) do této cílové složky. Nezapomeňte aktualizovat nastavení "Po zpracování e-mailu" na "Přesunout do jiné složky".',

	'Class:MailInboxStandard/Attribute:attcode_description' => 'Atribut popisu',
	'Class:MailInboxStandard/Attribute:attcode_description+' => 'Kód atributu cílové třídy, který má obdržet počáteční popis tiketu. Výchozí hodnota: "description", pokud je ponecháno prázdné.',
	'Class:MailInboxStandard/Attribute:attcode_caselog' => 'Atribut deníku (case log)',
	'Class:MailInboxStandard/Attribute:attcode_caselog+' => 'Kód atributu cílové třídy (deník), který má obdržet nové položky při vytvoření a/nebo aktualizaci tiketu. Výchozí hodnota: "public_log", pokud je ponecháno prázdné nebo neplatné.',

	'Class:MailInboxStandard/Attribute:ticket_default_values' => 'Výchozí hodnoty pro nový tiket',
	'Class:MailInboxStandard/Attribute:ticket_default_title' => 'Výchozí název (pokud je předmět prázdný)',
	'Class:MailInboxStandard/Attribute:title_pattern+' => 'Vzor, který se má hledat v předmětu',
	'Class:MailInboxStandard/Attribute:title_pattern' => 'Vzor názvu',
	'Class:MailInboxStandard/Attribute:title_pattern?' => 'Použijte syntaxi PCRE včetně počátečních a koncových oddělovačů, abyste určili, jak vypadá odkaz na tiket (vzor), aby bylo možné e-maily propojit s tikety.',

	'Class:MailInboxStandard/Attribute:title_pattern_ignore_patterns' => 'Ignorovat vzory v předmětu (regulární výrazy, jeden na řádek)',

	'Class:MailInboxStandard/Attribute:stimuli' => 'Podněty k použití',
	'Class:MailInboxStandard/Attribute:stimuli+' => 'Použít podnět, když je tiket v daném stavu',
	'Class:MailInboxStandard/Attribute:stimuli?' => 'Seznam kod_stavu:kod_podnetu (jeden na řádek) definující podnět, který se má použít (pouze po aktualizaci existujícího tiketu) pro daný stav tiketu. To je užitečné například k automatickému přeřazení tiketu, který je ve stavu "čeká". Použijte formát <kod_stavu>:<kod_podnetu>',


	'Class:MailInboxStandard/Attribute:trace' => 'Ladicí trasování',
	'Class:MailInboxStandard/Attribute:trace/Value:yes' => 'Ano',
	'Class:MailInboxStandard/Attribute:trace/Value:no' => 'Ne',

	'Class:MailInboxStandard/Attribute:debug_log' => 'Ladicí protokol',

	'Class:MailInboxStandard/Attribute:error_behavior' => 'Chování při chybě během zpracování',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:delete' => 'Smazat zprávu ze schránky',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:mark_as_error' => 'Označit jako chybu',
	'Class:MailInboxStandard/Attribute:notify_errors_to' => 'Kontakty, které mají být upozorněny na chybu',
	'Class:MailInboxStandard/Attribute:notify_errors_to+' => 'Dotaz OQL vracející osobu/osoby (např. "SELECT Person WHERE email = \'admin@example.com\'"), kterým budou přeposílány e-maily s chybou.',
	'Class:MailInboxStandard/Attribute:notify_from' => 'Adresa odesílatele',

	'Class:MailInboxStandard/Attribute:mail_aliases' => 'Aliasy e-mailu',
	'Class:MailInboxStandard/Attribute:mail_aliases+' => 'Aliasy e-mailu: jeden na řádek. Regulární výrazy jsou povoleny.',

	'Class:MailInboxStandard/Attribute:authentication_results_authserv_id' => 'ID důvěryhodného serveru Authentication-Results',
	'Class:MailInboxStandard/Attribute:authentication_results_authserv_id+' => 'Název hostitele (authserv-id) poštovního serveru, který skutečně ověřuje příchozí poštu pro tuto schránku (např. "mx.google.com"). Pokud je nastaveno, kontroly SPF/DKIM (použité k důvěře v adresu odesílatele) berou v úvahu pouze hlavičku "Authentication-Results" přidanou tímto serverem a ignorují jakýkoli jiný výskyt, který by jinak mohl být odesílatelem zfalšován. Ponechte prázdné, chcete-li důvěřovat prvnímu nalezenému výskytu (dřívější chování).',

	'Class:MailInboxStandard/Attribute:oauth_provider' => 'Poskytovatel OAuth',
	'Class:MailInboxStandard/Attribute:oauth_provider+' => '',
	'Class:MailInboxStandard/Attribute:oauth_client_id' => 'Klient OAuth',
	'Class:MailInboxStandard/Attribute:oauth_client_id+' => '',

	// Zásada: Kritéria příloh
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width' => 'Min. šířka (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width+' => 'Minimální šířka obrázku (px). Musí být alespoň 1. Příliš malé obrázky nebudou zpracovány.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width' => 'Max. šířka (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width+' => 'Maximální šířka obrázku (px). Nastavte na 0, chcete-li přijmout libovolnou šířku. Pokud je nainstalováno rozšíření php-gd, velikost větších obrázků bude změněna. Jinak nebudou zpracovány.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height' => 'Min. výška (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height+' => 'Minimální výška (px). Musí být alespoň 1.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height' => 'Max. výška (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height+' => 'Maximální výška (px). Nastavte na 0, chcete-li přijmout libovolnou výšku. Pokud je nainstalováno rozšíření php-gd, velikost větších obrázků bude změněna. Jinak nebudou zpracovány.',

	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes' => 'Vyloučit typy MIME',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes+' => 'Přílohy těchto typů MIME nebudou zpracovány. Zadejte jeden na řádek.',

	// Zásada: kontrola DKIM
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior' => 'Chování při porušení',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:bounce_delete' => 'Odmítnout zpět odesílateli a smazat',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:bounce_mark_as_undesired' => 'Odmítnout zpět odesílateli a označit jako nežádoucí',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:delete' => 'Smazat zprávu ze schránky',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:do_nothing' => 'Nedělat nic',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:inactive' => 'Neaktivní',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:mark_as_undesired' => 'Označit jako nežádoucí / Dočasně ponechat e-mail',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_subject' => 'Předmět odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_notification' => 'Zpráva o odmítnutí',

	// Zásada: e-mail je příliš velký
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior' => 'Chování při porušení',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_delete' => 'Odmítnout zpět odesílateli a smazat',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_mark_as_undesired' => 'Odmítnout zpět odesílateli a označit jako nežádoucí',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:delete' => 'Smazat zprávu ze schránky',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:do_nothing' => 'Nedělat nic',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:inactive' => 'Neaktivní',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:mark_as_undesired' => 'Označit jako nežádoucí / Dočasně ponechat e-mail',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_subject' => 'Předmět odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_notification' => 'Zpráva o odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB' => 'Max. velikost (MB)',
	'Error:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB+' => 'Maximální velikost e-mailu a jeho příloh. Větší e-maily nebudou zpracovány. Nastavte na 0, chcete-li zakázat.',

	// Zásada: příloha - zakázaný typ MIME
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior' => 'Chování při porušení',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:fallback_ignore_forbidden_attachments' => 'Záložní řešení: ignorovat zakázané přílohy',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_delete' => 'Odmítnout zpět odesílateli a smazat',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_mark_as_undesired' => 'Odmítnout zpět odesílateli a označit jako nežádoucí',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:delete' => 'Smazat zprávu ze schránky',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:do_nothing' => 'Nedělat nic',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:inactive' => 'Neaktivní',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:mark_as_undesired' => 'Označit jako nežádoucí / Dočasně ponechat e-mail',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_subject' => 'Předmět odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_notification' => 'Zpráva o odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_mimetypes' => 'Typy MIME (jeden na řádek)',

	// Zásada: bez předmětu
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior' => 'Chování při porušení',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:fallback_default_subject' => 'Záložní řešení: použít výchozí předmět',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_delete' => 'Odmítnout zpět odesílateli a smazat',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_mark_as_undesired' => 'Odmítnout zpět odesílateli a označit jako nežádoucí',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:delete' => 'Smazat zprávu ze schránky',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:do_nothing' => 'Nedělat nic',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:inactive' => 'Neaktivní',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:mark_as_undesired' => 'Označit jako nežádoucí / Dočasně ponechat e-mail',
	'Class:MailInboxStandard/Attribute:policy_no_subject_subject' => 'Předmět odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_no_subject_notification' => 'Zpráva o odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_no_subject_default_value' => 'Výchozí předmět',

	// Zásada: neznámý odesílatel
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior' => 'Chování při porušení',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:fallback_create_person' => 'Záložní řešení: vytvořit osobu',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_delete' => 'Odmítnout zpět odesílateli a smazat',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_mark_as_undesired' => 'Odmítnout zpět odesílateli a označit jako nežádoucí',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:delete' => 'Smazat zprávu ze schránky',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:mark_as_undesired' => 'Označit jako nežádoucí / Dočasně ponechat e-mail',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_subject' => 'Předmět odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_notification' => 'Zpráva o odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_default_values' => 'Výchozí hodnoty pro novou osobu (jedna na řádek, příklad: org_id:1)',

	// Zásada: ostatní příjemci
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior' => 'Chování při porušení',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_existing_other_contacts' => 'Záložní řešení: propojit pouze existující kontakty',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_other_contacts' => 'Záložní řešení: vždy propojit kontakt, v případě potřeby jej vytvořit',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_ignore_other_contacts' => 'Záložní řešení: ignorovat všechny ostatní kontakty',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_delete' => 'Odmítnout zpět odesílateli a smazat',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_mark_as_undesired' => 'Odmítnout zpět odesílateli a označit jako nežádoucí',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:delete' => 'Smazat zprávu ze schránky',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:do_nothing' => 'Nedělat nic',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:inactive' => 'Neaktivní',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:mark_as_undesired' => 'Označit jako nežádoucí / Dočasně ponechat e-mail',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_subject' => 'Předmět odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_notification' => 'Zpráva o odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_default_values' => 'Výchozí hodnoty pro novou osobu (jedna na řádek, příklad: org_id:1)',

	// Zásada: uzavřený tiket
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior' => 'Chování při porušení',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior+' => 'Pozn.: uzavřené tikety ve výchozím nastavení nelze znovu otevřít. Vyžaduje to změny v datovém modelu.',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:fallback_reopen' => 'Záložní řešení: znovu otevřít tiket',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_delete' => 'Odmítnout zpět odesílateli a smazat',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_mark_as_undesired' => 'Odmítnout zpět odesílateli a označit jako nežádoucí',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:delete' => 'Smazat zprávu ze schránky',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:do_nothing' => 'Nedělat nic',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:inactive' => 'Neaktivní',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:mark_as_undesired' => 'Označit jako nežádoucí / Dočasně ponechat e-mail',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_subject' => 'Předmět odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_notification' => 'Zpráva o odmítnutí',

	// Zásada: vyřešený tiket
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior' => 'Chování při porušení',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:fallback_reopen' => 'Záložní řešení: znovu otevřít tiket',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_delete' => 'Odmítnout zpět odesílateli a smazat',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_mark_as_undesired' => 'Odmítnout zpět odesílateli a označit jako nežádoucí',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:delete' => 'Smazat zprávu ze schránky',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:do_nothing' => 'Nedělat nic',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:inactive' => 'Neaktivní',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:mark_as_undesired' => 'Označit jako nežádoucí / Dočasně ponechat e-mail',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_subject' => 'Předmět odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_notification' => 'Zpráva o odmítnutí',

	// Zásada: neznámý tiket
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior' => 'Chování při porušení',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_delete' => 'Odmítnout zpět odesílateli a smazat',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_mark_as_undesired' => 'Odmítnout zpět odesílateli a označit jako nežádoucí',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:delete' => 'Smazat zprávu ze schránky',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:do_nothing' => 'Nedělat nic',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:inactive' => 'Neaktivní',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:mark_as_undesired' => 'Označit jako nežádoucí / Dočasně ponechat e-mail',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_subject' => 'Předmět odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_notification' => 'Zpráva o odmítnutí',

	// Zásada: nežádoucí vzory v názvu
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior' => 'Chování při porušení',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:delete' => 'Smazat zprávu ze schránky',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_delete' => 'Odmítnout zpět odesílateli a smazat',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_mark_as_undesired' => 'Odmítnout zpět odesílateli a označit jako nežádoucí',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:do_nothing' => 'Nedělat nic',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:inactive' => 'Neaktivní',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:mark_as_undesired' => 'Označit jako nežádoucí / Dočasně ponechat e-mail',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_subject' => 'Předmět odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_notification' => 'Zpráva o odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_patterns' => 'Nežádoucí vzory v předmětu (regulární výrazy, jeden na řádek)',


	// Zásada: odstranění částí názvu
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior' => 'Odstranit vzory z předmětu',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:do_nothing' => 'Nedělat nic',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:inactive' => 'Neaktivní',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:fallback_remove' => 'Odstranit část(i) předmětu',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_patterns' => 'Vzory, které se mají odstranit z předmětu (regulární výrazy, jeden na řádek)',

	// Zásada: odesílatel musí být stejný jako původní odesílatel tiketu
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior' => 'Chování při porušení',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:delete' => 'Smazat zprávu ze schránky',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_delete' => 'Odmítnout zpět odesílateli a smazat',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_mark_as_undesired' => 'Odmítnout zpět odesílateli a označit jako nežádoucí',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:do_nothing' => 'Nedělat nic',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:inactive' => 'Neaktivní',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:mark_as_undesired' => 'Označit jako nežádoucí / Dočasně ponechat e-mail',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_subject' => 'Předmět odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_notification' => 'Zpráva o odmítnutí',

	// Zásada: automatická odpověď
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior' => 'Chování při porušení',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:delete' => 'Smazat zprávu ze schránky',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:do_nothing' => 'Nedělat nic',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:inactive' => 'Neaktivní',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:mark_as_undesired' => 'Označit jako nežádoucí / Dočasně ponechat e-mail',

	// Zásada: oznámení o nedoručení
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior' => 'Chování při porušení',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:delete' => 'Smazat zprávu ze schránky',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:do_nothing' => 'Nedělat nic',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:inactive' => 'Neaktivní',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:mark_as_undesired' => 'Označit jako nežádoucí',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive' => 'Označit odesílatele jako neaktivního',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive+' => 'Odesílatel bude označen jako neaktivní, pokud se selhání doručení jeví jako trvalé a existuje vysoká míra jistoty, že příjemce již není na této e-mailové adrese dosažitelný.',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:yes' => 'Ano',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:no' => 'Ne',

	// Krok: aktualizovat atributy odesílatele
	'Class:MailInboxStandard/Attribute:step_update_caller_attributes' => 'Aktualizovat atributy odesílatele (jeden na řádek, příklad: status:active)',
	'Class:MailInboxStandard/Attribute:step_update_caller_attributes+' => 'Použije se na záznam osoby odesílatele pokaždé, když je úspěšně zpracovaný e-mail spárován s existujícím kontaktem. Ponechte prázdné, chcete-li nic neaktualizovat. V hodnotách lze použít zástupné symboly pošty (např. $mail->caller_email$).',

	// Zásada: e-mailová adresa odesílatele
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior' => 'Chování při porušení',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_delete' => 'Odmítnout zpět odesílateli a smazat',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_mark_as_undesired' => 'Odmítnout zpět odesílateli a označit jako nežádoucí',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:delete' => 'Smazat zprávu ze schránky',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:do_nothing' => 'Nedělat nic',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:inactive' => 'Neaktivní',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:mark_as_undesired' => 'Označit jako nežádoucí',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_subject' => 'Předmět odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_notification' => 'Zpráva o odmítnutí',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns' => 'Vzory',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns+' => 'Každý e-mail, jehož e-mailová adresa odesílatele odpovídá jednomu z definovaných regulárních výrazů (jeden na řádek), bude považován za porušení.',



	// Nadpisy
	'MailInbox:Server' => 'Konfigurace schránky',
	'MailInbox:Behavior' => 'Chování u příchozích e-mailů',
	'MailInbox:Errors' => 'E-maily s chybou',
	'MailInbox:Settings' => 'Nastavení',

	// Ověřovací zprávy
	'MailInbox:Error:TargetFolderRequired' => 'Pro aktivní schránku musí být zadána cílová složka.',
	'MailInbox:Error:CaseLogAttCodeRequired' => 'Kód atributu deníku musí být platným atributem cílové třídy \'%1$s\'.',
	'MailInbox:Error:DescriptionOrCaseLogAttCodeRequired' => 'Buď kód atributu popisu, nebo kód atributu deníku musí být platným atributem cílové třídy \'%1$s\'.',
	'MailInbox:Error:DescriptionAttCodeMustHaveMaxSize' => 'Atribut popisu \'%1$s\' cílové třídy \'%2$s\' nemá maximální velikost a nelze jej použít k uložení počátečního popisu tiketu.',

	// Kroky
	'MailInbox:StepAttachmentCriteria' => 'Vložené obrázky v e-mailu',
	'MailInbox:PolicyDkimCheck' => 'Kontrola DKIM',
	'MailInbox:PolicyMailSize' => 'Velikost pošty',
	'MailInbox:PolicyAttachmentForbiddenMimeType' => 'Příloha - zakázaný typ MIME',
	'MailInbox:PolicyAttachmentIgnoredMimeType' => 'Příloha - ignorovaný typ MIME',
	'MailInbox:PolicyUndesiredPattern' => 'Nežádoucí vzory v názvu',
	'MailInbox:PolicyRemovePattern' => 'Odstranit nežádoucí vzory v názvu',
	'MailInbox:PolicyIgnorePattern' => 'Ignorovat nežádoucí vzory v názvu',
	'MailInbox:PolicyResolvedTicket' => 'Vyřešené tikety',
	'MailInbox:PolicyClosedTicket' => 'Uzavřené tikety',
	'MailInbox:PolicyUnknownTicket' => 'Neznámé tikety',
	'MailInbox:PolicyNoSubject' => 'Bez předmětu',
	'MailInbox:PolicyUnknownCaller' => 'Neznámý odesílatel',
	'MailInbox:PolicyOtherRecipients' => 'Ostatní příjemci uvedení v Komu: nebo Kopie:',
	'MailInbox:PolicyBounceOtherEmailCallerThanTicketCaller' => 'Omezit přijaté odpovědi na e-mailovou adresu původního odesílatele tiketu',
	'MailInbox:PolicyAutoReply' => 'Automatická odpověď',
	'MailInbox:PolicyNonDeliveryReport' => 'Oznámení o nedoručení',
	'MailInbox:StepUpdateCallerAttributes' => 'Aktualizovat atributy odesílatele',
	'MailInbox:PolicySenderEmailAddress' => 'Blokovat odesílatele pomocí vzorů e-mailových adres',

	// Ověřovací zprávy
	'MailInbox:Error:NotifyErrorsToMustTargetContact' => 'Dotaz pro výběr %1$s musí cílit na třídu \'Contact\' (nebo podtřídu jako \'Person\' či \'Team\'), nikoli na \'%2$s\'.',

	'Menu:MailInboxes' => 'Schránky pro příchozí poštu',
	'Menu:MailInboxes+' => 'Konfigurace schránek, které se mají prohledávat na příchozí e-maily',

	'MailInboxStandard:DebugTrace' => 'Ladicí trasování',
	'MailInboxStandard:DebugTraceNotActive' => 'Aktivujte ladicí trasování na této schránce, chcete-li vidět podrobný protokol toho, co se děje.',
	'MailInboxStandard:DebugTraceAccessDenied' => 'Nemáte oprávnění zobrazit ladicí trasování této schránky.',

	'MailPolicy:CreateOrUpdateTicket:NoDescriptionProvided' => 'Nebyl uveden žádný popis',

	// OAuth2
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Title' => 'Vytvořit schránku',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Description' => 'Vytvořit novou schránku pro získávání e-mailů od vzdáleného poskytovatele pošty pomocí tohoto připojení OAuth jako metody ověření',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:CreateNewMailbox' => 'Vytvořit novou schránku',
	'UI:OAuthEmailSynchro:Error:UnknownVendor' => 'Poskytovatel OAuth %1$s neexistuje',

	// lnkEmailUidToTicket
	'Class:lnkEmailUidToTicket' => 'Propojení UID e-mailu / tiketu',
	'Class:lnkEmailUidToTicket/Attribute:message_uid' => 'UID zprávy',
	'Class:lnkEmailUidToTicket/Attribute:ticket_id' => 'ID tiketu',
	'Class:lnkEmailUidToTicket/Attribute:mailbox_id' => 'ID schránky',
	'Class:lnkEmailUidToTicket/UniquenessRule:unique_message_uid_and_mailbox_id_and_ticket_id' => 'Kombinace ID schránky, UID zprávy a ID tiketu musí být jedinečná.',


));

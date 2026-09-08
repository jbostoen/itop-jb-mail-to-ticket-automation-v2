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

Dict::Add('PT PT', 'Portuguese', 'Português', array(

	// Dictionary entries go here
	'Class:MailInboxStandard' => 'Caixa de correio IMAP',
	'Class:MailInboxStandard+' => 'Origem dos e-mails recebidos',
	'Class:MailInboxStandard/Attribute:behavior' => 'Comportamento ao processar um e-mail',
	'Class:MailInboxStandard/Attribute:behavior/Value:create_only' => 'Criar novos tickets',
	'Class:MailInboxStandard/Attribute:behavior/Value:update_only' => 'Atualizar tickets existentes',
	'Class:MailInboxStandard/Attribute:behavior/Value:both' => 'Criar ou atualizar tickets',

	'Class:MailInboxStandard/Attribute:email_storage' => 'Após processar o e-mail',
	'Class:MailInboxStandard/Attribute:email_storage+' => 'Ação a executar após processar o e-mail. Para melhor desempenho: se pretender arquivar, recomenda-se mover os e-mails processados com sucesso para outra pasta.',
	'Class:MailInboxStandard/Attribute:email_storage/Value:keep' => 'Manter na mesma pasta',
	'Class:MailInboxStandard/Attribute:email_storage/Value:delete' => 'Eliminar imediatamente',
	'Class:MailInboxStandard/Attribute:email_storage/Value:move' => 'Mover para outra pasta',

	'Class:MailInboxStandard/Attribute:target_class' => 'Classe do ticket',
	'Class:MailInboxStandard/Attribute:target_class/Value:Incident' => 'Incidente',
	'Class:MailInboxStandard/Attribute:target_class/Value:UserRequest' => 'Pedido de utilizador',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change' => 'Alteração',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange' => 'Alteração de rotina',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange' => 'Alteração normal',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange' => 'Alteração de emergência',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem' => 'Problema',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem+' => '',
	'Class:MailInboxStandard/Attribute:debug_trace' => 'Rasto de depuração',
	'Class:MailInboxStandard/Attribute:debug_trace+' => '',
	'Class:MailInboxStandard/Attribute:target_folder' => 'Pasta de destino',
	'Class:MailInboxStandard/Attribute:target_folder+' => 'O e-mail será movido (protocolo IMAP) para esta pasta de destino após ser processado. Não se esqueça de atualizar a opção "Após processar o e-mail" para "Mover para outra pasta".',

	'Class:MailInboxStandard/Attribute:attcode_description' => 'Atributo de descrição',
	'Class:MailInboxStandard/Attribute:attcode_description+' => 'Código do atributo da classe de destino que deve receber a descrição inicial do ticket. Por defeito: "description" se deixado vazio.',
	'Class:MailInboxStandard/Attribute:attcode_caselog' => 'Atributo do diário (case log)',
	'Class:MailInboxStandard/Attribute:attcode_caselog+' => 'Código do atributo da classe de destino (diário) que deve receber as novas entradas ao criar e/ou atualizar o ticket. Por defeito: "public_log" se deixado vazio ou inválido.',

	'Class:MailInboxStandard/Attribute:ticket_default_values' => 'Valores predefinidos para o novo ticket',
	'Class:MailInboxStandard/Attribute:ticket_default_title' => 'Título predefinido (se o assunto estiver vazio)',
	'Class:MailInboxStandard/Attribute:title_pattern+' => 'Padrão a procurar no assunto',
	'Class:MailInboxStandard/Attribute:title_pattern' => 'Padrão do título',
	'Class:MailInboxStandard/Attribute:title_pattern?' => 'Use sintaxe PCRE, incluindo delimitadores de início e fim, para especificar o aspeto da referência do ticket (padrão), para que os e-mails possam ser associados a tickets.',

	'Class:MailInboxStandard/Attribute:title_pattern_ignore_patterns' => 'Ignorar padrões no assunto (padrões de expressão regular, um por linha)',

	'Class:MailInboxStandard/Attribute:stimuli' => 'Estímulos a aplicar',
	'Class:MailInboxStandard/Attribute:stimuli+' => 'Aplicar um estímulo quando o ticket estiver num determinado estado',
	'Class:MailInboxStandard/Attribute:stimuli?' => 'Uma lista de codigo_estado:codigo_estimulo (um por linha) para definir o estímulo a aplicar (apenas após atualizar um ticket existente), para o estado indicado do ticket. Isto é útil, por exemplo, para reatribuir automaticamente um ticket que esteja no estado "pendente". Use o formato <codigo_estado>:<codigo_estimulo>',


	'Class:MailInboxStandard/Attribute:trace' => 'Rasto de depuração',
	'Class:MailInboxStandard/Attribute:trace/Value:yes' => 'Sim',
	'Class:MailInboxStandard/Attribute:trace/Value:no' => 'Não',

	'Class:MailInboxStandard/Attribute:debug_log' => 'Registo de depuração',

	'Class:MailInboxStandard/Attribute:error_behavior' => 'Comportamento quando ocorre um erro durante o processamento',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:delete' => 'Eliminar a mensagem da caixa de correio',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:mark_as_error' => 'Marcar como erro',
	'Class:MailInboxStandard/Attribute:notify_errors_to' => 'Contactos a notificar em caso de erro',
	'Class:MailInboxStandard/Attribute:notify_errors_to+' => 'Consulta OQL que devolve a(s) Pessoa(s) (ex. "SELECT Person WHERE email = \'admin@example.com\'") para quem os e-mails em erro serão reencaminhados.',
	'Class:MailInboxStandard/Attribute:notify_from' => 'Endereço de remetente',

	'Class:MailInboxStandard/Attribute:mail_aliases' => 'Aliases de e-mail',
	'Class:MailInboxStandard/Attribute:mail_aliases+' => 'Aliases de e-mail: um por linha. São permitidos padrões de expressão regular.',

	'Class:MailInboxStandard/Attribute:authentication_results_authserv_id' => 'ID do servidor Authentication-Results de confiança',
	'Class:MailInboxStandard/Attribute:authentication_results_authserv_id+' => 'Nome de anfitrião (authserv-id) do servidor de correio que realmente autentica o correio recebido para esta caixa de correio (ex. "mx.google.com"). Se definido, as verificações SPF/DKIM (usadas para confiar no endereço do remetente) apenas consideram o cabeçalho "Authentication-Results" adicionado por este servidor, ignorando qualquer outra ocorrência, que de outra forma poderia ser falsificada pelo remetente. Deixe vazio para confiar na primeira ocorrência encontrada (comportamento anterior).',

	'Class:MailInboxStandard/Attribute:oauth_provider' => 'Fornecedor OAuth',
	'Class:MailInboxStandard/Attribute:oauth_provider+' => '',
	'Class:MailInboxStandard/Attribute:oauth_client_id' => 'Cliente OAuth',
	'Class:MailInboxStandard/Attribute:oauth_client_id+' => '',

	// Política: Critérios de anexos
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width' => 'Largura mín. (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width+' => 'Largura mínima da imagem (px). Deve ser pelo menos 1. As imagens demasiado pequenas não serão processadas.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width' => 'Largura máx. (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width+' => 'Largura máxima da imagem (px). Defina como 0 para aceitar qualquer largura. Se a extensão php-gd estiver instalada, as imagens maiores serão redimensionadas. Caso contrário, não serão processadas.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height' => 'Altura mín. (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height+' => 'Altura mínima (px). Deve ser pelo menos 1.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height' => 'Altura máx. (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height+' => 'Altura máxima (px). Defina como 0 para aceitar qualquer altura. Se a extensão php-gd estiver instalada, as imagens maiores serão redimensionadas. Caso contrário, não serão processadas.',

	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes' => 'Excluir tipos MIME',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes+' => 'Os anexos destes tipos MIME não serão processados. Especifique um por linha.',

	// Política: verificação DKIM
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior' => 'Comportamento em caso de infração',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:bounce_delete' => 'Rejeitar para o remetente e eliminar',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:bounce_mark_as_undesired' => 'Rejeitar para o remetente e marcar como indesejado',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:delete' => 'Eliminar a mensagem da caixa de correio',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:do_nothing' => 'Não fazer nada',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:inactive' => 'Inativo',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:mark_as_undesired' => 'Marcar como indesejado / Manter o e-mail temporariamente',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_subject' => 'Assunto da rejeição',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_notification' => 'Mensagem de rejeição',

	// Política: e-mail demasiado grande
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior' => 'Comportamento em caso de infração',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_delete' => 'Rejeitar para o remetente e eliminar',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_mark_as_undesired' => 'Rejeitar para o remetente e marcar como indesejado',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:delete' => 'Eliminar a mensagem da caixa de correio',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:do_nothing' => 'Não fazer nada',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:inactive' => 'Inativo',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:mark_as_undesired' => 'Marcar como indesejado / Manter o e-mail temporariamente',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_subject' => 'Assunto da rejeição',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_notification' => 'Mensagem de rejeição',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB' => 'Tamanho máx. (MB)',
	'Error:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB+' => 'Tamanho máximo do e-mail e dos seus anexos. E-mails maiores não serão processados. Defina como 0 para desativar.',

	// Política: anexo - tipo MIME proibido
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior' => 'Comportamento em caso de infração',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:fallback_ignore_forbidden_attachments' => 'Alternativa: ignorar anexos proibidos',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_delete' => 'Rejeitar para o remetente e eliminar',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_mark_as_undesired' => 'Rejeitar para o remetente e marcar como indesejado',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:delete' => 'Eliminar a mensagem da caixa de correio',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:do_nothing' => 'Não fazer nada',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:inactive' => 'Inativo',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:mark_as_undesired' => 'Marcar como indesejado / Manter o e-mail temporariamente',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_subject' => 'Assunto da rejeição',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_notification' => 'Mensagem de rejeição',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_mimetypes' => 'Tipos MIME (um por linha)',

	// Política: sem assunto
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior' => 'Comportamento em caso de infração',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:fallback_default_subject' => 'Alternativa: usar assunto predefinido',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_delete' => 'Rejeitar para o remetente e eliminar',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_mark_as_undesired' => 'Rejeitar para o remetente e marcar como indesejado',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:delete' => 'Eliminar a mensagem da caixa de correio',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:do_nothing' => 'Não fazer nada',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:inactive' => 'Inativo',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:mark_as_undesired' => 'Marcar como indesejado / Manter o e-mail temporariamente',
	'Class:MailInboxStandard/Attribute:policy_no_subject_subject' => 'Assunto da rejeição',
	'Class:MailInboxStandard/Attribute:policy_no_subject_notification' => 'Mensagem de rejeição',
	'Class:MailInboxStandard/Attribute:policy_no_subject_default_value' => 'Assunto predefinido',

	// Política: remetente desconhecido
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior' => 'Comportamento em caso de infração',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:fallback_create_person' => 'Alternativa: criar pessoa',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_delete' => 'Rejeitar para o remetente e eliminar',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_mark_as_undesired' => 'Rejeitar para o remetente e marcar como indesejado',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:delete' => 'Eliminar a mensagem da caixa de correio',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:mark_as_undesired' => 'Marcar como indesejado / Manter o e-mail temporariamente',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_subject' => 'Assunto da rejeição',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_notification' => 'Mensagem de rejeição',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_default_values' => 'Valores predefinidos para a nova pessoa (um por linha, exemplo: org_id:1)',

	// Política: outros destinatários
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior' => 'Comportamento em caso de infração',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_existing_other_contacts' => 'Alternativa: associar apenas contactos existentes',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_other_contacts' => 'Alternativa: associar sempre o contacto, criando-o se necessário',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_ignore_other_contacts' => 'Alternativa: ignorar todos os outros contactos',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_delete' => 'Rejeitar para o remetente e eliminar',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_mark_as_undesired' => 'Rejeitar para o remetente e marcar como indesejado',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:delete' => 'Eliminar a mensagem da caixa de correio',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:do_nothing' => 'Não fazer nada',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:inactive' => 'Inativo',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:mark_as_undesired' => 'Marcar como indesejado / Manter o e-mail temporariamente',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_subject' => 'Assunto da rejeição',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_notification' => 'Mensagem de rejeição',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_default_values' => 'Valores predefinidos para a nova pessoa (um por linha, exemplo: org_id:1)',

	// Política: ticket fechado
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior' => 'Comportamento em caso de infração',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior+' => 'Nota: por defeito, os tickets fechados não podem ser reabertos. Isto requer alterações ao modelo de dados.',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:fallback_reopen' => 'Alternativa: reabrir ticket',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_delete' => 'Rejeitar para o remetente e eliminar',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_mark_as_undesired' => 'Rejeitar para o remetente e marcar como indesejado',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:delete' => 'Eliminar a mensagem da caixa de correio',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:do_nothing' => 'Não fazer nada',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:inactive' => 'Inativo',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:mark_as_undesired' => 'Marcar como indesejado / Manter o e-mail temporariamente',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_subject' => 'Assunto da rejeição',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_notification' => 'Mensagem de rejeição',

	// Política: ticket resolvido
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior' => 'Comportamento em caso de infração',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:fallback_reopen' => 'Alternativa: reabrir ticket',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_delete' => 'Rejeitar para o remetente e eliminar',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_mark_as_undesired' => 'Rejeitar para o remetente e marcar como indesejado',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:delete' => 'Eliminar a mensagem da caixa de correio',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:do_nothing' => 'Não fazer nada',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:inactive' => 'Inativo',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:mark_as_undesired' => 'Marcar como indesejado / Manter o e-mail temporariamente',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_subject' => 'Assunto da rejeição',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_notification' => 'Mensagem de rejeição',

	// Política: ticket desconhecido
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior' => 'Comportamento em caso de infração',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_delete' => 'Rejeitar para o remetente e eliminar',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_mark_as_undesired' => 'Rejeitar para o remetente e marcar como indesejado',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:delete' => 'Eliminar a mensagem da caixa de correio',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:do_nothing' => 'Não fazer nada',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:inactive' => 'Inativo',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:mark_as_undesired' => 'Marcar como indesejado / Manter o e-mail temporariamente',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_subject' => 'Assunto da rejeição',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_notification' => 'Mensagem de rejeição',

	// Política: padrões de título indesejados
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior' => 'Comportamento em caso de infração',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:delete' => 'Eliminar a mensagem da caixa de correio',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_delete' => 'Rejeitar para o remetente e eliminar',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_mark_as_undesired' => 'Rejeitar para o remetente e marcar como indesejado',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:do_nothing' => 'Não fazer nada',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:inactive' => 'Inativo',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:mark_as_undesired' => 'Marcar como indesejado / Manter o e-mail temporariamente',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_subject' => 'Assunto da rejeição',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_notification' => 'Mensagem de rejeição',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_patterns' => 'Padrões indesejados no assunto (padrões de expressão regular, um por linha)',


	// Política: remover partes do título
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior' => 'Remover padrões do assunto',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:do_nothing' => 'Não fazer nada',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:inactive' => 'Inativo',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:fallback_remove' => 'Remover parte(s) do assunto',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_patterns' => 'Padrões a remover do assunto (padrões de expressão regular, um por linha)',

	// Política: o remetente deve ser o mesmo do ticket original
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior' => 'Comportamento em caso de infração',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:delete' => 'Eliminar a mensagem da caixa de correio',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_delete' => 'Rejeitar para o remetente e eliminar',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_mark_as_undesired' => 'Rejeitar para o remetente e marcar como indesejado',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:do_nothing' => 'Não fazer nada',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:inactive' => 'Inativo',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:mark_as_undesired' => 'Marcar como indesejado / Manter o e-mail temporariamente',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_subject' => 'Assunto da rejeição',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_notification' => 'Mensagem de rejeição',

	// Política: resposta automática
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior' => 'Comportamento em caso de infração',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:delete' => 'Eliminar a mensagem da caixa de correio',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:do_nothing' => 'Não fazer nada',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:inactive' => 'Inativo',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:mark_as_undesired' => 'Marcar como indesejado / Manter o e-mail temporariamente',

	// Política: relatório de não entrega
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior' => 'Comportamento em caso de infração',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:delete' => 'Eliminar a mensagem da caixa de correio',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:do_nothing' => 'Não fazer nada',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:inactive' => 'Inativo',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:mark_as_undesired' => 'Marcar como indesejado',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive' => 'Marcar remetente como inativo',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive+' => 'O remetente será marcado como inativo se a falha de entrega do e-mail parecer permanente e houver elevada confiança de que o destinatário já não é alcançável através deste endereço de e-mail.',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:yes' => 'Sim',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:no' => 'Não',

	// Passo: atualizar atributos do remetente
	'Class:MailInboxStandard/Attribute:step_update_caller_attributes' => 'Atualizar atributos do remetente (um por linha, exemplo: status:active)',
	'Class:MailInboxStandard/Attribute:step_update_caller_attributes+' => 'Aplicado ao registo de Pessoa do remetente sempre que um e-mail processado com sucesso é associado a um contacto existente. Deixe vazio para não atualizar nada. Podem ser usados marcadores de e-mail (ex. $mail->caller_email$) nos valores.',

	// Política: endereço de e-mail do remetente
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior' => 'Comportamento em caso de infração',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_delete' => 'Rejeitar para o remetente e eliminar',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_mark_as_undesired' => 'Rejeitar para o remetente e marcar como indesejado',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:delete' => 'Eliminar a mensagem da caixa de correio',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:do_nothing' => 'Não fazer nada',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:inactive' => 'Inativo',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:mark_as_undesired' => 'Marcar como indesejado',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_subject' => 'Assunto da rejeição',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_notification' => 'Mensagem de rejeição',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns' => 'Padrões',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns+' => 'Qualquer e-mail cujo endereço de remetente corresponda a um dos padrões de expressão regular definidos (um por linha) será considerado uma infração.',



	// Cabeçalhos
	'MailInbox:Server' => 'Configuração da caixa de correio',
	'MailInbox:Behavior' => 'Comportamento com e-mails recebidos',
	'MailInbox:Errors' => 'E-mails em erro',
	'MailInbox:Settings' => 'Definições',

	// Mensagens de validação
	'MailInbox:Error:TargetFolderRequired' => 'A pasta de destino deve ser especificada para uma caixa de correio ativa.',
	'MailInbox:Error:CaseLogAttCodeRequired' => 'O código do atributo do diário deve ser um atributo válido da classe de destino \'%1$s\'.',
	'MailInbox:Error:DescriptionOrCaseLogAttCodeRequired' => 'O código do atributo de descrição ou o código do atributo do diário deve ser um atributo válido da classe de destino \'%1$s\'.',
	'MailInbox:Error:DescriptionAttCodeMustHaveMaxSize' => 'O atributo de descrição \'%1$s\' da classe de destino \'%2$s\' não tem um tamanho máximo e não pode ser usado para armazenar a descrição inicial do ticket.',

	// Passos
	'MailInbox:StepAttachmentCriteria' => 'Imagens de e-mail incorporadas',
	'MailInbox:PolicyDkimCheck' => 'Verificação DKIM',
	'MailInbox:PolicyMailSize' => 'Tamanho do e-mail',
	'MailInbox:PolicyAttachmentForbiddenMimeType' => 'Anexo - Tipo MIME proibido',
	'MailInbox:PolicyAttachmentIgnoredMimeType' => 'Anexo - Tipo MIME ignorado',
	'MailInbox:PolicyUndesiredPattern' => 'Padrões de título indesejados',
	'MailInbox:PolicyRemovePattern' => 'Remover padrões de título indesejados',
	'MailInbox:PolicyIgnorePattern' => 'Ignorar padrões de título indesejados',
	'MailInbox:PolicyResolvedTicket' => 'Tickets resolvidos',
	'MailInbox:PolicyClosedTicket' => 'Tickets fechados',
	'MailInbox:PolicyUnknownTicket' => 'Tickets desconhecidos',
	'MailInbox:PolicyNoSubject' => 'Sem assunto',
	'MailInbox:PolicyUnknownCaller' => 'Remetente desconhecido',
	'MailInbox:PolicyOtherRecipients' => 'Outros destinatários especificados em Para: ou CC:',
	'MailInbox:PolicyBounceOtherEmailCallerThanTicketCaller' => 'Limitar as respostas aceites ao endereço de e-mail do remetente original do ticket',
	'MailInbox:PolicyAutoReply' => 'Resposta automática',
	'MailInbox:PolicyNonDeliveryReport' => 'Relatórios de não entrega',
	'MailInbox:StepUpdateCallerAttributes' => 'Atualizar atributos do remetente',
	'MailInbox:PolicySenderEmailAddress' => 'Bloquear remetentes através de padrões de endereço de e-mail',

	// Mensagens de validação
	'MailInbox:Error:NotifyErrorsToMustTargetContact' => 'A consulta para selecionar %1$s deve visar a classe \'Contact\' (ou uma subclasse como \'Person\' ou \'Team\'), não \'%2$s\'.',

	'Menu:MailInboxes' => 'Caixas de correio de e-mail recebido',
	'Menu:MailInboxes+' => 'Configuração das caixas de correio a analisar em busca de e-mails recebidos',

	'MailInboxStandard:DebugTrace' => 'Rasto de depuração',
	'MailInboxStandard:DebugTraceNotActive' => 'Ative o rasto de depuração nesta caixa de correio para ver um registo detalhado do que acontece.',
	'MailInboxStandard:DebugTraceAccessDenied' => 'Não tem permissão para ver o rasto de depuração desta caixa de correio.',

	'MailPolicy:CreateOrUpdateTicket:NoDescriptionProvided' => 'Nenhuma descrição fornecida',

	// OAuth2
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Title' => 'Criar uma caixa de correio',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Description' => 'Criar uma nova caixa de correio para obter e-mails de um fornecedor de correio remoto usando esta ligação OAuth como método de autenticação',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:CreateNewMailbox' => 'Criar uma nova caixa de correio',
	'UI:OAuthEmailSynchro:Error:UnknownVendor' => 'O fornecedor OAuth %1$s não existe',

	// lnkEmailUidToTicket
	'Class:lnkEmailUidToTicket' => 'Associação UID de e-mail / Ticket',
	'Class:lnkEmailUidToTicket/Attribute:message_uid' => 'UID da mensagem',
	'Class:lnkEmailUidToTicket/Attribute:ticket_id' => 'ID do ticket',
	'Class:lnkEmailUidToTicket/Attribute:mailbox_id' => 'ID da caixa de correio',
	'Class:lnkEmailUidToTicket/UniquenessRule:unique_message_uid_and_mailbox_id_and_ticket_id' => 'A combinação de ID da caixa de correio, UID da mensagem e ID do ticket deve ser única.',


));

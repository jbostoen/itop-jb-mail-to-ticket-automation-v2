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

Dict::Add('ES ES', 'Spanish', 'Español', array(

	// Dictionary entries go here
	'Class:MailInboxStandard' => 'Buzón de correo IMAP',
	'Class:MailInboxStandard+' => 'Origen de los correos entrantes',
	'Class:MailInboxStandard/Attribute:behavior' => 'Comportamiento al procesar un correo',
	'Class:MailInboxStandard/Attribute:behavior/Value:create_only' => 'Crear nuevos tickets',
	'Class:MailInboxStandard/Attribute:behavior/Value:update_only' => 'Actualizar tickets existentes',
	'Class:MailInboxStandard/Attribute:behavior/Value:both' => 'Crear o actualizar tickets',

	'Class:MailInboxStandard/Attribute:email_storage' => 'Tras procesar el correo',
	'Class:MailInboxStandard/Attribute:email_storage+' => 'Acción a realizar tras procesar el correo. Para un mejor rendimiento: si desea archivar, se recomienda mover los correos procesados correctamente a otra carpeta.',
	'Class:MailInboxStandard/Attribute:email_storage/Value:keep' => 'Mantenerlo en la misma carpeta',
	'Class:MailInboxStandard/Attribute:email_storage/Value:delete' => 'Eliminarlo inmediatamente',
	'Class:MailInboxStandard/Attribute:email_storage/Value:move' => 'Moverlo a otra carpeta',

	'Class:MailInboxStandard/Attribute:target_class' => 'Clase de ticket',
	'Class:MailInboxStandard/Attribute:target_class/Value:Incident' => 'Incidencia',
	'Class:MailInboxStandard/Attribute:target_class/Value:UserRequest' => 'Solicitud de usuario',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change' => 'Cambio',
	'Class:MailInboxStandard/Attribute:target_class/Value:Change+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange' => 'Cambio de rutina',
	'Class:MailInboxStandard/Attribute:target_class/Value:RoutineChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange' => 'Cambio normal',
	'Class:MailInboxStandard/Attribute:target_class/Value:NormalChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange' => 'Cambio de emergencia',
	'Class:MailInboxStandard/Attribute:target_class/Value:EmergencyChange+' => '',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem' => 'Problema',
	'Class:MailInboxStandard/Attribute:target_class/Value:Problem+' => '',
	'Class:MailInboxStandard/Attribute:debug_trace' => 'Traza de depuración',
	'Class:MailInboxStandard/Attribute:debug_trace+' => '',
	'Class:MailInboxStandard/Attribute:target_folder' => 'Carpeta de destino',
	'Class:MailInboxStandard/Attribute:target_folder+' => 'El correo se moverá (protocolo IMAP) a esta carpeta de destino tras ser procesado. Recuerde actualizar la opción "Tras procesar el correo" a "Moverlo a otra carpeta".',

	'Class:MailInboxStandard/Attribute:attcode_description' => 'Atributo de descripción',
	'Class:MailInboxStandard/Attribute:attcode_description+' => 'Código del atributo de la clase destino que debe recibir la descripción inicial del ticket. Por defecto: "description" si se deja vacío.',
	'Class:MailInboxStandard/Attribute:attcode_caselog' => 'Atributo del diario (case log)',
	'Class:MailInboxStandard/Attribute:attcode_caselog+' => 'Código del atributo de la clase destino (diario) que debe recibir las nuevas entradas al crear y/o actualizar el ticket. Por defecto: "public_log" si se deja vacío o no es válido.',

	'Class:MailInboxStandard/Attribute:ticket_default_values' => 'Valores por defecto para el nuevo ticket',
	'Class:MailInboxStandard/Attribute:ticket_default_title' => 'Título por defecto (si el asunto está vacío)',
	'Class:MailInboxStandard/Attribute:title_pattern+' => 'Patrón a buscar en el asunto',
	'Class:MailInboxStandard/Attribute:title_pattern' => 'Patrón del título',
	'Class:MailInboxStandard/Attribute:title_pattern?' => 'Use sintaxis PCRE, incluyendo los delimitadores de inicio y fin, para especificar el aspecto de la referencia del ticket (patrón), de modo que los correos puedan vincularse a los tickets.',

	'Class:MailInboxStandard/Attribute:title_pattern_ignore_patterns' => 'Ignorar patrones en el asunto (patrones de expresión regular, uno por línea)',

	'Class:MailInboxStandard/Attribute:stimuli' => 'Estímulos a aplicar',
	'Class:MailInboxStandard/Attribute:stimuli+' => 'Aplicar un estímulo cuando el ticket esté en un estado determinado',
	'Class:MailInboxStandard/Attribute:stimuli?' => 'Una lista de codigo_estado:codigo_estimulo (uno por línea) para definir el estímulo a aplicar (solo tras actualizar un ticket existente), para el estado dado del ticket. Esto es útil, por ejemplo, para reasignar automáticamente un ticket que está en el estado "pendiente". Use el formato <codigo_estado>:<codigo_estimulo>',


	'Class:MailInboxStandard/Attribute:trace' => 'Traza de depuración',
	'Class:MailInboxStandard/Attribute:trace/Value:yes' => 'Sí',
	'Class:MailInboxStandard/Attribute:trace/Value:no' => 'No',

	'Class:MailInboxStandard/Attribute:debug_log' => 'Registro de depuración',

	'Class:MailInboxStandard/Attribute:error_behavior' => 'Comportamiento cuando ocurre un error durante el procesamiento',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:delete' => 'Eliminar el mensaje del buzón',
	'Class:MailInboxStandard/Attribute:error_behavior/Value:mark_as_error' => 'Marcar como error',
	'Class:MailInboxStandard/Attribute:notify_errors_to' => 'Contactos a notificar en caso de error',
	'Class:MailInboxStandard/Attribute:notify_errors_to+' => 'Consulta OQL que devuelve la(s) Persona(s) (ej. "SELECT Person WHERE email = \'admin@example.com\'") a quien se reenviarán los correos en error.',
	'Class:MailInboxStandard/Attribute:notify_from' => 'Dirección de remitente',

	'Class:MailInboxStandard/Attribute:mail_aliases' => 'Alias de correo',
	'Class:MailInboxStandard/Attribute:mail_aliases+' => 'Alias de correo: uno por línea. Se permiten patrones de expresión regular.',

	'Class:MailInboxStandard/Attribute:authentication_results_authserv_id' => 'ID del servidor Authentication-Results de confianza',
	'Class:MailInboxStandard/Attribute:authentication_results_authserv_id+' => 'Nombre de host (authserv-id) del servidor de correo que realmente autentica el correo entrante para este buzón (ej. "mx.google.com"). Si se especifica, las comprobaciones SPF/DKIM (usadas para confiar en la dirección del remitente) solo consideran la cabecera "Authentication-Results" añadida por este servidor, ignorando cualquier otra ocurrencia, que de lo contrario podría ser falsificada por el remitente. Deje vacío para confiar en la primera ocurrencia encontrada (comportamiento anterior).',

	'Class:MailInboxStandard/Attribute:oauth_provider' => 'Proveedor OAuth',
	'Class:MailInboxStandard/Attribute:oauth_provider+' => '',
	'Class:MailInboxStandard/Attribute:oauth_client_id' => 'Cliente OAuth',
	'Class:MailInboxStandard/Attribute:oauth_client_id+' => '',

	// Política: Criterios de adjuntos
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width' => 'Ancho mín. (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_width+' => 'Ancho mínimo de imagen (px). Debe ser al menos 1. Las imágenes demasiado pequeñas no se procesarán.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width' => 'Ancho máx. (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_width+' => 'Ancho máximo de imagen (px). Ponga 0 para aceptar cualquier ancho. Si la extensión php-gd está instalada, las imágenes más grandes se redimensionarán. En caso contrario, no se procesarán.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height' => 'Alto mín. (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_min_height+' => 'Alto mínimo (px). Debe ser al menos 1.',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height' => 'Alto máx. (px)',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_image_max_height+' => 'Alto máximo (px). Ponga 0 para aceptar cualquier alto. Si la extensión php-gd está instalada, las imágenes más grandes se redimensionarán. En caso contrario, no se procesarán.',

	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes' => 'Excluir tipos MIME',
	'Class:MailInboxStandard/Attribute:step_attachment_criteria_exclude_mimetypes+' => 'Los adjuntos de estos tipos MIME no se procesarán. Especifique uno por línea.',

	// Política: comprobación DKIM
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior' => 'Comportamiento en caso de infracción',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:bounce_delete' => 'Rechazar al remitente y eliminar',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:bounce_mark_as_undesired' => 'Rechazar al remitente y marcar como no deseado',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:delete' => 'Eliminar el mensaje del buzón',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:do_nothing' => 'No hacer nada',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:inactive' => 'Inactivo',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_behavior/Value:mark_as_undesired' => 'Marcar como no deseado / Mantener el correo temporalmente',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_subject' => 'Asunto del rechazo',
	'Class:MailInboxStandard/Attribute:policy_dkim_check_notification' => 'Mensaje de rechazo',

	// Política: correo demasiado grande
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior' => 'Comportamiento en caso de infracción',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_delete' => 'Rechazar al remitente y eliminar',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:bounce_mark_as_undesired' => 'Rechazar al remitente y marcar como no deseado',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:delete' => 'Eliminar el mensaje del buzón',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:do_nothing' => 'No hacer nada',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:inactive' => 'Inactivo',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_behavior/Value:mark_as_undesired' => 'Marcar como no deseado / Mantener el correo temporalmente',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_subject' => 'Asunto del rechazo',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_notification' => 'Mensaje de rechazo',
	'Class:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB' => 'Tamaño máx. (MB)',
	'Error:MailInboxStandard/Attribute:policy_mail_size_too_big_max_size_MB+' => 'Tamaño máximo del correo y sus adjuntos. Los correos más grandes no se procesarán. Ponga 0 para desactivar.',

	// Política: adjunto - tipo MIME prohibido
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior' => 'Comportamiento en caso de infracción',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:fallback_ignore_forbidden_attachments' => 'Alternativa: ignorar adjuntos prohibidos',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_delete' => 'Rechazar al remitente y eliminar',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:bounce_mark_as_undesired' => 'Rechazar al remitente y marcar como no deseado',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:delete' => 'Eliminar el mensaje del buzón',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:do_nothing' => 'No hacer nada',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:inactive' => 'Inactivo',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_behavior/Value:mark_as_undesired' => 'Marcar como no deseado / Mantener el correo temporalmente',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_subject' => 'Asunto del rechazo',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_notification' => 'Mensaje de rechazo',
	'Class:MailInboxStandard/Attribute:policy_attachment_forbidden_mimetype_mimetypes' => 'Tipos MIME (uno por línea)',

	// Política: sin asunto
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior' => 'Comportamiento en caso de infracción',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:fallback_default_subject' => 'Alternativa: usar asunto por defecto',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_delete' => 'Rechazar al remitente y eliminar',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:bounce_mark_as_undesired' => 'Rechazar al remitente y marcar como no deseado',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:delete' => 'Eliminar el mensaje del buzón',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:do_nothing' => 'No hacer nada',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:inactive' => 'Inactivo',
	'Class:MailInboxStandard/Attribute:policy_no_subject_behavior/Value:mark_as_undesired' => 'Marcar como no deseado / Mantener el correo temporalmente',
	'Class:MailInboxStandard/Attribute:policy_no_subject_subject' => 'Asunto del rechazo',
	'Class:MailInboxStandard/Attribute:policy_no_subject_notification' => 'Mensaje de rechazo',
	'Class:MailInboxStandard/Attribute:policy_no_subject_default_value' => 'Asunto por defecto',

	// Política: remitente desconocido
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior' => 'Comportamiento en caso de infracción',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:fallback_create_person' => 'Alternativa: crear persona',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_delete' => 'Rechazar al remitente y eliminar',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:bounce_mark_as_undesired' => 'Rechazar al remitente y marcar como no deseado',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:delete' => 'Eliminar el mensaje del buzón',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_behavior/Value:mark_as_undesired' => 'Marcar como no deseado / Mantener el correo temporalmente',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_subject' => 'Asunto del rechazo',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_notification' => 'Mensaje de rechazo',
	'Class:MailInboxStandard/Attribute:policy_unknown_caller_default_values' => 'Valores por defecto para la nueva persona (uno por línea, ejemplo: org_id:1)',

	// Política: otros destinatarios
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior' => 'Comportamiento en caso de infracción',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_existing_other_contacts' => 'Alternativa: vincular solo contactos existentes',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_add_other_contacts' => 'Alternativa: vincular siempre el contacto, creándolo si es necesario',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:fallback_ignore_other_contacts' => 'Alternativa: ignorar todos los demás contactos',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_delete' => 'Rechazar al remitente y eliminar',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:bounce_mark_as_undesired' => 'Rechazar al remitente y marcar como no deseado',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:delete' => 'Eliminar el mensaje del buzón',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:do_nothing' => 'No hacer nada',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:inactive' => 'Inactivo',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_behavior/Value:mark_as_undesired' => 'Marcar como no deseado / Mantener el correo temporalmente',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_subject' => 'Asunto del rechazo',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_notification' => 'Mensaje de rechazo',
	'Class:MailInboxStandard/Attribute:policy_other_recipients_default_values' => 'Valores por defecto para la nueva persona (uno por línea, ejemplo: org_id:1)',

	// Política: ticket cerrado
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior' => 'Comportamiento en caso de infracción',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior+' => 'Nota: por defecto, los tickets cerrados no se pueden reabrir. Esto requiere cambios en el modelo de datos.',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:fallback_reopen' => 'Alternativa: reabrir ticket',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_delete' => 'Rechazar al remitente y eliminar',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:bounce_mark_as_undesired' => 'Rechazar al remitente y marcar como no deseado',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:delete' => 'Eliminar el mensaje del buzón',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:do_nothing' => 'No hacer nada',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:inactive' => 'Inactivo',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_behavior/Value:mark_as_undesired' => 'Marcar como no deseado / Mantener el correo temporalmente',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_subject' => 'Asunto del rechazo',
	'Class:MailInboxStandard/Attribute:policy_ticket_closed_notification' => 'Mensaje de rechazo',

	// Política: ticket resuelto
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior' => 'Comportamiento en caso de infracción',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:fallback_reopen' => 'Alternativa: reabrir ticket',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_delete' => 'Rechazar al remitente y eliminar',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:bounce_mark_as_undesired' => 'Rechazar al remitente y marcar como no deseado',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:delete' => 'Eliminar el mensaje del buzón',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:do_nothing' => 'No hacer nada',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:inactive' => 'Inactivo',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_behavior/Value:mark_as_undesired' => 'Marcar como no deseado / Mantener el correo temporalmente',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_subject' => 'Asunto del rechazo',
	'Class:MailInboxStandard/Attribute:policy_ticket_resolved_notification' => 'Mensaje de rechazo',

	// Política: ticket desconocido
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior' => 'Comportamiento en caso de infracción',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_delete' => 'Rechazar al remitente y eliminar',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:bounce_mark_as_undesired' => 'Rechazar al remitente y marcar como no deseado',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:delete' => 'Eliminar el mensaje del buzón',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:do_nothing' => 'No hacer nada',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:inactive' => 'Inactivo',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_behavior/Value:mark_as_undesired' => 'Marcar como no deseado / Mantener el correo temporalmente',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_subject' => 'Asunto del rechazo',
	'Class:MailInboxStandard/Attribute:policy_ticket_unknown_notification' => 'Mensaje de rechazo',

	// Política: patrones de título no deseados
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior' => 'Comportamiento en caso de infracción',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:delete' => 'Eliminar el mensaje del buzón',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_delete' => 'Rechazar al remitente y eliminar',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:bounce_mark_as_undesired' => 'Rechazar al remitente y marcar como no deseado',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:do_nothing' => 'No hacer nada',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:inactive' => 'Inactivo',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_behavior/Value:mark_as_undesired' => 'Marcar como no deseado / Mantener el correo temporalmente',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_subject' => 'Asunto del rechazo',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_notification' => 'Mensaje de rechazo',
	'Class:MailInboxStandard/Attribute:policy_undesired_pattern_patterns' => 'Patrones no deseados en el asunto (patrones de expresión regular, uno por línea)',


	// Política: eliminar partes del título
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior' => 'Eliminar patrones del asunto',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:do_nothing' => 'No hacer nada',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:inactive' => 'Inactivo',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_behavior/Value:fallback_remove' => 'Eliminar parte(s) del asunto',
	'Class:MailInboxStandard/Attribute:policy_remove_pattern_patterns' => 'Patrones a eliminar del asunto (patrones de expresión regular, uno por línea)',

	// Política: el remitente debe coincidir con el del ticket original
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior' => 'Comportamiento en caso de infracción',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:delete' => 'Eliminar el mensaje del buzón',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_delete' => 'Rechazar al remitente y eliminar',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:bounce_mark_as_undesired' => 'Rechazar al remitente y marcar como no deseado',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:do_nothing' => 'No hacer nada',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:inactive' => 'Inactivo',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_behavior/Value:mark_as_undesired' => 'Marcar como no deseado / Mantener el correo temporalmente',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_subject' => 'Asunto del rechazo',
	'Class:MailInboxStandard/Attribute:policy_other_email_caller_than_ticket_caller_notification' => 'Mensaje de rechazo',

	// Política: respuesta automática
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior' => 'Comportamiento en caso de infracción',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:delete' => 'Eliminar el mensaje del buzón',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:do_nothing' => 'No hacer nada',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:inactive' => 'Inactivo',
	'Class:MailInboxStandard/Attribute:policy_autoreply_behavior/Value:mark_as_undesired' => 'Marcar como no deseado / Mantener el correo temporalmente',

	// Política: informe de no entrega
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior' => 'Comportamiento en caso de infracción',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:delete' => 'Eliminar el mensaje del buzón',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:do_nothing' => 'No hacer nada',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:inactive' => 'Inactivo',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_behavior/Value:mark_as_undesired' => 'Marcar como no deseado',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive' => 'Marcar remitente como inactivo',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive+' => 'El remitente se marcará como inactivo si el fallo de entrega del correo parece permanente y hay alta confianza en que el destinatario ya no es accesible a través de esta dirección de correo.',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:yes' => 'Sí',
	'Class:MailInboxStandard/Attribute:policy_non_delivery_report_mark_caller_as_inactive/Value:no' => 'No',

	// Paso: actualizar atributos del remitente
	'Class:MailInboxStandard/Attribute:step_update_caller_attributes' => 'Actualizar atributos del remitente (uno por línea, ejemplo: status:active)',
	'Class:MailInboxStandard/Attribute:step_update_caller_attributes+' => 'Se aplica al registro de Persona del remitente cada vez que un correo procesado correctamente se vincula a un contacto existente. Deje vacío para no actualizar nada. Se pueden usar marcadores de correo (ej. $mail->caller_email$) en los valores.',

	// Política: dirección de correo del remitente
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior' => 'Comportamiento en caso de infracción',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_delete' => 'Rechazar al remitente y eliminar',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:bounce_mark_as_undesired' => 'Rechazar al remitente y marcar como no deseado',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:delete' => 'Eliminar el mensaje del buzón',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:do_nothing' => 'No hacer nada',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:inactive' => 'Inactivo',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_behavior/Value:mark_as_undesired' => 'Marcar como no deseado',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_subject' => 'Asunto del rechazo',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_notification' => 'Mensaje de rechazo',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns' => 'Patrones',
	'Class:MailInboxStandard/Attribute:policy_sender_email_address_patterns+' => 'Cualquier correo cuya dirección de remitente coincida con uno de los patrones de expresión regular definidos (uno por línea) se considerará una infracción.',



	// Encabezados
	'MailInbox:Server' => 'Configuración del buzón',
	'MailInbox:Behavior' => 'Comportamiento con los correos entrantes',
	'MailInbox:Errors' => 'Correos en error',
	'MailInbox:Settings' => 'Ajustes',

	// Mensajes de validación
	'MailInbox:Error:TargetFolderRequired' => 'Debe especificarse la carpeta de destino para un buzón activo.',
	'MailInbox:Error:CaseLogAttCodeRequired' => 'El código del atributo del diario debe ser un atributo válido de la clase destino \'%1$s\'.',
	'MailInbox:Error:DescriptionOrCaseLogAttCodeRequired' => 'El código del atributo de descripción o el código del atributo del diario debe ser un atributo válido de la clase destino \'%1$s\'.',
	'MailInbox:Error:DescriptionAttCodeMustHaveMaxSize' => 'El atributo de descripción \'%1$s\' de la clase destino \'%2$s\' no tiene un tamaño máximo y no se puede usar para almacenar la descripción inicial del ticket.',

	// Pasos
	'MailInbox:StepAttachmentCriteria' => 'Imágenes de correo incrustadas',
	'MailInbox:PolicyDkimCheck' => 'Comprobación DKIM',
	'MailInbox:PolicyMailSize' => 'Tamaño del correo',
	'MailInbox:PolicyAttachmentForbiddenMimeType' => 'Adjunto - Tipo MIME prohibido',
	'MailInbox:PolicyAttachmentIgnoredMimeType' => 'Adjunto - Tipo MIME ignorado',
	'MailInbox:PolicyUndesiredPattern' => 'Patrones de título no deseados',
	'MailInbox:PolicyRemovePattern' => 'Eliminar patrones de título no deseados',
	'MailInbox:PolicyIgnorePattern' => 'Ignorar patrones de título no deseados',
	'MailInbox:PolicyResolvedTicket' => 'Tickets resueltos',
	'MailInbox:PolicyClosedTicket' => 'Tickets cerrados',
	'MailInbox:PolicyUnknownTicket' => 'Tickets desconocidos',
	'MailInbox:PolicyNoSubject' => 'Sin asunto',
	'MailInbox:PolicyUnknownCaller' => 'Remitente desconocido',
	'MailInbox:PolicyOtherRecipients' => 'Otros destinatarios especificados en Para: o CC:',
	'MailInbox:PolicyBounceOtherEmailCallerThanTicketCaller' => 'Limitar las respuestas aceptadas a la dirección de correo del remitente original del ticket',
	'MailInbox:PolicyAutoReply' => 'Respuesta automática',
	'MailInbox:PolicyNonDeliveryReport' => 'Informes de no entrega',
	'MailInbox:StepUpdateCallerAttributes' => 'Actualizar atributos del remitente',
	'MailInbox:PolicySenderEmailAddress' => 'Bloquear remitentes mediante patrones de dirección de correo',

	// Mensajes de validación
	'MailInbox:Error:NotifyErrorsToMustTargetContact' => 'La consulta para seleccionar %1$s debe apuntar a la clase \'Contact\' (o una subclase como \'Person\' o \'Team\'), no a \'%2$s\'.',

	'Menu:MailInboxes' => 'Buzones de correo entrante',
	'Menu:MailInboxes+' => 'Configuración de los buzones a explorar en busca de correos entrantes',

	'MailInboxStandard:DebugTrace' => 'Traza de depuración',
	'MailInboxStandard:DebugTraceNotActive' => 'Active la traza de depuración en este buzón para ver un registro detallado de lo que ocurre.',
	'MailInboxStandard:DebugTraceAccessDenied' => 'No tiene permiso para ver la traza de depuración de este buzón.',

	'MailPolicy:CreateOrUpdateTicket:NoDescriptionProvided' => 'No se ha proporcionado descripción',

	// OAuth2
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Title' => 'Crear un buzón',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:Description' => 'Crear un nuevo buzón para obtener correos de un proveedor de correo remoto usando esta conexión OAuth como método de autenticación',
	'UI:OAuthEmailSynchro:Wizard:ResultConf:Panel:CreateNewMailbox' => 'Crear un nuevo buzón',
	'UI:OAuthEmailSynchro:Error:UnknownVendor' => 'El proveedor OAuth %1$s no existe',

	// lnkEmailUidToTicket
	'Class:lnkEmailUidToTicket' => 'Vínculo UID de correo / Ticket',
	'Class:lnkEmailUidToTicket/Attribute:message_uid' => 'UID del mensaje',
	'Class:lnkEmailUidToTicket/Attribute:ticket_id' => 'ID del ticket',
	'Class:lnkEmailUidToTicket/Attribute:mailbox_id' => 'ID del buzón',
	'Class:lnkEmailUidToTicket/UniquenessRule:unique_message_uid_and_mailbox_id_and_ticket_id' => 'La combinación de ID de buzón, UID de mensaje e ID de ticket debe ser única.',


));

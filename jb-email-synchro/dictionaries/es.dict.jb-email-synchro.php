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
	'Class:MailInboxBase' => 'Buzón de correo',
	'Class:MailInboxBase+' => 'Origen de los correos entrantes',

	'Class:MailInboxBase/Attribute:server' => 'Servidor de correo',
	'Class:MailInboxBase/Attribute:server+' => 'La dirección IP o el nombre de host completo del servidor de correo',
	'Class:MailInboxBase/Attribute:mailbox' => 'Carpeta del buzón (para IMAP)',
	'Class:MailInboxBase/Attribute:mailbox+' => 'Nota: las carpetas IMAP distinguen mayúsculas y minúsculas. Especifique las subcarpetas usando barras: Elementos eliminados/Ejemplo. Si se omite, se explorará el buzón predeterminado (raíz)',
	'Class:MailInboxBase/Attribute:login' => 'Usuario',
	'Class:MailInboxBase/Attribute:login+' => 'El nombre de la cuenta de correo usada para conectarse al buzón',
	'Class:MailInboxBase/Attribute:password' => 'Contraseña',
	'Class:MailInboxBase/Attribute:protocol' => 'Protocolo',
	'Class:MailInboxBase/Attribute:protocol/Value:imap' => 'IMAP',
	'Class:MailInboxBase/Attribute:port' => 'Puerto',
	'Class:MailInboxBase/Attribute:port+' => 'Puertos por defecto: 143 para IMAP - 993 para IMAP seguro.',
	'Class:MailInboxBase/Attribute:active' => 'Activo',
	'Class:MailInboxBase/Attribute:active+' => 'Solo si está en "Sí", el buzón será consultado. En caso contrario, no lo será.',
	'Class:MailInboxBase/Attribute:active/Value:yes' => 'Sí',
	'Class:MailInboxBase/Attribute:active/Value:no' => 'No',
	'Class:MailInboxBase/Attribute:imap_options' => 'Opciones IMAP',
	'Class:MailInboxBase/Attribute:imap_options+' => 'Las opciones IMAP se pueden especificar línea por línea. Se procesan en ese orden.',
	'Class:MailInboxBase/Attribute:disable_authenticator' => 'Desactivar autenticador',
	'Class:MailInboxBase/Attribute:disable_authenticator+' => 'En la implementación heredada, es posible desactivar ciertos mecanismos de autenticación. En la mayoría de los casos, deje esto vacío.',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI' => 'GSSAPI',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI+' => 'GSSAPI (Kerberos)',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM+' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN' => 'PLAIN',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN+' => 'PLAIN',
	'Class:MailInboxBase/Attribute:folder_delimiter' => 'Delimitador de carpeta',
	'Class:MailInboxBase/Attribute:folder_delimiter+' => 'El carácter usado por el proveedor de correo para indicar subcarpetas. Normalmente "/" (Google, Microsoft) o "." (Hetzner).',

	'MailInbox:MailboxContent' => 'Contenido del buzón',
	'MailInbox:MailboxContent:ConfirmMessage' => '¿Está seguro?',
	'MailInbox:NoValidEmailsFound' => 'No se encontraron correos válidos en este buzón.',
	'MailInbox:Z_DisplayedThereAre_X_Msg_Y_NewInTheMailbox' => '%1$d correo(s) mostrado(s). Hay %2$d correo(s) en el buzón (%3$d sin procesar y %4$d ilegibles/corruptos).',
	'MailInbox:UnprocessableMessages' => 'Hay %1$s correo(s) que no se pueden listar debido a un problema técnico.',
	'MailInbox:MaxAllowedPacketTooSmall' => 'El parámetro MySQL max_allowed_packet en "my.ini" es demasiado pequeño: %1$s. El valor recomendado es al menos: %2$s',
	'MailInbox:Status' => 'Estado',
	'MailInbox:Subject' => 'Asunto',
	'MailInbox:From' => 'De',
	'MailInbox:Date' => 'Fecha',
	'MailInbox:RelatedTicket' => 'Ticket relacionado',
	'MailInbox:ErrorMessage' => 'Mensaje de error',
	'MailInbox:Status/Processed' => 'Ya procesado',
	'MailInbox:Status/New' => 'Nuevo',
	'MailInbox:Status/Error' => 'Error',
    'MailInbox:Status/Undesired' => 'No deseado',
	'MailInbox:Status/Ignored' => 'Ignorado',
	'MailInbox:ReplicaLink' => 'Réplica',
	'MailInbox:InternalId' => 'ID interno',
	'MailInbox:UID' => 'UID',

	'MailInbox:Login/ServerMustBeUnique' => 'La combinación de usuario (%1$s) y servidor (%2$s) ya está configurada para otro buzón de correo.',
	'MailInbox:Login/Server/MailboxMustBeUnique' => 'La combinación de usuario (%1$s), servidor (%2$s) y buzón (%3$s) ya está configurada para otro buzón de correo',
	'MailInbox:Display_X_eMailsStartingFrom_Y' => 'Mostrando %1$s correo(s), a partir de %2$s.',
	'MailInbox:WithSelectedDo' => 'Con los correos seleccionados: ',
	'MailInbox:ResetStatus' => 'Restablecer estado',
	'MailInbox:DeleteMessage' => 'Eliminar correo',
	'MailInbox:IgnoreMessage' => 'Ignorar correo',

	'MailInbox:MessageDetails' => 'Detalles del mensaje',
	'MailInbox:DownloadEml' => 'Descargar archivo .eml',
	'Class:TriggerOnMailUpdate' => 'Disparador (al actualizar por correo)',
	'Class:TriggerOnMailUpdate+' => 'Disparador activado cuando un ticket se actualiza al procesar un correo entrante',

	'MailInbox:EmptyMailbox' => 'Buzón vacío',

	'Class:EmailReplica' => 'Réplica de correo',
	'Class:EmailReplica/Attribute:ticket_id' => 'ID del ticket',
	'Class:EmailReplica/Attribute:uidl' => 'UIDL',
	'Class:EmailReplica/Attribute:mailbox_path' => 'Ruta del buzón',
	'Class:EmailReplica/Attribute:message_id' => 'ID del mensaje',
	'Class:EmailReplica/Attribute:message_text' => 'Texto del mensaje',
	'Class:EmailReplica/Attribute:references' => 'Referencias',
	'Class:EmailReplica/Attribute:thread_index' => 'Índice de hilo',
	'Class:EmailReplica/Attribute:message_date' => 'Fecha del mensaje',
	'Class:EmailReplica/Attribute:last_seen' => 'Visto por última vez',
	'Class:EmailReplica/Attribute:status' => 'Estado',
	'Class:EmailReplica/Attribute:status/Value:error' => 'Error',
	'Class:EmailReplica/Attribute:status/Value:ignored' => 'Ignorado',
	'Class:EmailReplica/Attribute:status/Value:ok' => 'OK',
	'Class:EmailReplica/Attribute:status/Value:undesired' => 'No deseado',
	'Class:EmailReplica/Attribute:error_message' => 'Mensaje de error',
	'Class:EmailReplica/Attribute:error_trace' => 'Traza de error',
	'Class:EmailReplica/Attribute:contents' => 'Contenido',
	'Class:EmailReplica/Attribute:mailbox_id' => 'ID del buzón',


));

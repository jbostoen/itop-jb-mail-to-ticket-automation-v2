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
	'Class:MailInboxBase' => 'Caixa de correio',
	'Class:MailInboxBase+' => 'Origem dos e-mails recebidos',

	'Class:MailInboxBase/Attribute:server' => 'Servidor de correio',
	'Class:MailInboxBase/Attribute:server+' => 'O endereço IP ou o nome de anfitrião completo do servidor de correio',
	'Class:MailInboxBase/Attribute:mailbox' => 'Pasta da caixa de correio (para IMAP)',
	'Class:MailInboxBase/Attribute:mailbox+' => 'Nota: as pastas IMAP diferenciam maiúsculas de minúsculas. Especifique subpastas usando barras: Itens eliminados/Exemplo. Se omitido, será analisada a caixa de correio predefinida (raiz)',
	'Class:MailInboxBase/Attribute:login' => 'Utilizador',
	'Class:MailInboxBase/Attribute:login+' => 'O nome da conta de correio usada para ligar à caixa de correio',
	'Class:MailInboxBase/Attribute:password' => 'Palavra-passe',
	'Class:MailInboxBase/Attribute:protocol' => 'Protocolo',
	'Class:MailInboxBase/Attribute:protocol/Value:imap' => 'IMAP',
	'Class:MailInboxBase/Attribute:port' => 'Porta',
	'Class:MailInboxBase/Attribute:port+' => 'Portas predefinidas: 143 para IMAP - 993 para IMAP seguro.',
	'Class:MailInboxBase/Attribute:active' => 'Ativo',
	'Class:MailInboxBase/Attribute:active+' => 'Só quando definido como "Sim" é que a caixa de correio será consultada. Caso contrário, não será consultada.',
	'Class:MailInboxBase/Attribute:active/Value:yes' => 'Sim',
	'Class:MailInboxBase/Attribute:active/Value:no' => 'Não',
	'Class:MailInboxBase/Attribute:imap_options' => 'Opções IMAP',
	'Class:MailInboxBase/Attribute:imap_options+' => 'As opções IMAP podem ser especificadas linha a linha. São processadas por essa ordem.',
	'Class:MailInboxBase/Attribute:disable_authenticator' => 'Desativar autenticador',
	'Class:MailInboxBase/Attribute:disable_authenticator+' => 'Na implementação legada, é possível desativar certos mecanismos de autenticação. Na maioria dos casos, deixe este campo vazio.',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI' => 'GSSAPI',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:GSSAPI+' => 'GSSAPI (Kerberos)',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:NTLM+' => 'NTLM',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN' => 'PLAIN',
	'Class:MailInboxBase/Attribute:disable_authenticator/Value:PLAIN+' => 'PLAIN',
	'Class:MailInboxBase/Attribute:folder_delimiter' => 'Delimitador de pasta',
	'Class:MailInboxBase/Attribute:folder_delimiter+' => 'O carácter usado pelo fornecedor de correio para indicar subpastas. Normalmente "/" (Google, Microsoft) ou "." (Hetzner).',

	'MailInbox:MailboxContent' => 'Conteúdo da caixa de correio',
	'MailInbox:MailboxContent:ConfirmMessage' => 'Tem a certeza?',
	'MailInbox:NoValidEmailsFound' => 'Não foram encontrados e-mails válidos nesta caixa de correio.',
	'MailInbox:Z_DisplayedThereAre_X_Msg_Y_NewInTheMailbox' => '%1$d e-mail(s) apresentado(s). Existem %2$d e-mail(s) na caixa de correio (%3$d por processar e %4$d ilegíveis/corrompidos).',
	'MailInbox:UnprocessableMessages' => 'Existem %1$s e-mail(s) que não podem ser listados devido a um problema técnico.',
	'MailInbox:MaxAllowedPacketTooSmall' => 'O parâmetro MySQL max_allowed_packet em "my.ini" é demasiado pequeno: %1$s. O valor recomendado é, pelo menos: %2$s',
	'MailInbox:Status' => 'Estado',
	'MailInbox:Subject' => 'Assunto',
	'MailInbox:From' => 'De',
	'MailInbox:Date' => 'Data',
	'MailInbox:RelatedTicket' => 'Ticket relacionado',
	'MailInbox:ErrorMessage' => 'Mensagem de erro',
	'MailInbox:Status/Processed' => 'Já processado',
	'MailInbox:Status/New' => 'Novo',
	'MailInbox:Status/Error' => 'Erro',
    'MailInbox:Status/Undesired' => 'Indesejado',
	'MailInbox:Status/Ignored' => 'Ignorado',
	'MailInbox:ReplicaLink' => 'Réplica',
	'MailInbox:InternalId' => 'ID interno',
	'MailInbox:UID' => 'UID',

	'MailInbox:Login/ServerMustBeUnique' => 'A combinação de utilizador (%1$s) e servidor (%2$s) já está configurada para outra caixa de correio.',
	'MailInbox:Login/Server/MailboxMustBeUnique' => 'A combinação de utilizador (%1$s), servidor (%2$s) e caixa de correio (%3$s) já está configurada para outra caixa de correio',
	'MailInbox:Display_X_eMailsStartingFrom_Y' => 'A apresentar %1$s e-mail(s), a partir de %2$s.',
	'MailInbox:WithSelectedDo' => 'Com os e-mails selecionados: ',
	'MailInbox:ResetStatus' => 'Repor estado',
	'MailInbox:DeleteMessage' => 'Eliminar e-mail',
	'MailInbox:IgnoreMessage' => 'Ignorar e-mail',

	'MailInbox:MessageDetails' => 'Detalhes da mensagem',
	'MailInbox:DownloadEml' => 'Transferir ficheiro .eml',
	'Class:TriggerOnMailUpdate' => 'Gatilho (ao atualizar por correio)',
	'Class:TriggerOnMailUpdate+' => 'Gatilho ativado quando um ticket é atualizado ao processar um e-mail recebido',

	'MailInbox:EmptyMailbox' => 'Caixa de correio vazia',

	'Class:EmailReplica' => 'Réplica de e-mail',
	'Class:EmailReplica/Attribute:ticket_id' => 'ID do ticket',
	'Class:EmailReplica/Attribute:uidl' => 'UIDL',
	'Class:EmailReplica/Attribute:mailbox_path' => 'Caminho da caixa de correio',
	'Class:EmailReplica/Attribute:message_id' => 'ID da mensagem',
	'Class:EmailReplica/Attribute:message_text' => 'Texto da mensagem',
	'Class:EmailReplica/Attribute:references' => 'Referências',
	'Class:EmailReplica/Attribute:thread_index' => 'Índice de conversa',
	'Class:EmailReplica/Attribute:message_date' => 'Data da mensagem',
	'Class:EmailReplica/Attribute:last_seen' => 'Visto pela última vez',
	'Class:EmailReplica/Attribute:status' => 'Estado',
	'Class:EmailReplica/Attribute:status/Value:error' => 'Erro',
	'Class:EmailReplica/Attribute:status/Value:ignored' => 'Ignorado',
	'Class:EmailReplica/Attribute:status/Value:ok' => 'OK',
	'Class:EmailReplica/Attribute:status/Value:undesired' => 'Indesejado',
	'Class:EmailReplica/Attribute:error_message' => 'Mensagem de erro',
	'Class:EmailReplica/Attribute:error_trace' => 'Rasto do erro',
	'Class:EmailReplica/Attribute:contents' => 'Conteúdo',
	'Class:EmailReplica/Attribute:mailbox_id' => 'ID da caixa de correio',


));

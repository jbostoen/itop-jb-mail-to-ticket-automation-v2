<?php
// Copyright (C) 2019 Combodo SARL
//
//   This program is free software; you can redistribute it and/or modify
//   it under the terms of the GNU Lesser General Public License as published by
//   the Free Software Foundation; version 3 of the License.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of the GNU General Public License
//   along with this program; if not, write to the Free Software
//   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
/**
 * @copyright   Copyright (c) 2016-2025 Combodo SARL
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

class EmailReplica extends DBObject {

	public static function Init() {

		$aParams = array(
			"category" => "requestmgmt,grant_by_profile",
			"key_type" => "autoincrement",
			"name_attcode" => "uidl",
			"state_attcode" => "",
			"reconc_keys" => array("uidl", "mailbox_id", "mailbox_path"),
			"db_table" => "email_replica",
			"db_key_field" => "id",
			"db_finalclass_field" => "",
			'indexes' => array(
				// Indexes for faster search
				array('uidl'),
				array('uidl', 'mailbox_path'),
				array('uidl', 'mailbox_path', 'id', 'last_seen'),
				array('mailbox_id'),
			),		
		);
		MetaModel::Init_Params($aParams);

		MetaModel::Init_AddAttribute(new AttributeInteger("ticket_id", array("allowed_values" => null, "sql" => "ticket_id", "default_value" => 0, "is_null_allowed" => true, "depends_on" => array())));
		MetaModel::Init_AddAttribute(new AttributeString("uidl", array("allowed_values" => null, "sql" => "uidl", "default_value" => null, "is_null_allowed" => true, "depends_on" => array())));
		MetaModel::Init_AddAttribute(new AttributeString("mailbox_path", array("allowed_values" => null, "sql" => "mailbox_path", "default_value" => null, "is_null_allowed" => true, "depends_on" => array())));
		MetaModel::Init_AddAttribute(new AttributeString("message_id", array("allowed_values"=>null, "sql"=>"message_id", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeText("message_text", array("allowed_values"=>null, "sql"=>"message_text", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeText("references", array("allowed_values"=>null, "sql"=>"references", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeText("thread_index", array("allowed_values"=>null, "sql"=>"thread_index", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeDateTime("message_date", array("allowed_values"=>null, "sql"=>"message_date", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeDateTime("last_seen", array("allowed_values"=>null, "sql"=>"last_seen", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeEnum("status", array("allowed_values"=>new ValueSetEnum('ok,error,undesired,ignored'), "sql"=>"status", "default_value"=>'ok', "is_null_allowed"=>false, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeText("error_message", array("allowed_values"=>null, "sql"=>"error_message", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
		MetaModel::Init_AddAttribute(new AttributeHTML("error_trace", array("allowed_values"=>null, "sql"=>'error_trace', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeBlob("contents", array("is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeExternalKey("mailbox_id", array("allowed_values" => null, "sql" => "mailbox_id", "targetclass" => "MailInboxBase", "default_value" => null, "is_null_allowed" => false, "on_target_delete"=>DEL_AUTO, "depends_on" => array())));

	}
	
	
	public static function MakeReferencesHeader($sInitialMessageId, $oObject) {
		$sReferences = '';
		if ($sInitialMessageId != '')
		{
			$sReferences .= $sInitialMessageId.' ';
		}
		$sReferences .= self::MakeMessageId($oObject);
		return $sReferences;
	}
	
	public static function MakeMessageId($oObject) {
		
		//  N°5216 Fix invalid message-id when sending notification using cron on system with a specific locale set (#15) The timestamp used was indeed locale dependent.
		$sMessageId = sprintf('<iTop_%s_%d_%F@%s.openitop.org>',
			get_class($oObject),
			$oObject->GetKey(),
			microtime(true /* get as float*/),
			MetaModel::GetConfig()->Get('session_name')
		);
		return $sMessageId;
	}
}

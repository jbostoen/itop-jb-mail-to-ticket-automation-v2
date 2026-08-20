<?php
/**
 * @copyright   Copyright (c) 2019-2026 Jeffrey Bostoen
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     3.2.260819
 */

/**
 * Class lnkMailProcessingStepToTrigger.
 * N:N link between a MailProcessingStep (a pre-created step identifier) and a TriggerOnMailProcessingEvent subscribed to it.
 */
class lnkMailProcessingStepToTrigger extends cmdbAbstractObject {

	/**
	 * @throws CoreException
	 */
	public static function Init() {

		$aParams = [
			'category' => 'grant_by_profile,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => '',
			'state_attcode' => '',
			'reconc_keys' => ['step_id', 'trigger_id'],
			'db_table' => 'mail_lnk_step_to_trigger',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'is_link' => true,
		];
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();

		MetaModel::Init_AddAttribute(new AttributeExternalKey('trigger_id', [
			'allowed_values' => null,
			'sql' => 'trigger_id',
			'targetclass' => 'TriggerOnMailProcessingEvent',
			'is_null_allowed' => false,
			'on_target_delete' => DEL_AUTO,
			'depends_on' => [],
		]));

		MetaModel::Init_AddAttribute(new AttributeExternalKey('step_id', [
			'allowed_values' => null,
			'sql' => 'step_id',
			'targetclass' => 'MailProcessingStep',
			'is_null_allowed' => false,
			'on_target_delete' => DEL_AUTO,
			'depends_on' => [],
		]));

		MetaModel::Init_AddAttribute(new AttributeExternalField('code', [
			'allowed_values' => null,
			'extkey_attcode' => 'step_id',
			'target_attcode' => 'code',
		]));

		// The step's implementing class (FQCN). This, not "code", is what FireMailProcessingEventTriggers() actually matches on.
		MetaModel::Init_AddAttribute(new AttributeExternalField('category', [
			'allowed_values' => null,
			'extkey_attcode' => 'step_id',
			'target_attcode' => 'category',
		]));

		MetaModel::Init_SetZListItems('details', ['trigger_id', 'step_id']);
		MetaModel::Init_SetZListItems('list', ['step_id', 'code', 'category']);

	}

}

<?php
/**
 * @copyright   Copyright (c) 2019-2026 Jeffrey Bostoen
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     3.2.260819
 */

/**
 * Class MailProcessingStep.
 * A pre-created, reference-data object representing one step in the mail processing pipeline.
 * These objects are what TriggerOnMailProcessingEvent subscribes to (see lnkMailProcessingStepToTrigger), instead of a free-text list of identifiers.
 * Pre-created by the module installer for every available step class.
 *
 * @details "code" (Step::GetXMLSettingsPrefix(), e.g. "policy_other_recipients") is NOT unique: multiple step classes can share
 * the same XML settings prefix (they gate on the same mailbox setting, but fire at different points in the pipeline).
 * "category" (the step's FQCN) is what actually identifies a specific step, and is therefore the unique/reconciliation key.
 */
class MailProcessingStep extends cmdbAbstractObject {

	/**
	 * @throws CoreException
	 */
	public static function Init() {

		$aParams = [
			'category' => 'grant_by_profile,searchable',
			'key_type' => 'autoincrement',
			'name_attcode' => 'code',
			'state_attcode' => '',
			'reconc_keys' => ['category'],
			'db_table' => 'mail_processing_step',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'uniqueness_rules' => [
				'category' => [
					'attributes' => ['category'],
					'filter' => '',
					'disabled' => false,
				],
			],
		];
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();

		// The step's XML settings prefix (Step::GetXMLSettingsPrefix()), e.g. "policy_no_subject". Not unique, see class docblock.
		MetaModel::Init_AddAttribute(new AttributeString('code', [
			'allowed_values' => null,
			'sql' => 'code',
			'default_value' => '',
			'is_null_allowed' => false,
			'depends_on' => [],
		]));

		// The step's implementing class (FQCN), e.g. "JeffreyBostoenExtensions\MailToTicket\Steps\OtherRecipients". The actual unique identifier.
		// AttributeText, not AttributeString: AttributeString is capped at 255 chars (VARCHAR(255)), which a deeply namespaced FQCN could exceed.
		MetaModel::Init_AddAttribute(new AttributeText('category', [
			'allowed_values' => null,
			'sql' => 'category',
			'default_value' => '',
			'is_null_allowed' => false,
			'depends_on' => [],
		]));

		MetaModel::Init_SetZListItems('details', ['code', 'category']);
		MetaModel::Init_SetZListItems('list', ['code', 'category']);
		MetaModel::Init_SetZListItems('standard_search', ['code', 'category']);

	}

}

<?php
/**
 * @copyright   Copyright (c) 2019-2026 Jeffrey Bostoen
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     3.2.260711
 */

/**
 * Class TriggerOnMailPolicy.
 * A trigger that is activated when an incoming e-mail violates a mailbox policy ("step"),
 * e.g. an undesired title pattern, a forbidden attachment mime type, an unknown caller, ...
 *
 * @details Unlike TriggerOnMailUpdate, this does NOT extend TriggerOnObject: a policy violation does not necessarily
 * relate to an existing Ticket (e.g. the message may be rejected before any Ticket is created or found).
 * This is purely additive to the existing per-step "bounce message" (behavior/subject/notification) settings on the
 * mailbox: those keep working as before, and are not migrated automatically.
 */
class TriggerOnMailPolicy extends Trigger {

	/**
	 * @throws CoreException
	 */
	public static function Init() {

		$aParams = [
			'category' => 'grant_by_profile,core/cmdb,application',
			'key_type' => 'autoincrement',
			'name_attcode' => 'description',
			'state_attcode' => '',
			'reconc_keys' => ['description'],
			'db_table' => 'priv_trigger_onmailpolicy',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		];
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();

		// The steps to subscribe to: one step identifier (Step::GetXMLSettingsPrefix()) per line, e.g. "policy_no_subject".
		MetaModel::Init_AddAttribute(new AttributeText('step_list', [
			'allowed_values' => null,
			'sql' => 'step_list',
			'default_value' => '',
			'is_null_allowed' => false,
			'depends_on' => [],
		]));

		// Whether the original e-mail should be attached (as .eml) to the context arguments passed to the linked actions.
		// Also exposes it as the 'mail->eml_base64' placeholder, e.g. for use in an ActionWebhook payload.
		MetaModel::Init_AddAttribute(new AttributeBoolean('include_original_message', [
			'allowed_values' => null,
			'sql' => 'include_original_message',
			'default_value' => false,
			'is_null_allowed' => false,
			'depends_on' => [],
		]));

		// Display lists
		MetaModel::Init_SetZListItems('details', ['description', 'step_list', 'include_original_message', 'subscription_policy', 'action_list']);
		MetaModel::Init_SetZListItems('list', ['description', 'step_list']);
		// Search criteria
		MetaModel::Init_SetZListItems('standard_search', ['description']);

	}

}

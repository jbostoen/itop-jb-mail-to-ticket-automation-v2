<?php
/**
 * @copyright   Copyright (c) 2019-2026 Jeffrey Bostoen
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     3.2.260711
 */

/**
 * Class TriggerOnMailProcessingEvent.
 * A trigger that is activated upon a step during the processing of an incoming e-mail,
 * e.g. an undesired title pattern, a forbidden attachment mime type, an unknown caller, ...
 * Triggers subscribe to steps (Step::GetXMLSettingsPrefix()), the parent class; a policy is just one kind of step.
 *
 * @details Unlike TriggerOnMailUpdate, this does NOT extend TriggerOnObject: a mail processing event does not necessarily
 * relate to an existing Ticket (e.g. the message may be rejected before any Ticket is created or found).
 * This is purely additive to the existing per-step "bounce message" (behavior/subject/notification) settings on the
 * mailbox: those keep working as before, and are not migrated automatically.
 */
class TriggerOnMailProcessingEvent extends Trigger {

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
			'db_table' => 'priv_trigger_onmailprocessingevent',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		];
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();

		// The steps (MailProcessingStep, e.g. "policy_no_subject") to subscribe to.
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect('steps_list', [
			'linked_class' => 'lnkMailProcessingStepToTrigger',
			'ext_key_to_me' => 'trigger_id',
			'ext_key_to_remote' => 'step_id',
			'count_min' => 0,
			'count_max' => 0,
			'duplicates' => false,
			'display_style' => '',
			'allowed_values' => null,
			'with_php_constraint' => false,
			'with_php_computation' => false,
			'depends_on' => [],
			'always_load_in_tables' => false,
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
		MetaModel::Init_SetZListItems('details', ['description', 'steps_list', 'include_original_message', 'subscription_policy', 'action_list']);
		MetaModel::Init_SetZListItems('list', ['description']);
		// Search criteria
		MetaModel::Init_SetZListItems('standard_search', ['description']);

	}

}

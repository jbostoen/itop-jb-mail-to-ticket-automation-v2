<?php
/**
 * @copyright   Copyright (c) 2019-2026 Jeffrey Bostoen
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     3.2.261023
 */

/**
 * Class TriggerMailToTicketProcessingOnEvent.
 * A trigger that is activated upon an event during the processing of incoming e-mails (see JeffreyBostoenExtensions\MailToTicket\eEvent),
 * e.g. an undesired title pattern, a forbidden attachment mime type, an unknown caller, the start or end of a cron task, ...
 *
 * @details Unlike TriggerOnMailUpdate, this does NOT extend TriggerOnObject: an event does not necessarily
 * relate to an existing Ticket (e.g. the message may be rejected before any Ticket is created or found).
 */
class TriggerMailToTicketProcessingOnEvent extends Trigger {

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
			'db_table' => 'priv_trigger_mailtoticketprocessingonevent',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
		];
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();

		// The events (MailProcessingEvent) to subscribe to.
		MetaModel::Init_AddAttribute(new AttributeLinkedSetIndirect('events_list', [
			'linked_class' => 'lnkMailProcessingEventToTrigger',
			'ext_key_to_me' => 'trigger_id',
			'ext_key_to_remote' => 'event_id',
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

		// Optional: only activate for events of this mailbox. Empty: all mailboxes (and events not related to a mailbox).
		MetaModel::Init_AddAttribute(new AttributeExternalKey('mailbox_id', [
			'targetclass' => 'MailInboxBase',
			'allowed_values' => null,
			'sql' => 'mailbox_id',
			'is_null_allowed' => true,
			'on_target_delete' => DEL_AUTO,
			'depends_on' => [],
		]));

		// Whether the original e-mail should be attached (as .eml) to the context arguments passed to the linked actions.
		// Also exposes it as the 'mail->eml_base64' placeholder, e.g. for use in an ActionWebhook payload.
		MetaModel::Init_AddAttribute(new AttributeEnum('include_original_message', [
			'allowed_values' => new ValueSetEnum('yes,no'),
			'sql' => 'include_original_message',
			'default_value' => 'no',
			'is_null_allowed' => false,
			'depends_on' => [],
		]));

		// Display lists
		MetaModel::Init_SetZListItems('details', ['description', 'mailbox_id', 'events_list', 'include_original_message', 'subscription_policy', 'action_list']);
		MetaModel::Init_SetZListItems('list', ['description', 'mailbox_id']);
		// Search criteria
		MetaModel::Init_SetZListItems('standard_search', ['description', 'mailbox_id']);

	}

}

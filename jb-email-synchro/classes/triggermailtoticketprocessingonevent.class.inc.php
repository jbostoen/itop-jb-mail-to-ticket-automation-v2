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

		// Only activate for events of the mailboxes this query returns (a query of MailInboxBase or a subclass).
		// The default query returns all mailboxes (and also covers events that are not related to a mailbox).
		MetaModel::Init_AddAttribute(new AttributeOQL('mailbox_oql', [
			'allowed_values' => null,
			'sql' => 'mailbox_oql',
			'default_value' => static::DEFAULT_MAILBOX_OQL,
			'is_null_allowed' => false,
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
		MetaModel::Init_SetZListItems('details', ['description', 'mailbox_oql', 'events_list', 'include_original_message', 'subscription_policy', 'action_list']);
		MetaModel::Init_SetZListItems('list', ['description', 'mailbox_oql']);
		// Search criteria
		MetaModel::Init_SetZListItems('standard_search', ['description', 'mailbox_oql']);

	}

	/**
	 * @var string DEFAULT_MAILBOX_OQL The query that returns all mailboxes.
	 */
	public const DEFAULT_MAILBOX_OQL = 'SELECT MailInboxBase';

	/**
	 * @var array<string, bool> $aAppliesToMailbox Results of AppliesToMailbox(), by query and mailbox.
	 */
	private static array $aAppliesToMailbox = [];

	/**
	 * Checks whether this trigger is meant for events of a mailbox: whether the mailbox is part of the result of the mailbox query.
	 *
	 * @param DBObject|null $oMailBox The mailbox being processed, if any.
	 *
	 * @return bool
	 */
	public function AppliesToMailbox(?DBObject $oMailBox) : bool {

		$sOQL = trim((string)$this->Get('mailbox_oql'));

		// - All mailboxes (and events that are not related to a mailbox).
		if($sOQL === '' || strcasecmp($sOQL, static::DEFAULT_MAILBOX_OQL) === 0) {
			return true;
		}

		if($oMailBox === null) {
			return false;
		}

		$sCacheKey = $sOQL.'|'.$oMailBox->GetKey();

		if(!isset(static::$aAppliesToMailbox[$sCacheKey])) {

			try {
				$oSearch = DBObjectSearch::FromOQL($sOQL);
				$oSearch->AddCondition('id', $oMailBox->GetKey());
				static::$aAppliesToMailbox[$sCacheKey] = (new DBObjectSet($oSearch))->Count() > 0;
			}
			catch(Exception $e) {
				IssueLog::Warning("TriggerMailToTicketProcessingOnEvent #{$this->GetKey()}: invalid mailbox query, trigger skipped: ".$e->getMessage());
				static::$aAppliesToMailbox[$sCacheKey] = false;
			}

		}

		return static::$aAppliesToMailbox[$sCacheKey];

	}

}

<?php
/**
 * @copyright   Copyright (c) 2019-2026 Jeffrey Bostoen
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version     3.2.260711
 */

/**
 * Class ActionEmailLiteralRecipients.
 * An ActionEmail that can additionally reach recipients that have no corresponding Person/Contact record in iTop
 * (e.g. an incoming e-mail's sender that a mail processing step did not identify or create as a Person).
 *
 * @details ActionEmail::FindRecipients() resolves 'to'/'cc'/'bcc' exclusively through a live DBObjectSearch: no
 * matter how the OQL is written, it can only ever return objects that already exist as rows in the database.
 * There is no way to feed it a bare address. 'to_address'/'cc_address'/'bcc_address' below reuse the same
 * placeholder-substitution mechanism ActionEmail already uses for 'from'/'reply_to' (MetaModel::ApplyParams()),
 * and are merged with whatever the existing OQL-based fields find, rather than replacing them.
 */
class ActionEmailLiteralRecipients extends ActionEmail {

	/**
	 * @throws CoreException
	 */
	public static function Init() {

		$aParams = [
			'category' => 'grant_by_profile,core/cmdb,application',
			'key_type' => 'autoincrement',
			'name_attcode' => 'name',
			'state_attcode' => '',
			'reconc_keys' => ['name'],
			'db_table' => 'priv_action_email_literal_recipients',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'style' => new ormStyle(null, null, null, null, null, '../images/icons/icons8-mailing.svg'),
		];
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();

		MetaModel::Init_AddAttribute(new AttributeString('to_address', ['allowed_values' => null, 'sql' => 'to_address', 'default_value' => '', 'is_null_allowed' => true, 'depends_on' => []]));
		MetaModel::Init_AddAttribute(new AttributeString('cc_address', ['allowed_values' => null, 'sql' => 'cc_address', 'default_value' => '', 'is_null_allowed' => true, 'depends_on' => []]));
		MetaModel::Init_AddAttribute(new AttributeString('bcc_address', ['allowed_values' => null, 'sql' => 'bcc_address', 'default_value' => '', 'is_null_allowed' => true, 'depends_on' => []]));

		// Display lists
		MetaModel::Init_SetZListItems('details', [
			'col:col1' => [
				'fieldset:ActionEmail:main' => [
					0 => 'name',
					1 => 'description',
					2 => 'status',
					3 => 'language',
					4 => 'html_template',
					5 => 'subject',
					6 => 'body',
				],
				'fieldset:ActionEmail:trigger' => [
					0 => 'trigger_list',
					1 => 'asynchronous',
				],
			],
			'col:col2' => [
				'fieldset:ActionEmail:recipients' => [
					0 => 'from',
					1 => 'from_label',
					2 => 'reply_to',
					3 => 'reply_to_label',
					4 => 'test_recipient',
					5 => 'ignore_notify',
					6 => 'to',
					7 => 'to_address',
					8 => 'cc',
					9 => 'cc_address',
					10 => 'bcc',
					11 => 'bcc_address',
				],
			],
		]);
		MetaModel::Init_SetZListItems('list', ['status', 'to', 'to_address', 'subject', 'language']);

	}

	/**
	 * @inheritDoc
	 *
	 * @details Merges ActionEmail's OQL-based lookup with a literal, placeholder-aware address string
	 * (attribute '{$sRecipAttCode}_address'), so this action can still reach a recipient with no
	 * corresponding object in the database.
	 */
	protected function FindRecipients($sRecipAttCode, $aArgs) {

		$sOQLRecipients = parent::FindRecipients($sRecipAttCode, $aArgs);

		$sTemplate = $this->Get($sRecipAttCode.'_address');
		if(utils::IsNullOrEmptyString($sTemplate)) {
			return $sOQLRecipients;
		}

		$sResolved = MetaModel::ApplyParams($sTemplate, $aArgs);

		$aRecipients = utils::IsNullOrEmptyString($sOQLRecipients) ? [] : explode(', ', $sOQLRecipients);
		$iRecipientCountBefore = count($aRecipients);

		foreach(explode(',', $sResolved) as $sAddress) {

			$sAddress = trim($sAddress);
			if($sAddress === '') {
				continue;
			}

			if(filter_var($sAddress, FILTER_VALIDATE_EMAIL) === false) {
				IssueLog::Warning("ActionEmailLiteralRecipients #{$this->GetKey()}: literal '$sRecipAttCode' address resolved to an invalid e-mail address (\"$sAddress\"), skipped.");
				continue;
			}

			$aRecipients[] = $sAddress;

		}

		$aRecipients = array_unique($aRecipients);
		$this->m_iRecipients += count($aRecipients) - $iRecipientCountBefore;

		return implode(', ', $aRecipients);

	}

}

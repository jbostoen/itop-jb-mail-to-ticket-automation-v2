<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260711
 */


namespace JeffreyBostoenExtensions\MailToTicket\Steps;

use JeffreyBostoenExtensions\MailToTicket\{
	ProcessingHelper
};

use Exception;

/**
 * Class UpdateCallerAttributes.
 * A step to update attributes on an already-known caller (e.g. reactivating a Person whose
 * status was previously set to inactive), once a new e-mail from that caller is processed.
 */
abstract class UpdateCallerAttributes extends Base {

	/**
	 * @inheritDoc
	 */
	public static int $iPrecedence = 111;

	/**
	 * @inheritDoc
	 */
	public static string $sXMLSettingsPrefix = 'step_update_caller';

	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {

		$oEmail = ProcessingHelper::GetMail();
		$oCaller = $oEmail->GetSender();

		if($oCaller === null) {
			static::Trace(". Step not relevant: no caller was determined for this e-mail.");
			return;
		}

		$oRawEmail = ProcessingHelper::GetRawMail();

		if(preg_match('/\b(spf|dkim)=fail\b/i', $oRawEmail->GetHeader('authentication-results'))) {
			static::Trace(".. Refusing to update Person::{$oCaller->GetKey()}'s attributes: the receiving mail server reported a failed SPF/DKIM check (Authentication-Results) for this message.");
			return;
		}

		$sAttributes = static::GetStepSetting('attributes');

		if(trim($sAttributes) === '') {
			static::Trace(". No caller attributes configured to update.");
			return;
		}

		$aValues = static::ParseAttributeValues($sAttributes);

		if($aValues === []) {
			static::Trace(". No valid 'attcode:value' lines found in the configured attributes.");
			return;
		}

		static::Trace('.. Updating Person::'.$oCaller->GetKey().' with: '.http_build_query($aValues));
		ProcessingHelper::InitObjectFromDefaultValues($oCaller, $aValues);

		try {
			$oCaller->DBUpdate();
		}
		catch(Exception $e) {
			static::Trace('.. Unable to update Person::'.$oCaller->GetKey().': '.$e->getMessage());
		}

	}

}

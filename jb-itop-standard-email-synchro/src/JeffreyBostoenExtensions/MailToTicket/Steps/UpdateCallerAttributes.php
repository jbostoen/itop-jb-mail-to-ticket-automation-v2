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
		$oCaller->DBUpdate();

	}

}

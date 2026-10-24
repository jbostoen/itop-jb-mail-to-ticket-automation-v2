<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.261023
 */


namespace JeffreyBostoenExtensions\MailToTicket\Steps;

use JeffreyBostoenExtensions\MailToTicket\{
	ProcessingHelper
};

use RawEmailMessage;


/**
 * Class SenderAuthentication.
 * A policy, running before any other step, to evaluate the SPF/DKIM result(s) (RFC 8601) reported by the
 * receiving mail server for this message exactly once.
 * Any configured behavior ends the processing of the message (see ProcessingHelper::ProcessSteps()), so later steps
 * never run for a message that failed authentication and do not evaluate the header themselves.
 * When this policy is inactive, no step distrusts the sender based on authentication results.
 */
abstract class SenderAuthentication extends Base {

	/**
	 * Authentication methods (RFC 8601) that can be configured as failures.
	 */
	private const METHODS = ['spf', 'dkim'];

	/**
	 * @var string[] $aDetectedStates The authentication results (e.g. "spf=softfail") found for the message that is being processed.
	 */
	private static array $aDetectedStates = [];

	/**
	 * @inheritDoc
	 */
	public static int $iPrecedence = -10;

	/**
	 * @inheritDoc
	 */
	public static string $sXMLSettingsPrefix = 'policy_sender_authentication';

	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {

		$oMailBox = ProcessingHelper::GetMailBox();

		/** @var RawEmailMessage $oRawEmail Raw e-mail message. */
		$oRawEmail = ProcessingHelper::GetRawMail();

		$sAuthservId = $oMailBox->Get('authentication_results_authserv_id');

		// - Each configured failure is "<method>_<state>", e.g. "spf_softfail" or "dkim_permerror".
		$aConfiguredFailures = $oMailBox->Get(static::GetXMLSettingsPrefix().'_failures')?->GetValues() ?? [];
		$aDetectedFailures = [];
		$aDetectedStates = [];
		static::$aDetectedStates = [];

		foreach(static::METHODS as $sMethod) {

			$aStates = $oRawEmail->GetAuthenticationStates($sMethod, $sAuthservId);

			foreach($aStates as $sState) {

				$aDetectedStates[] = "{$sMethod}={$sState}";

				if(in_array("{$sMethod}_{$sState}", $aConfiguredFailures, true)) {
					$aDetectedFailures[] = "{$sMethod}={$sState}";
				}

			}

		}

		if(count($aDetectedStates) === 0) {
			static::Trace('.. No SPF/DKIM result found for this message.');
			return;
		}

		static::$aDetectedStates = $aDetectedStates;
		static::Trace('.. Detected authentication result(s): %1$s', implode(', ', $aDetectedStates));

		if(count($aDetectedFailures) > 0) {
			static::Trace('.. Sender authentication failed (result(s): %1$s).', implode(', ', $aDetectedFailures));
			static::HandleViolation();
		}

	}

	/**
	 * @inheritDoc
	 */
	protected static function GetViolationContextArguments(string $sBehavior) : array {

		return array_merge(parent::GetViolationContextArguments($sBehavior), [
			'authentication_states' => implode(', ', static::$aDetectedStates),
		]);

	}

}

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

use RawEmailMessage;


/**
 * Class DkimVerification.
 * A policy, running before any other step, to double-check the DKIM result(s) (RFC 8601) reported by the
 * receiving mail server for this message, and act on messages that failed the check.
 */
abstract class DkimVerification extends Base {

	/**
	 * DKIM states (RFC 8601 §2.7.1) that indicate the message did not pass DKIM verification.
	 */
	private const BAD_STATES = ['fail', 'permerror'];

	/**
	 * @inheritDoc
	 */
	public static int $iPrecedence = -10;

	/**
	 * @inheritDoc
	 */
	public static string $sXMLSettingsPrefix = 'policy_dkim_check';

	/**
	 * @inheritDoc
	 */
	public static function Execute() : void {

		$oMailBox = ProcessingHelper::GetMailBox();

		/** @var RawEmailMessage $oRawEmail Raw e-mail message. */
		$oRawEmail = ProcessingHelper::GetRawMail();

		$aStates = $oRawEmail->GetDkimStates($oMailBox->Get('authentication_results_authserv_id'));

		if(count($aStates) === 0) {
			static::Trace('.. No DKIM result found for this message.');
			return;
		}

		static::Trace('.. Detected DKIM state(s): %1$s', implode(', ', $aStates));

		$aBadStates = array_intersect($aStates, static::BAD_STATES);

		if(count($aBadStates) > 0) {
			static::Trace('.. DKIM check failed (state(s): %1$s).', implode(', ', $aBadStates));
			static::HandleViolation();
		}

	}

}

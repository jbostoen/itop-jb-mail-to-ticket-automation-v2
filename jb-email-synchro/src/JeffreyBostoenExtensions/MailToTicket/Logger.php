<?php
/**
 * @copyright   Copyright (c) 2020-2026 Jeffrey Bostoen
 * @license     See license.md
 * @version     3.2.260711
 */

namespace JeffreyBostoenExtensions\MailToTicket;

// iTop.
use LogAPI;

/**
 * Class Logger. Dedicated logger for mail-to-ticket processing, writing to its own log file
 * independently of iTop's other log channels.
 */
class Logger extends LogAPI {

	const CHANNEL_DEFAULT = 'MailToTicket';
	const LEVEL_DEFAULT = self::LEVEL_TRACE;

	// - Untyped, deliberately: matches every other LogAPI subclass in core (IssueLog, ToolsLog,
	//   SetupLog, DeadLockLog, ExceptionLog all declare this the same way). LogAPI::Enable()
	//   assigns a FileLog *object* here (static::$m_oFileLog = new FileLog($sTargetFile)) - a
	//   typed declaration here is a type mismatch waiting to surface as soon as Enable() is
	//   actually called.
	protected static $m_oFileLog = null;

	/**
	 * @inheritDoc
	 */
	public static function Enable($sTargetFile = null) : void {

		if(empty($sTargetFile)) {
			$sTargetFile = APPROOT.'log/mailtoticket.log';
		}

		parent::Enable($sTargetFile);

	}

}

Logger::Enable();

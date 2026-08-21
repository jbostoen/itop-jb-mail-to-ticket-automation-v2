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

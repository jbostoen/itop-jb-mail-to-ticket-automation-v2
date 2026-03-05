<?php

namespace Combodo\iTop\Extension\EmailSynchro\Service;

use DirectoryTree\ImapEngine\Connection\Loggers\LoggerInterface;
use IssueLog;

class IMAPEmailLogger implements LoggerInterface {

	public const LOG_CHANNEL = 'IMAP';

	/**
	 * Log when a message is sent.
	 */
	public function sent(string $message): void {
		IssueLog::Debug("IMAP Sent: $message", static::LOG_CHANNEL);
	}

	/**
	 * Log when a message is received.
	 */
	public function received(string $message): void {

		IssueLog::Debug("IMAP Received: $message", static::LOG_CHANNEL);

	}

}

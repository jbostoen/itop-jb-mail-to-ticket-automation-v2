<?php

namespace Combodo\iTop\Extension\EmailSynchro\Service;

use DirectoryTree\ImapEngine\Connection\Loggers\LoggerInterface;
use IssueLog;
use MailInboxBase;

class IMAPEmailLogger implements LoggerInterface {

	public const LOG_CHANNEL = 'IMAP';

	/**
	 * @var MailInboxBase|null The mailbox currently being processed. Log lines are routed through its
	 * own Trace() method, which also feeds that mailbox's own "debug_trace" field, instead of only the
	 * generic application log. The IMAP engine instantiates this logger itself with no constructor
	 * arguments, so IMAPEmailSource hands the mailbox over via this static property.
	 */
	public static ?MailInboxBase $oCurrentMailbox = null;

	/**
	 * Log when a message is sent.
	 */
	public function sent(string $message): void {
		static::Log('IMAP Sent: '.static::RedactCredentials($message));
	}

	/**
	 * Log when a message is received.
	 */
	public function received(string $message): void {
		static::Log('IMAP Received: '.$message);
	}

	/**
	 * Routes a log line through the current mailbox's own Trace() method, if known; falls back to the
	 * generic application log otherwise (e.g. if no mailbox was registered before the engine connected).
	 *
	 * @param string $sMessage
	 * @return void
	 */
	private static function Log(string $sMessage) : void {

		if(static::$oCurrentMailbox !== null) {
			static::$oCurrentMailbox->Trace($sMessage);
		}
		else {
			IssueLog::Debug($sMessage, static::LOG_CHANNEL);
		}

	}

	/**
	 * Redacts the password argument from a raw "LOGIN" command before it is logged, so mailbox
	 * credentials are never written to iTop's log files when IMAP debug logging is enabled.
	 *
	 * @param string $sLine Raw protocol line, as sent to the IMAP server.
	 * @return string
	 */
	private static function RedactCredentials(string $sLine) : string {

		return preg_replace('/^(\S+\s+LOGIN\s+\S+\s+).+$/i', '$1"********"', $sLine);

	}

}

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
	 * @var bool Whether the next "sent" line is expected to itself be a credential: the raw base64
	 * SASL continuation line the client sends after an "AUTHENTICATE" command with no inline argument.
	 */
	private static bool $bNextSentLineIsCredential = false;

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
	 * Redacts credentials from a raw protocol line before it is logged, so mailbox passwords and
	 * OAuth access tokens are never written to iTop's log files when IMAP debug logging is enabled.
	 * Covers both plain "LOGIN user password" authentication and SASL "AUTHENTICATE" exchanges
	 * (e.g. XOAUTH2), whose credential can appear inline or as a separate continuation line.
	 *
	 * @param string $sLine Raw protocol line, as sent to the IMAP server.
	 * @return string
	 */
	private static function RedactCredentials(string $sLine) : string {

		// A previous "AUTHENTICATE" command had no inline argument; this line is the raw
		// (base64) credential the client sends once the server replies with a "+" continuation.
		if(static::$bNextSentLineIsCredential) {
			static::$bNextSentLineIsCredential = false;
			return '********';
		}

		// LOGIN command: redact the password argument.
		if(preg_match('/^\S+\s+LOGIN\s+\S+\s+/i', $sLine)) {
			return preg_replace('/^(\S+\s+LOGIN\s+\S+\s+).+$/i', '$1"********"', $sLine);
		}

		// AUTHENTICATE with an inline credential (SASL-IR), e.g. "a1 AUTHENTICATE XOAUTH2 <base64>".
		if(preg_match('/^(\S+\s+AUTHENTICATE\s+\S+\s+)\S.*$/i', $sLine, $aMatches)) {
			return $aMatches[1].'********';
		}

		// AUTHENTICATE with no inline argument: the credential follows as a separate line.
		if(preg_match('/^\S+\s+AUTHENTICATE\s+\S+\s*$/i', $sLine)) {
			static::$bNextSentLineIsCredential = true;
			return $sLine;
		}

		return $sLine;

	}

}

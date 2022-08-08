<?php

namespace Combodo\iTop\Extension\Service;


use IssueLog;
use Laminas\Mail\Storage;
use Laminas\Mail\Storage\Exception\ExceptionInterface;
use Laminas\Mail\Storage\Exception\InvalidArgumentException;
use Laminas\Mail\Storage\Exception\RuntimeException;
use Laminas\Mail\Storage\Imap;

class IMAPOAuthStorage extends Imap
{
	const LOG_CHANNEL = 'OAuth';

	public function __construct($params)
	{
		if (is_array($params)) {
			$params = (object)$params;
		}

		$this->has['flags'] = true;

		if ($params instanceof IMAPOAuthLogin) {
			$this->protocol = $params;
			try {
				$this->selectFolder('INBOX');
			}
			catch (ExceptionInterface $e) {
				throw new  RuntimeException('IMAPOAuthStorage cannot select INBOX, is this a valid transport?', 0, $e);
			}

			return;
		}

		if (!isset($params->user)) {
			throw new  InvalidArgumentException('IMAPOAuthStorage need at least user in params');
		}

		$host = isset($params->host) ? $params->host : 'localhost';
		$password = isset($params->password) ? $params->password : '';
		$port = isset($params->port) ? $params->port : null;
		$ssl = isset($params->ssl) ? $params->ssl : false;

		$this->protocol = new IMAPOAuthLogin($params->provider);

		$this->protocol->connect($host, $port, $ssl);
		if (!$this->protocol->login($params->user, $password)) {
			IssueLog::Error("Cannot login to IMAP OAuth for mailbox $host", static::LOG_CHANNEL);
			throw new  RuntimeException('cannot login, user or tokens');
		}
		$this->selectFolder(isset($params->folder) ? $params->folder : 'INBOX');
	}

	/**
	 * Remove a message from server.
	 *
	 * If you're doing that from a web environment you should be careful and
	 * use a unique id as parameter if possible to identify the message.
	 *
	 * @param  int $id number of message
	 * @throws RuntimeException
	 */
	public function removeMessage($id)
	{
		if (! $this->protocol->store([Storage::FLAG_DELETED], $id, null, '+')) {
			throw new RuntimeException('cannot set deleted flag');
		}
		// Postpone EXPUNGE until logout
		$this->protocol->SetHasDeletedMails(true);
	}

}
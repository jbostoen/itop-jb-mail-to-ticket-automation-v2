<?php

namespace Combodo\iTop\Extension\EmailSynchro\Service;

use Combodo\iTop\Extension\EmailSynchro\Helper\ProviderHelper;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

// iTop internals.
use IssueLog;

// iTop classes.
use MailInboxBase;

class IMAPOAuthEmailSource extends IMAPEmailSource {

	/** @inheritDoc */
	public const LOG_CHANNEL = IMAPOAuthEmailLogger::LOG_CHANNEL;

	/** @inheritDoc */
	public const LOG_DEBUG_CLASS = 'IMAPOAuthEmailSource';

	/** @inheritDoc */
	public const CONFIG_AUTHENTICATION = 'oauth';

	/** @inheritDoc */
	public const CONFIG_DEBUG_LOGGER = IMAPOAuthEmailLogger::class;

	/**
	 * @inheritDoc
	 */
	public function __construct(MailInboxBase $oMailbox) {

		$oProvider = ProviderHelper::GetProviderForIMAP($oMailbox);
		$this->sAccessToken = null;

		try {
			$this->sAccessToken = ProviderHelper::GetAccessTokenForProvider($oProvider);
		} catch (IdentityProviderException $e) {
			IssueLog::Error('Failed to get IMAP oAuth credentials for incoming mails for provider '.$oProvider::GetVendorName(), static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack' => $e->getTraceAsString(),
			]);
		}

		if (empty($this->sAccessToken)) {
			// Don't fall through to an IMAP connection attempt with no usable credential:
			// that would surface as a confusing low-level IMAP auth error rather than this
			// actual, already-logged OAuth failure.
			throw new IdentityProviderException('No OAuth token for IMAP for provider '.$oProvider::GetVendorName(), 255, []);
		}

		parent::__construct($oMailbox);

	}
	 
}

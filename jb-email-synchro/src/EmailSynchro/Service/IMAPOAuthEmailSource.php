<?php

namespace Combodo\iTop\Extension\EmailSynchro\Service;

use Combodo\iTop\Extension\EmailSynchro\Helper\ProviderHelper;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

// iTop internals.
use IssueLog;

// iTop classes.
use MailInboxBase;

// Generic.
use Throwable;

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

		$this->sAccessToken = null;
		$sVendorName = 'unknown';

		try {
			// GetProviderForIMAP() is inside this try too: a misconfigured/missing oauth_client_id
			// throws a CoreException (MetaModel::GetObject($bMustBeFound = true)), which otherwise
			// wouldn't be caught at all.
			$oProvider = ProviderHelper::GetProviderForIMAP($oMailbox);
			$sVendorName = $oProvider::GetVendorName();
			$this->sAccessToken = ProviderHelper::GetAccessTokenForProvider($oProvider);
		}
		catch(Throwable $e) {
			// Catch Throwable, not just IdentityProviderException: a stored token with no expiry
			// information throws a plain RuntimeException (AccessToken::hasExpired()), and a network
			// failure reaching the token endpoint throws a Guzzle transfer exception - neither extends
			// IdentityProviderException, so both would otherwise escape uncaught from here.
			IssueLog::Error('Failed to get IMAP oAuth credentials for incoming mails for provider '.$sVendorName, static::LOG_CHANNEL, [
				'exception.message' => $e->getMessage(),
				'exception.stack' => $e->getTraceAsString(),
			]);
		}

		if (empty($this->sAccessToken)) {
			// Don't fall through to an IMAP connection attempt with no usable credential:
			// that would surface as a confusing low-level IMAP auth error rather than this
			// actual, already-logged OAuth failure.
			throw new IdentityProviderException('No OAuth token for IMAP for provider '.$sVendorName, 255, []);
		}

		parent::__construct($oMailbox);

	}
	 
}

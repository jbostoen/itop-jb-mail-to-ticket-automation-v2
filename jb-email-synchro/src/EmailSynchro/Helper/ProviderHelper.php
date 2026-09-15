<?php
namespace Combodo\iTop\Extension\EmailSynchro\Helper;

use Combodo\iTop\Core\Authentication\Client\OAuth\OAuthClientProviderFactory;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use MetaModel;
use TypeError;

class ProviderHelper {


	public static function GetProviderForIMAP($oMailbox) {

		$oOAuthClient = MetaModel::GetObject('OAuthClient', $oMailbox->Get('oauth_client_id'));
		return OAuthClientProviderFactory::GetClientProvider($oOAuthClient);

	}


	/**
	 * @param $oMailbox
	 *
	 * @return string
	 *
	 * @details Reads the vendor name straight from the OAuthClient's 'provider' attribute
	 * rather than through OAuthClientProviderAbstract::GetVendorName(), which always
	 * returns an empty string: it reads self::$sVendorName, and PHP resolves that to the
	 * declaring (abstract) class's own redeclared property, never the calling subclass's.
	 */
	public static function GetVendorNameForIMAP($oMailbox): string {

		$oOAuthClient = MetaModel::GetObject('OAuthClient', $oMailbox->Get('oauth_client_id'));
		return (string)$oOAuthClient->Get('provider');

	}


	/**
	 * @param $oProvider
	 *
	 * @return string
	 * @throws IdentityProviderException
	 */
	public static function GetAccessTokenForProvider($oProvider): string {

		try {
			$oAccessToken = $oProvider->GetAccessToken();
		}
		catch(TypeError $e) {
			// OAuthClientProviderAbstract::GetAccessToken() is declared to return a non-nullable
			// AccessToken, but the underlying OAuthClient::GetAccessToken() actually returns null
			// when the OAuthClient's own 'status' isn't 'active' (i.e. no prior authentication) -
			// triggering PHP's return-type enforcement as a TypeError rather than a falsy/empty
			// value. `empty($oProvider->GetAccessToken())` could therefore never detect this case.
			throw new IdentityProviderException('Not prior authentication to OAuth', 255, []);
		}

		// Reuse the current token while it's still valid. Refreshing it on every call (this
		// runs on every mailbox poll, by default every 30 seconds) risks triggering the identity
		// provider's rate-limiting/abuse protection, which can invalidate the refresh token.
		if(!$oAccessToken->hasExpired()) {
			return $oAccessToken->getToken();
		}

		$oProvider->SetAccessToken($oProvider->GetVendorProvider()->getAccessToken('refresh_token', [
			'refresh_token' => $oProvider->GetAccessToken()->getRefreshToken(),
			'scope' => $oProvider->GetScope()
		]));

		return $oProvider->GetAccessToken()->getToken();
	}

}

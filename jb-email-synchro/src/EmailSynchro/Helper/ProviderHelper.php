<?php
namespace Combodo\iTop\Extension\EmailSynchro\Helper;

use Combodo\iTop\Core\Authentication\Client\OAuth\OAuthClientProviderFactory;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use MetaModel;

class ProviderHelper {


	public static function GetProviderForIMAP($oMailbox) {

		$oOAuthClient = MetaModel::GetObject('OAuthClient', $oMailbox->Get('oauth_client_id'));
		return OAuthClientProviderFactory::GetClientProvider($oOAuthClient);

	}


	/**
	 * @param $oProvider
	 *
	 * @return string
	 * @throws IdentityProviderException
	 */
	public static function GetAccessTokenForProvider($oProvider): string {

		if(empty($oProvider->GetAccessToken())) {
			throw new IdentityProviderException('Not prior authentication to OAuth', 255, []);
		}

		// Reuse the current token while it's still valid. Refreshing it on every call (this
		// runs on every mailbox poll, by default every 30 seconds) risks triggering the identity
		// provider's rate-limiting/abuse protection, which can invalidate the refresh token.
		if(!$oProvider->GetAccessToken()->hasExpired()) {
			return $oProvider->GetAccessToken()->getToken();
		}

		$oProvider->SetAccessToken($oProvider->GetVendorProvider()->getAccessToken('refresh_token', [
			'refresh_token' => $oProvider->GetAccessToken()->getRefreshToken(),
			'scope' => $oProvider->GetScope()
		]));

		return $oProvider->GetAccessToken()->getToken();
	}

}

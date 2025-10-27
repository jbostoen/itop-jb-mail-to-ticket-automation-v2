<?php
namespace Combodo\iTop\Extension\Helper;

use Combodo\iTop\Core\Authentication\Client\OAuth\OAuthClientProviderFactory;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use MetaModel;

class ProviderHelper{


	public static function getProviderForIMAP($oMailbox) {

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

		$oProvider->SetAccessToken($oProvider->GetVendorProvider()->getAccessToken('refresh_token', [
			'refresh_token' => $oProvider->GetAccessToken()->getRefreshToken(),
			'scope' => $oProvider->GetScope()
		]));

		return $oProvider->GetAccessToken()->getToken();
	}

}

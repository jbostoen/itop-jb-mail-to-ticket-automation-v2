<?php
namespace Combodo\iTop\Extension\Helper;

use Combodo\iTop\Core\Authentication\Client\OAuth\OAuthClientProviderFactory;
use MetaModel;

class ProviderHelper{
	public static function getProviderForIMAP($oMailbox)
	{
		$oOAuthClient = MetaModel::GetObject('OAuthClient', $oMailbox->Get('oauth_client_id'));
		return OAuthClientProviderFactory::GetClientProvider($oOAuthClient);
	}
}
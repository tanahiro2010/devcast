<?php
namespace App\Futures\Auth\Callback;

use App\Libraries\GitHub;
use App\Config\Config;

class CallbackService {
  public static function exchangeToken(string $code, string $state): array {
    $oauthConfig = Config::oauth();
    $clientId = $oauthConfig['github']['client_id'];
    $clientSecret = $oauthConfig['github']['client_secret'];
    $redirectUri = $oauthConfig['github']['redirect_uri'];

    $github = new GitHub($clientId, $clientSecret, $redirectUri);
    $tokenData = $github->getAccessToken($code, $state);

    if (!isset($tokenData['access_token'])) {
      throw new \Exception("Access token not found in response");
    }


    return $tokenData;
  }
}

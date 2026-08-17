<?php
namespace App\Futures\Auth\Callback;

use App\Libraries\GitHub;
use App\Config\Config;

class CallbackService {
  private GitHub $github;

  public function __construct() {
    $oauthConfig = Config::oauth();
    $clientId = $oauthConfig['github']['client_id'];
    $clientSecret = $oauthConfig['github']['client_secret'];
    $redirectUri = $oauthConfig['github']['redirect_uri'];

    $this->github = new GitHub($clientId, $clientSecret, $redirectUri);
  }

  public function exchangeToken(string $code, string $state): array {
    $tokenData = $this->github->getAccessToken($code, $state);

    if (!isset($tokenData['access_token'])) {
      throw new \Exception("Access token not found in response");
    }

    return $tokenData;
  }

  public function getProfile(string $accessToken): array {
    return $this->github->getProfile($accessToken);
  }
}
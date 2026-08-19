<?php
namespace App\Futures\Auth\Callback;

use App\Libraries\GitHub;
use App\Config\Config;
use App\Models\DB\User;

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

  public function findOrCreateUser(string $provider, array $profile): User {
    $user = User::findByProviderId($provider, $profile['id']);
    
    if (!$user) {
      $user = User::create([
        'username' => $profile['login'],
        'email' => $profile['email'] ?? null,
        'name' => $profile['name'] ?? "unknown",
        'provider' => $provider,
        'provider_id' => $profile['id'],
        'avatar_url' => $profile['avatar_url'] ?? null,
      ]);
    }

    return $user;
  }
}
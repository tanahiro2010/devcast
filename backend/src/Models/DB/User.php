<?php
namespace App\Models\DB;
use App\Models\DB\BaseModel;
use App\Models\DB\Subscriptions;
use App\Models\DB\Credential;
use App\Models\DB\Session;
use App\Models\DB\ProviderToken;
use App\Libraries\Crypto;
use App\Libraries\Algorithm;
use App\Config\Config;

class User extends BaseModel {
  protected $table = 'users';
  protected $primaryKey = 'id';
  protected $fillable = ['id', 'provider', 'provider_id', 'username', 'name', 'email', 'avatar_url', 'created_at', 'updated_at'];

  public function __construct() {
    parent::__construct();
  }

  public static function findByProviderId(string $provider, string $providerId) {
    return self::firstWhere(['provider' => $provider, 'provider_id' => $providerId]);
  }

  public static function findById(int $id) {
    return self::firstWhere(['id' => $id]);
  }

  public static function findByUsername(string $username) {
    return self::firstWhere(['username' => $username]);
  }

  public function credentials(): array {
    return $this->hasMany(Credential::class, 'user_id');
  }

  public function createCredential(string $provider, string $accessToken, ?string $refreshToken, ?string $expiresAt, ?string $scope, string $tokenType) {
    $credential = Credential::create([
      'user_id'    => $this->id,
      'provider'   => $provider,
      'access_token'  => $accessToken,
      'refresh_token' => $refreshToken,
      'token_expires_at' => $expiresAt,
      'scope' =>      $scope,
      'token_type' => $tokenType,
    ]);

    return $credential;
  }

  /**
   * @return Session[] Returns an array of Session objects associated with the user
   */
  public function sessions(): array {
    return $this->hasMany(Session::class, 'user_id');
  }

  public function createSession(string $sessionId, string $ipAddress, string $userAgent, ?string $expiresAt) {
    $session = Session::create([
      'user_id'    => $this->id,
      'session_id' => $sessionId,
      'ip_address' => $ipAddress,
      'user_agent' => $userAgent,
      'expires_at' => $expiresAt,
    ]);

    return $session;
  }

  public function createAccessToken(string $ipAddress, string $userAgent): string {
    $session = $this->createSession(
      Crypto::generateRandomString(16),
      $ipAddress,
      $userAgent,
      date('Y-m-d H:i:s', strtotime('+7 days'))
    );

    return Crypto::jwtEncode([
      'sub' => $this->id,
      'iss' => $session->get('session_id'),
      'iat' => time(),
      'exp' => time() + 3600, // 1 hour expiration
    ], Config::env('JWT_SECRET'), Algorithm::HS256);
  }

  public function createRefreshToken(?string $token, ?\DateTime $expiresAt) {
    return RefreshToken::createToken($this->id, $token, $expiresAt);
  }

  public function createSubscription(string $priceId) {
    return Subscriptions::create([
      'user_id' => $this->id,
      'stripe_price_id' => $priceId,
      'status' => 'active',
    ]);
  }

  public function deleteAllSessions() {
    $sessions = $this->sessions();
    foreach ($sessions as $session) {
      $session->destroy();
    }
  }

  public function refresh(string $ipAddress, string $userAgent): string {
    $session = $this->createSession(
      Crypto::generateRandomString(16),
      $ipAddress,
      $userAgent,
      date('Y-m-d H:i:s', strtotime('+7 days'))
    );

    return Crypto::jwtEncode([
      'sub' => $this->id,
      'iss' => $session->get('session_id'),
      'iat' => time(),
      'exp' => time() + 3600, // 1 hour expiration
    ], Config::env('JWT_SECRET'), Algorithm::HS256);
  }

  public function refreshToken(): string {
    $refreshToken = $this->createRefreshToken(null, null);
    return $refreshToken->get('token');
  }

  public function subscriptions(): array {
    return $this->hasMany(Subscriptions::class, 'user_id');
  }

  public function activeSubscription(): ?Subscriptions {
    return Subscriptions::findActiveByUserId($this->id);
  }

  public function providers(): array {
    return $this->hasMany(ProviderToken::class, 'user_id');
  }

  public function registerProvider(string $provider, string $token, \DateTime $expiresAt): ProviderToken {
    return ProviderToken::create([
      'user_id' => $this->id,
      'provider' => $provider,
      'token' => $token,
      'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
    ]);
  }
}
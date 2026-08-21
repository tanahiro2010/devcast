<?php
namespace App\Models\DB;
use App\Models\DB\BaseModel;
use App\Models\DB\Credential;
use App\Models\DB\Session;

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

  public function credentials() {
    return Credential::where(['user_id' => $this->id]);
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
  public function sessions() {
    return Session::where(['user_id' => $this->id]);
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

  public function createRefreshToken(?string $token, ?\DateTime $expiresAt) {
    return RefreshToken::createToken($this->id, $token, $expiresAt);
  }

  public function deleteAllSessions() {
    $sessions = $this->sessions();
    foreach ($sessions as $session) {
      $session->destroy();
    }
  }
}
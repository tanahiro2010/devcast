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

  public static function findByProviderId($provider, $providerId) {
    return self::firstWhere(['provider' => $provider, 'provider_id' => $providerId]);
  }

  public static function findById($id) {
    return self::firstWhere(['id' => $id]);
  }

  public static function findByUsername($username) {
    return self::firstWhere(['username' => $username]);
  }

  public function credentials() {
    return Credential::where(['user_id' => $this->id]);
  }

  public function createCredential($provider, $accessToken, $refreshToken, $expiresAt) {
    $credential = Credential::create([
      'user_id' => $this->id,
      'provider' => $provider,
      'access_token' => $accessToken,
      'refresh_token' => $refreshToken,
      'token_expires_at' => $expiresAt,
    ]);

    return $credential;
  }

  public function sessions() {
    return Session::where(['user_id' => $this->id]);
  }

  public function createSession($sessionId, $ipAddress, $userAgent, $expiresAt) {
    $session = Session::create([
      'user_id' => $this->id,
      'session_id' => $sessionId,
      'ip_address' => $ipAddress,
      'user_agent' => $userAgent,
      'expires_at' => $expiresAt,
    ]);

    return $session;
  }
}
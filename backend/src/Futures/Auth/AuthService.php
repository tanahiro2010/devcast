<?php
namespace App\Futures\Auth;

use App\Config\Config;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthService {
  private const STATE_SESSION_KEY = 'oauth_state';
  private const STATE_EXPIRES_SESSION_KEY = 'oauth_state_expires_at';
  private const STATE_TTL_SECONDS = 600; // 10分

  public static function getOauthUrl() {
    $oauthConfig = Config::oauth();

    self::ensureSessionStarted();
    $state = bin2hex(random_bytes(16));
    $_SESSION[self::STATE_SESSION_KEY] = $state;
    $_SESSION[self::STATE_EXPIRES_SESSION_KEY] = time() + self::STATE_TTL_SECONDS;

    $queries = [
      "client_id" => $oauthConfig['github']['client_id'],
      "redirect_uri" => $oauthConfig['github']['redirect_uri'],
      "scope" => implode(" ", $oauthConfig['github']['scope']),
      "state" => $state
    ];

    return "https://github.com/login/oauth/authorize?" . http_build_query($queries);
  }

  /**
   * getOauthUrl() で発行した state を1回限り取り出す(検証後は必ず破棄する)。
   * 発行されていない/期限切れの場合は null を返す。
   */
  public static function consumeIssuedState(): ?string {
    self::ensureSessionStarted();

    $state = $_SESSION[self::STATE_SESSION_KEY] ?? null;
    $expiresAt = $_SESSION[self::STATE_EXPIRES_SESSION_KEY] ?? 0;
    unset($_SESSION[self::STATE_SESSION_KEY], $_SESSION[self::STATE_EXPIRES_SESSION_KEY]);

    if (!is_string($state) || time() > $expiresAt) {
      return null;
    }

    return $state;
  }

  private static function ensureSessionStarted(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
      return;
    }

    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
      'lifetime' => 0,
      'path' => '/',
      'secure' => $isHttps,
      'httponly' => true,
      'samesite' => 'Lax',
    ]);
    session_start();
  }
}

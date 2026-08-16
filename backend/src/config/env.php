<?php
namespace App\Config;

class Config {
  private static ?array $oauth = null;

  public static function env(string $name): string | false {
    $value = getenv($name);
    if ($value !== false) {
      return $value;
    }

    return $_SERVER[$name] ?? $_ENV[$name] ?? false;
  }

  public static function oauth(): array {
    if (self::$oauth === null) {
      self::$oauth = [
        'github' => [
          'client_id' => self::env('GITHUB_CLIENT_ID') ?: '',
          'client_secret' => self::env('GITHUB_CLIENT_SECRET') ?: '',
          'redirect_uri' => self::env('GITHUB_REDIRECT_URI') ?: 'http://localhost:8000/auth/callback',
          'front_redirect_uri' => self::server()['frontend']['base_url'] . '/_auth/callback',
          'scope' => ["read:user", "user:email"],
        ],
        'providers' => ['github', 'google', 'qiita'],
      ];
    }
    return self::$oauth;
  }

  public static function server(): array {
    return [
      'frontend' => [
        'base_url' => self::env('FRONTEND_URL') ?: 'http://localhost:5173',
      ],
      'backend' => [
        'base_url' => self::env('BACKEND_URL') ?: 'http://localhost:8000',
      ],
    ];
  }
}
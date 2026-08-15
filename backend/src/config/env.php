<?php
namespace App\Config;

class Config {
  private static ?array $oauth = null;

  public static function oauth(): array {
    if (self::$oauth === null) {
      self::$oauth = [
        'github' => [
          'client_id' => getenv('GITHUB_CLIENT_ID') ?? '',
          'client_secret' => getenv('GITHUB_CLIENT_SECRET') ?? '',
          'redirect_uri' => getenv('GITHUB_REDIRECT_URI') ?? '',
          'scope' => ["read:user", "user:email"],
        ],
        'providers' => ['github', 'google', 'qiita'],
      ];
    }
    return self::$oauth;
  }

  public static function frontend(): array {
    return [
      'base_url' => getenv('FRONTEND_URL') ?: 'http://localhost:5173',
    ];
  }
}
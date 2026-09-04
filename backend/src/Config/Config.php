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
        $config = self::server();
        if (self::$oauth === null) {
            self::$oauth = [
                'github' => [
                    'client_id' => self::env('GITHUB_CLIENT_ID') ?: '',
                    'client_secret' => self::env('GITHUB_CLIENT_SECRET') ?: '',
                    'redirect_uri' => $config['backend']['base_url'] . '/auth/callback',
                    'front_redirect_uri' => $config['frontend']['base_url'] . '/_auth/callback',
                    'scope' => ["read:user", "user:email"],
                ],
                'providers' => ['github'],
            ];
        }
        return self::$oauth;
    }

    public static function server(): array {
        return [
            'frontend' => [
                'base_url' => self::env('FRONTEND_URL') ?: 'http://localhost:5174',
            ],
            'backend' => [
                'base_url' => self::env('BACKEND_URL') ?: 'http://localhost:8000',
            ],
        ];
    }
}
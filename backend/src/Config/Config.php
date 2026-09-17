<?php
namespace App\Config;

class Config
{
    /** @var array{github: array{client_id: string, client_secret: string, redirect_uri: string, front_redirect_uri: string, scope: string[]}, providers: string[]}|null */
    private static ?array $oauth = null;

    public static function env(string $name): string | false
    {
        $value = getenv($name);
        if ($value !== false) {
            return $value;
        }

        return $_SERVER[$name] ?? $_ENV[$name] ?? false;
    }

    /**
     * @return array{github: array{client_id: string, client_secret: string, redirect_uri: string, front_redirect_uri: string, scope: string[]}, providers: string[]}
     */
    public static function oauth(): array
    {
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

    /**
     * @return array{host_url: string, bucket: string, region: string, client_id: string, client_secret: string}
     */
    public static function s3(): array
    {
        return [
            'host_url' => self::env('S3_HOST_URL') ?: '',
            'bucket' => self::env('S3_BUCKET') ?: '',
            'region' => self::env('S3_REGION') ?: 'us-east-1',
            'client_id' => self::env('S3_ACCESS_KEY_ID') ?: '',
            'client_secret' => self::env('S3_SECRET_ACCESS_KEY') ?: '',
        ];
    }

    /**
     * @return array{frontend: array{base_url: string}, backend: array{base_url: string}}
     */
    public static function server(): array
    {
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
<?php

namespace App\Models\DB;

use App\Models\DB\BaseModel;
use App\Models\DB\User;
use App\Config\Config;
use App\Shared\Crypto;

class ProviderToken extends BaseModel
{
    protected string $table = 'tokens';
    protected string $primaryKey = 'id';
    /** @var string[] */
    protected array $fillable = [
        'id',
        'user_id',
        'provider',
        'token',
        'expires_at',
        'created_at',
        'updated_at'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return static[]
     */
    public static function findByUserId(int $userId): array
    {
        return self::whereAll(['user_id' => $userId]);
    }

    public static function findByUserIdAndProvider(int $userId, string $provider): ?static
    {
        return self::firstWhere(['user_id' => $userId, 'provider' => $provider]);
    }

    public function exists(string $provider): bool
    {
        return self::firstWhere(['user_id' => $this->user_id, 'provider' => $provider]) !== null;
    }

    public function updateToken(string $token, \DateTime $expiresAt): void
    {
        $this->token = Crypto::encrypt($token, Config::env('CRYPTO_KEY'));
        $this->expires_at = $expiresAt->format('Y-m-d H:i:s');
        $this->save();
    }

    public function user(): ?User
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isTokenExpired(): bool
    {
        if ($this->expires_at === null) {
            return true; // If there's no expiration time, consider it expired
        }
        $currentTime = new \DateTime();
        $expirationTime = new \DateTime($this->expires_at);
        return $currentTime >= $expirationTime;
    }
}
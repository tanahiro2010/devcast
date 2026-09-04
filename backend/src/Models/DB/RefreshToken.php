<?php

namespace App\Models\DB;
use App\Models\DB\BaseModel;
use App\Models\DB\User;
use App\Libraries\Crypto;

class RefreshToken extends BaseModel
{
    protected $table = 'refresh_tokens';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'user_id', 'token', 'expires_at', 'created_at', 'updated_at'];

    public function __construct()
    {
        parent::__construct();
    }

    public static function createToken(string $userId, ?string $token, ?\DateTime $expiresAt)
    {
        if ($token === null) $token = Crypto::generateRandomString(64); // Generate a random token if not provided

        if ($expiresAt === null)
            $expiresAt = (new \DateTime())->modify('+30 days'); // Default expiration time of 30 days

        return self::create([
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);
    }

    public static function findByToken(string $token)
    {
        return self::firstWhere(['token' => $token]);
    }

    public function user(): ?User
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isTokenExpired()
    {
        if ($this->expires_at === null) {
            return true; // If there's no expiration time, consider it expired
        }
        $currentTime = new \DateTime();
        $expirationTime = new \DateTime($this->expires_at);
        return $currentTime >= $expirationTime;
    }

    /**
     * リフレッシュトークンを使用してアクセストークンを再発行すると同時に、
     * このリフレッシュトークン自体を無効化し新しいものに置き換える(ローテーション)。
     * 使用済みのリフレッシュトークンは再利用できなくなるため、漏洩時の被害を
     * 「発覚するまで無制限に使われ続ける」状態から「1回使われたら気付ける」状態にする。
     *
     * @return array{0: string, 1: string} [新しいアクセストークン(JWT), 新しいリフレッシュトークン文字列]
     */
    public function rotate(string $ipAddress, string $userAgent): array
    {
        $user = $this->user();
        if (!$user) {
            throw new \Exception("User not found for this refresh token");
        }

        $newAccessToken = $user->refresh($ipAddress, $userAgent);
        $newRefreshTokenValue = $user->refreshToken();
        $this->destroy();

        return [$newAccessToken, $newRefreshTokenValue];
    }
}
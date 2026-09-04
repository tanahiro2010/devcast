<?php
namespace App\Models\DB;
use App\Models\DB\BaseModel;
use App\Models\DB\User;

class Credential extends BaseModel
{
    protected string $table = 'credentials';
    protected string $primaryKey = 'id';
    /** @var string[] */
    protected array $fillable = ['id', 'provider', 'user_id', 'access_token', 'refresh_token', 'token_expires_at', 'scope', 'token_type', 'created_at', 'updated_at'];

    public function __construct()
    {
        parent::__construct();
    }

    public static function findByUserId(int $userId): ?static
    {
        return self::firstWhere(['user_id' => $userId]);
    }

    public function user(): ?User
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isTokenExpired(): bool
    {
        if ($this->token_expires_at === null) {
            return true; // If there's no expiration time, consider it expired
        }
        $currentTime = new \DateTime();
        $expirationTime = new \DateTime($this->token_expires_at);
        return $currentTime >= $expirationTime;
    }

    public function updateToken(string $accessToken, ?string $refreshToken, ?string $expiresAt): void
    {
        $this->update([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_expires_at' => $expiresAt,
        ]);
    }

    public function refreshAccessToken(): void
    {
        // Implement the logic to refresh the access token using the refresh token
        // This will depend on the OAuth provider's API
        // For example, you might make an HTTP request to the provider's token endpoint
        // and update the access token and expiration time accordingly.
        if ($this->properties['provider'] === 'github') {

        }
    }
}
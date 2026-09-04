<?php

namespace App\Models\DB;

use App\Models\DB\BaseModel;
use App\Models\DB\User;

class Session extends BaseModel
{
    protected string $table = 'sessions';
    protected string $primaryKey = 'id';
    /** @var string[] */
    protected array $fillable = ['id', 'user_id', 'session_id', 'ip_address', 'user_agent', 'is_logged_out', 'expires_at', 'created_at', 'updated_at'];

    public function __construct()
    {
        parent::__construct();
    }

    public static function findByUserId(int $userId): ?static
    {
        return self::firstWhere(['user_id' => $userId]);
    }

    public static function findBySessionId(string $sessionId): ?static
    {
        return self::firstWhere(['session_id' => $sessionId]);
    }

    public function user(): ?User
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isExpired(): bool
    {
        if ($this->expires_at === null) {
            return false; // No expiration set means the session never expires
        }
        $currentTime = new \DateTime();
        $expirationTime = new \DateTime($this->expires_at);
        return $currentTime >= $expirationTime;
    }

    public function markAsLoggedOut(): void
    {
        $this->is_logged_out = true;
        $this->save();
    }
}

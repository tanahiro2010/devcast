<?php
namespace App\Models\DB;

use App\Models\DB\BaseModel;
use App\Models\DB\User;

class Subscriptions extends BaseModel
{
    protected string $table = 'subscriptions';
    protected string $primaryKey = 'id';
    /** @var string[] */
    protected array $fillable = [
        'user_id',
        'stripe_subscription_id',
        'stripe_price_id',
        'status',
        'current_period_start',
        'current_period_end',
    ];

    public static function findActiveByUserId(string $userId): ?static
    {
        return self::firstWhere([
            'user_id' => $userId,
            'status' => 'active',
        ]);
    }

    public static function findByStripeSubscriptionId(string $stripeSubscriptionId): ?static
    {
        return self::firstWhere(['stripe_subscription_id' => $stripeSubscriptionId]);
    }

    public function user(): ?User
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

<?php
namespace App\Features\Auth\Profile;
use App\Models\DB\User;

class ProfileService
{
    /**
     * @return array{
     *     id: mixed,
     *     username: mixed,
     *     email: mixed,
     *     providers: array{id: mixed, provider: mixed, expires_at: mixed}[],
     *     created_at: mixed,
     *     updated_at: mixed,
     *     subscription: array{stripe_subscription_id: mixed, stripe_price_id: mixed, status: mixed, current_period_start: mixed, current_period_end: mixed}|null
     * }
     */
    static function getProfile(User $user): array
    {
        $profile = [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'providers' => $user->providers(),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];

        $subscription = $user->activeSubscription();
        if ($subscription) {
            $profile['subscription'] = [
                'stripe_subscription_id' => $subscription->stripe_subscription_id,
                'stripe_price_id' => $subscription->stripe_price_id,
                'status' => $subscription->status,
                'current_period_start' => $subscription->current_period_start,
                'current_period_end' => $subscription->current_period_end,
            ];
        } else {
            $profile['subscription'] = null;
        }

        return $profile;
    }
}
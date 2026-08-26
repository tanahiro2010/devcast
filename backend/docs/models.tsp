namespace DevCast.Models;

/**
 * Mirrors the `subscription` block built in
 * `App\Futures\Auth\Profile\ProfileService::getProfile`
 * (backend/src/Futures/Auth/Profile/ProfileService.php:18-24),
 * sourced from `App\Models\DB\Subscriptions`.
 */
model Subscription {
  stripe_subscription_id: string;
  stripe_price_id: string;
  status: string;
  current_period_start: string;
  current_period_end: string;
}

/**
 * A linked OAuth provider credential, as returned by `User::providers()`
 * (backend/src/Models/DB/User.php) and used by both
 * `GET /auth/profile` and `GET /v1/providers`.
 */
model Provider {
  id: int32;
  provider: string;
  expires_at: string;
}

/**
 * Mirrors the array built in
 * `App\Futures\Auth\Profile\ProfileService::getProfile`
 * (backend/src/Futures/Auth/Profile/ProfileService.php:7-29).
 */
model Profile {
  id: int32;
  username: string;
  email: string;
  providers: Provider[];
  created_at: string;
  updated_at: string;
  subscription: Subscription | null;
}

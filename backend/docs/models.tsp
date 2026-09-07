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

/**
 * Mirrors the `articles` table row as returned by `App\Models\DB\Article`
 * (backend/src/Models/DB/Article.php). `Article::toArray()` returns the raw
 * DB row, so every column is present as-is.
 */
model Article {
  id: int32;
  user_id: int32;
  title: string;

  /** Opaque random identifier generated on creation, not a human slug. */
  slug: string;

  body: string;

  /** JSON-encoded array of tag strings (stored as `jsonb`, not decoded before serialization). */
  tags: string;

  last_updated_at: string;
  created_at: string;
  updated_at: string;
}

/**
 * Mirrors `App\Futures\Version1\Articles\ArticlesService::getMetadata`
 * (backend/src/Futures/Version1/Articles/ArticlesService.php:26-50).
 */
model ArticlesMetadata {
  total_count: int32;
  published_count: int32;
  draft_count: int32;
  pending_count: int32;
}

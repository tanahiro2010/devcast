<?php
namespace App\Models\DB;
use App\Models\DB\BaseModel;
use App\Models\DB\Subscriptions;
use App\Models\DB\Credential;
use App\Models\DB\Session;
use App\Models\DB\ProviderToken;
use App\Libraries\Crypto;
use App\Libraries\Algorithm;
use App\Config\Config;

class User extends BaseModel
{
    protected string $table = 'users';
    protected string $primaryKey = 'id';
    /** @var string[] */
    protected array $fillable = ['id', 'provider', 'provider_id', 'username', 'name', 'email', 'avatar_url', 'created_at', 'updated_at'];

    public function __construct()
    {
        parent::__construct();
    }

    public static function findByProviderId(string $provider, string $providerId): ?static
    {
        return self::firstWhere(['provider' => $provider, 'provider_id' => $providerId]);
    }

    public static function findById(int $id): ?static
    {
        return self::firstWhere(['id' => $id]);
    }

    public static function findByUsername(string $username): ?static
    {
        return self::firstWhere(['username' => $username]);
    }

    public function credentials(): array
    {
        return $this->hasMany(Credential::class, 'user_id');
    }

    public function createCredential(string $provider, string $accessToken, ?string $refreshToken, ?string $expiresAt, ?string $scope, string $tokenType): Credential
    {
        $key = Config::env('CRYPTO_KEY');
        $credential = Credential::create([
            'user_id'    => $this->id,
            'provider'   => $provider,
            'access_token'  => Crypto::encrypt($accessToken, $key),
            'refresh_token' => $refreshToken !== null ? Crypto::encrypt($refreshToken, $key) : null,
            'token_expires_at' => $expiresAt,
            'scope' =>      $scope,
            'token_type' => $tokenType,
        ]);

        return $credential;
    }

    /**
     * @return Session[] Returns an array of Session objects associated with the user
     */
    public function sessions(): array
    {
        return $this->hasMany(Session::class, 'user_id');
    }

    public function createSession(string $sessionId, string $ipAddress, string $userAgent, ?string $expiresAt): Session
    {
        $session = Session::create([
            'user_id'    => $this->id,
            'session_id' => $sessionId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'expires_at' => $expiresAt,
        ]);

        return $session;
    }

    public function createAccessToken(string $ipAddress, string $userAgent): string
    {
        $session = $this->createSession(
            Crypto::generateRandomString(16),
            $ipAddress,
            $userAgent,
            date('Y-m-d H:i:s', strtotime('+7 days'))
        );

        return Crypto::jwtEncode([
            'sub' => $this->id,
            'iss' => $session->get('session_id'),
            'iat' => time(),
            'exp' => time() + 3600, // 1 hour expiration
        ], Config::env('JWT_SECRET'), Algorithm::HS256);
    }

    public function createRefreshToken(?string $token, ?\DateTime $expiresAt): RefreshToken
    {
        return RefreshToken::createToken($this->id, $token, $expiresAt);
    }

    public function createSubscription(string $priceId): Subscriptions
    {
        return Subscriptions::create([
            'user_id' => $this->id,
            'stripe_price_id' => $priceId,
            'status' => 'active',
        ]);
    }

    /**
     * @param array{title: string, body: string, tags: string[]} $data
     */
    public function createArticle(array $data): Article
    {
        return Article::createArticle($this, $data);
    }

    /**
     * @return Article[]
     */
    public function getArticles(): array
    {
        return Article::findByUserId($this['id']);
    }

    public function deleteAllSessions(): void
    {
        $sessions = $this->sessions();
        foreach ($sessions as $session) {
            $session->destroy();
        }
    }

    /**
     * @return RefreshToken[]
     */
    public function refreshTokens(): array
    {
        return $this->hasMany(RefreshToken::class, 'user_id');
    }

    public function deleteAllRefreshTokens(): void
    {
        foreach ($this->refreshTokens() as $refreshToken) {
            $refreshToken->destroy();
        }
    }

    public function refresh(string $ipAddress, string $userAgent): string
    {
        $session = $this->createSession(
            Crypto::generateRandomString(16),
            $ipAddress,
            $userAgent,
            date('Y-m-d H:i:s', strtotime('+7 days'))
        );

        return Crypto::jwtEncode([
            'sub' => $this->id,
            'iss' => $session->get('session_id'),
            'iat' => time(),
            'exp' => time() + 3600, // 1 hour expiration
        ], Config::env('JWT_SECRET'), Algorithm::HS256);
    }

    public function refreshToken(): string
    {
        $refreshToken = $this->createRefreshToken(null, null);
        return $refreshToken->get('token');
    }

    public function subscriptions(): array
    {
        return $this->hasMany(Subscriptions::class, 'user_id');
    }

    public function activeSubscription(): ?Subscriptions
    {
        return Subscriptions::findActiveByUserId($this->id);
    }

    /**
     * @return array{id: mixed, provider: mixed, expires_at: mixed}[]
     */
    public function providers(): array
    {
        $providers = $this->hasMany(ProviderToken::class, 'user_id');
        return array_map(function($provider) {
            return [
                'id' => $provider->id,
                'provider' => $provider->provider,
                'expires_at' => $provider->expires_at
            ];
        }, $providers);
    }

    public function provider(string $provider): ?ProviderToken
    {
        return ProviderToken::findByUserIdAndProvider($this->id, $provider);
    }

    public function registerProvider(string $provider, string $token, \DateTime $expiresAt): ProviderToken
    {
        return ProviderToken::create([
            'user_id' => $this->id,
            'provider' => $provider,
            'token' => Crypto::encrypt($token, Config::env('CRYPTO_KEY')),
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);
    }
}
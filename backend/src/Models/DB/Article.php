<?php

namespace App\Models\DB;

use App\Models\DB\BaseModel;
use App\Models\DB\User;
use App\Models\DB\ArticleRevision;

class Article extends BaseModel
{
    protected string $table = 'articles';
    protected string $primaryKey = 'id';
    /** @var string[] */
    protected array $fillable = ['id', 'user_id', 'title', 'slug', 'body', 'tags', 'created_at', 'updated_at'];

    /** @var array<string, string> Maps an include key to the attach method it triggers */
    protected array $includes = [
        'revisions_count' => 'attachRevisionsCount',
    ];

    /**
     * @param string[] $include
     */
    static function findBySlug(string $slug, array $include = []): ?Article
    {
        return self::firstWhere(['slug' => $slug], $include);
    }

    /**
     * @param string[] $include
     */
    static function findById(int $id, array $include = []): ?Article
    {
        return self::firstWhere(['id' => $id], $include);
    }

    /**
     * @param int $userId
     * @param string[] $include
     * @return Article[]
     */
    static function findByUserId(int $userId, array $include = []): array
    {
        return self::whereAll(['user_id' => $userId], $include);
    }

    private function attachRevisionsCount(): static
    {
        $this->properties['revisions_count'] = ArticleRevision::countByArticleId($this['id']);
        return $this;
    }

    /**
     * @param array{title: string, body: string, tags: string[]} $data
     * @return Article
     */
    static function createArticle(User $user, array $data): Article
    {
        $article = self::create([
            'user_id' => $user['id'],
            'title' => $data["title"],
            'slug'  => bin2hex(random_bytes(20)),
            'body'  => $data["body"],
            'tags'  => json_encode($data["tags"]),
        ]);

        $providers = $user->providers();
        foreach ($providers as $provider) {
            ArticleStatus::create([
                'article_id'    => $article['id'],
                'provider_id'   => $provider['provider'],
                'is_published'  => false,
                'is_synced'     => false,
            ]);
        }

        ArticleRevision::createRevision($article, $user, $data);

        return $article;
    }

    /**
     * @param array{title?: string, body?: string, tags?: string[]} $data
     * @return Article
     */
    public function updateArticle(User $user, array $data): Article
    {
        ArticleRevision::createRevision($this, $user, [
            'title' => $data['title'] ?? $this['title'],
            'body'  => $data['body'] ?? $this['body'],
            'tags'  => $data['tags'] ?? json_decode($this['tags'], true),
        ]);

        if (isset($data['tags'])) {
            $data['tags'] = json_encode($data['tags']);
        }

        return $this->update($data);
    }

    /**
     * @return ArticleRevision[]
     */
    public function revisions(): array
    {
        return $this->hasMany(ArticleRevision::class, 'article_id');
    }

    public function latestRevision(): ArticleRevision | null
    {
        return ArticleRevision::findLatestRevisionByArticleId($this['id']);
    }

    public function deleteArticle(): true
    {
        return $this->destroy();
    }

    /**
     * @return ArticleStatus[]
     */
    public function providers(): array
    {
        return $this->hasMany(ArticleStatus::class, 'article_id');
    }

    public function user(): ?User
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function toArray(): array
    {
        return $this->properties;
    }
}
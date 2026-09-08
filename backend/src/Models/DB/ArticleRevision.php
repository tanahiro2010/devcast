<?php

namespace App\Models\DB;

class ArticleRevision extends BaseModel
{
    protected string $table = 'article_revisions';
    protected string $primaryKey = 'id';
    /** @var string[] */
    protected array $fillable = ['id', 'article_id', 'user_id', 'version', 'title', 'body', 'tags', 'created_at'];

    /**
     * @return ArticleRevision[]
     */
    static function findByArticleId(int $articleId): array
    {
        return self::whereAll(['article_id' => $articleId]);
    }

    static function findByArticleIdAndVersion(int $articleId, int $version): ?ArticleRevision
    {
        return self::firstWhere(['article_id' => $articleId, 'version' => $version]);
    }

    static function findLatestRevisionByArticleId(int $articleId): ?ArticleRevision
    {
        $revisions = self::findByArticleId($articleId);
        if (empty($revisions)) return null;

        return $revisions[0];
    }

    static function findLatestVersion(int $articleId): int
    {
        $revisions = self::findByArticleId($articleId);
        if (empty($revisions)) {
            return 0;
        }

        return max(array_map(fn($revision) => (int) $revision['version'], $revisions));
    }

    /**
     * @param array{title: string, body: string, tags: string[]} $data
     */
    static function createRevision(Article $article, User $user, array $data): ArticleRevision
    {
        $nextVersion = self::findLatestVersion($article['id']) + 1;

        return self::create([
            'article_id' => $article['id'],
            'user_id'    => $user['id'],
            'version'    => $nextVersion,
            'title'      => $data['title'],
            'body'       => $data['body'],
            'tags'       => json_encode($data['tags']),
        ]);
    }

    public function article(): ?Article
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function user(): ?User
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

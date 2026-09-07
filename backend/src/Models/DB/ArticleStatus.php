<?php

namespace App\Models\DB;

class ArticleStatus extends BaseModel
{

    protected string $table = 'article_status';
    protected string $primaryKey = 'id';
    /** @var string[] */
    protected array $fillable = ['id', 'article_id', 'provider_id', 'is_published', 'is_synced', 'published_at', 'created_at', 'updated_at'];


    /**
     * @param int $articleId
     * @return ArticleStatus[]
     */
    static function findByArticleId(int $articleId): array
    {
        return self::whereAll(['article_id' => $articleId]);
    }

    /**
     * @param string $provider
     * @return ArticleStatus[]
     */
    static function findByProviderId(string $provider): array
    {
        return self::whereAll(['provider_id' => $provider]);
    }

    public function article(): ?Article
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function updateArticle(array $attributes): bool
    {
        $attributes['updated_at'] = date("Y-m-d H:i:s");
        return $this->fill($attributes)->save();
    }

    public function changePublishedStatus(): bool
    {
        $newStatus = !$this['is_published'];
        $data = [
            'is_published' => $newStatus,
            'published_at' => $newStatus ? date('Y-m-d H:i:s') : null,
        ];

        $this->updateArticle($data);

        return $newStatus;
    }
}
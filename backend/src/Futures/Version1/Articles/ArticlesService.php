<?php
namespace App\Futures\Version1\Articles;

use App\Models\DB\Article;
use App\Models\DB\User;

class ArticlesService
{
    function __construct()
    {

    }

    /**
     * @param string[] $include
     * @return Article[]
     */
    public function getArticles(User $user, array $include = []): array
    {
        return $user->getArticles($include);
    }

    /**
     * @param Article[] $articles
     * @return array{total_count: int, published_count: int, draft_count: int, pending_count: int}
     */
    public function getMetadata(array $articles): array
    {
        $publishedArticles = [];
        $pendingArticles   = [];

        foreach ($articles as $article) {
            $providers = $article->providers();
            foreach ($providers as $provider) {
                if ($provider['is_published']) {
                    $publishedArticles[] = $article;
                    if (!$provider['is_synced']) $pendingArticles[] = $article;
                }
            }
        }

        $totalCount = count($publishedArticles);
        $publishedCount = count($publishedArticles);

        return [
            'total_count' => $totalCount,
            'published_count' => $publishedCount,
            'draft_count' => $totalCount - $publishedCount,
            'pending_count' => count($pendingArticles),
        ];
    }


}
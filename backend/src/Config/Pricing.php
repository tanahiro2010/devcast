<?php
namespace App\Config;

use App\Models\Plan;

class Pricing
{
    private static ?array $plans = null;

    public static function plans(): array
    {
        if (self::$plans === null) {
            self::$plans = [
                new Plan('free', 'Free', 0, null, [
                    '記事の作成・管理（無制限）',
                    'Qiita / DEV.to / はてなブログ 全Publisher接続',
                    'Multi-Publish / Sync Detection',
                    'Public Content API',
                ], [
                    'apiKeys' => 1,
                    'articleHistory' => 'latest', // 直近リビジョンのみ保持
                    'aiTranslationsPerMonth' => 0,
                    'scheduledPublishing' => false,
                    'webhooks' => false,
                    'analytics' => false,
                    'teamWorkspace' => false,
                    'customDomain' => false,
                    'support' => 'community',
                ]),
                new Plan('pro', 'Pro', 980, 9800, [
                    '記事の作成・管理（無制限）',
                    'Qiita / DEV.to / はてなブログ 全Publisher接続',
                    'Multi-Publish / Sync Detection',
                    'Public / Private Content API',
                    'Article History（無制限履歴・差分表示）',
                    'AI Translation（月20回まで）',
                    'Scheduled Publishing',
                ], [
                    'apiKeys' => 5,
                    'articleHistory' => 'unlimited',
                    'aiTranslationsPerMonth' => 20,
                    'scheduledPublishing' => true,
                    'webhooks' => false,
                    'analytics' => 'basic',
                    'teamWorkspace' => false,
                    'customDomain' => false,
                    'support' => 'email',
                ]),
                new Plan('business', 'Business', 2980, 29800, [
                    '記事の作成・管理（無制限）',
                    'Qiita / DEV.to / はてなブログ 全Publisher接続',
                    'Multi-Publish / Sync Detection',
                    'Public / Private Content API（高レート制限）',
                    'Article History（無制限履歴・差分表示）',
                    'AI Translation（無制限・Cloud AI優先実行）',
                    'Scheduled Publishing',
                    'Webhooks',
                    'Analytics（媒体横断フル統合）',
                    'Team Workspace（Owner / Admin / Editor / Viewer）',
                    'Custom Domain',
                ], [
                    'apiKeys' => null, // 無制限
                    'articleHistory' => 'unlimited',
                    'aiTranslationsPerMonth' => null, // 無制限
                    'scheduledPublishing' => true,
                    'webhooks' => true,
                    'analytics' => 'full',
                    'teamWorkspace' => true,
                    'customDomain' => true,
                    'support' => 'priority',
                ]),
            ];
        }

        return self::$plans;
    }
}

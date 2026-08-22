# DevCast Backend

DevCastのContent API。記事の管理・複数プラットフォームへの配信ロジックを担うバックエンドです。

## スタック

- PHP 8.4
- [Slim Framework](https://www.slimframework.com/) 4
- [PHP-DI](https://php-di.org/)
- [Illuminate Database](https://github.com/illuminate/database)（Eloquent ORM）
- [tanahiro2010/slim-router-dsl](https://packagist.org/packages/tanahiro2010/slim-router-dsl)（ルーティングDSL）

## ディレクトリ構成

```text
backend
├── index.php              # エントリーポイント
├── scripts/
│   ├── migrate.php        # マイグレーション実行
│   └── make_migration.php # マイグレーションファイル生成
└── src/
    ├── Config/            # ルーティング・DB・料金プランなどの設定
    ├── Futures/            # 機能単位のController/Service（Auth, Health, Errorsなど）
    ├── Middleware/         # CORS・認証などのミドルウェア
    ├── Models/             # Eloquentモデル・レスポンス定義
    ├── Helpers/            # 共通ヘルパー
    ├── Libraries/          # ライブラリラッパー
    └── database/
        └── migrations/     # DBマイグレーション
```

`Futures`配下は機能（Auth, Health, Errorsなど）ごとにController/Serviceをまとめたディレクトリです。

## Setup

リポジトリルートの`.env`を使用します（詳細は[ルートREADME](../README.md)を参照）。

### Docker経由（推奨）

リポジトリルートから:

```bash
make back
```

### ローカル実行

```bash
cd backend
composer install
php -S 0.0.0.0:8000 -t . index.php
```

## マイグレーション

```bash
# 実行
make migrate

# 新規マイグレーション作成
make make-migration name=create_articles_table
```

（リポジトリルートから実行。内部的に`backend/scripts/migrate.php` / `make_migration.php`を呼び出します）

## ルーティング

ルート定義は[src/Config/routes.php](src/Config/routes.php)に集約されています。

| メソッド | パス | 内容 |
| --- | --- | --- |
| GET | `/` | 404ハンドラ |
| GET | `/health` | ヘルスチェック |
| GET | `/auth` | OAuth認可URL取得 |
| GET | `/auth/token/refresh_token` | アクセストークンのリフレッシュ（認証必須） |
| GET | `/auth/callback` | OAuthコールバック |

## エンドポイント

- API: http://localhost:8000
- ヘルスチェック: http://localhost:8000/health

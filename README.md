# DevCast

> Write once. Publish everywhere.

DevCastは、技術記事を1箇所で管理し、Qiita・DEV Community・はてなブログなど複数の技術記事プラットフォームへ配信できる、技術者向けのHeadless CMS / Multi-Publisherです。

DevCast自身を記事のSingle Source of Truthとし、各外部プラットフォームはPublication Targetとして扱います。詳細な仕様は [PRD.md](PRD.md) を参照してください。

## 構成

モノレポ構成で、以下の3つのアプリケーションから成り立っています。

| ディレクトリ | 役割 | スタック |
| --- | --- | --- |
| [backend](backend) | Content API / 記事管理・配信ロジック | PHP (Slim Framework, PHP-DI, Illuminate Database) |
| [frontend](frontend) | 管理画面 | React 19, Vite, TypeScript, MUI, TanStack Query |
| [lp](lp) | ランディングページ | Astro, Tailwind CSS |

インフラはMySQL + Adminerを含む`docker-compose.yaml`で管理しています。

## Requirements

- Docker / Docker Compose
- (ローカル開発時) PHP, Composer, Node.js / Bun

## Setup

1. 環境変数ファイルを用意します。

```bash
cp .env.example .env
```

`.env`に以下の値を設定してください。

- `FTP_SERVER` / `FTP_USERNAME` / `FTP_PASSWORD`
- `JWT_SECRET` / `CRYPTO_KEY`
- `GITHUB_CLIENT_ID` / `GITHUB_CLIENT_SECRET` / `GITHUB_CLIENT_SECRET_PROD`
- `DB_*`（DB接続情報。デフォルト値あり）

2. 全サービスを起動します。

```bash
make up
```

3. DBマイグレーションを実行します。

```bash
make migrate
```

## Makefile コマンド

| コマンド | 内容 |
| --- | --- |
| `make up` | 全サービスをビルドして起動 |
| `make docker` | バックグラウンドで全サービスを起動 |
| `make docker-reset` | コンテナとボリュームを削除 |
| `make front` | frontendのみビルドして起動 |
| `make back` | backendのみビルドして起動 |
| `make lp` | lpのみビルドして起動 |
| `make migrate` | DBマイグレーションを実行 |
| `make make-migration name=<name>` | 新規マイグレーションファイルを作成 |

## アクセス先（デフォルトポート）

| サービス | URL |
| --- | --- |
| frontend | http://localhost:5174 |
| backend (API) | http://localhost:8000 |
| lp | http://localhost:4321 |
| Adminer | http://localhost:8080 |
| MySQL | localhost:3306 |

## License

CC-BY-NC-ND-4.0

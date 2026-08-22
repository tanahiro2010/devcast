# DevCast Frontend

DevCastの管理画面。記事の作成・管理、各プラットフォームへの配信状態の確認などを行うSPAです。

## スタック

- React 19 + TypeScript
- Vite（Rolldown版、React Compiler有効）
- [MUI](https://mui.com/) + Tailwind CSS
- [TanStack Query](https://tanstack.com/query)
- [react-router-dom](https://reactrouter.com/) / [@util-tools/react-router-dsl](https://www.npmjs.com/package/@util-tools/react-router-dsl)
- Bun（パッケージマネージャ）
- Oxlint（Lint）

## ディレクトリ構成

```text
frontend/src
├── app/            # ページコンポーネント（home, auth, not-foundなど）
│   └── _auth/      # 認証が必要なページ
├── components/
│   ├── ui/         # 汎用UIコンポーネント
│   ├── layout/     # レイアウトコンポーネント
│   ├── screen/     # 画面単位のコンポーネント
│   └── icons/      # アイコン
├── config/         # ルーティング・アプリ設定
├── hooks/          # カスタムフック
├── lib/
│   ├── api/        # APIクライアント
│   └── utils.ts
├── middleware/      # 認証ミドルウェア（auth.tsx）
├── types/          # 型定義
└── theme.ts        # MUIテーマ設定
```

## Setup

リポジトリルートの`.env`を使用します（詳細は[ルートREADME](../README.md)を参照）。

### Docker経由（推奨）

リポジトリルートから:

```bash
make front
```

### ローカル実行

```bash
cd frontend
bun install
bun dev
```

http://localhost:5174 で起動します。

## コマンド

| コマンド | 内容 |
| --- | --- |
| `bun dev` | 開発サーバー起動 |
| `bun build` | 型チェック + 本番ビルド |
| `bun preview` | ビルド結果のプレビュー |
| `bun lint` | Oxlintによるlint |

## 認証

`middleware/auth.tsx`でバックエンドのOAuth（GitHub）を用いた認証状態を管理し、`app/_auth`配下は認証済みユーザーのみアクセス可能なページとして扱います。

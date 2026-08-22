# DevCast LP

DevCastのランディングページ。サービス紹介・料金プラン・FAQ・利用規約などの静的コンテンツを提供します。

## スタック

- [Astro](https://astro.build/)
- Tailwind CSS
- Bun（パッケージマネージャ）

## ディレクトリ構成

```text
lp/src
├── components/     # セクション単位のコンポーネント（Hero, Pricing, Faqなど）
├── layouts/        # ページレイアウト（Layout, LegalLayout）
├── data/           # 静的データ（pricing, faq, business）
├── pages/          # ルーティング対象のページ
└── styles/         # グローバルスタイル
```

### 主なページ

| パス | 内容 |
| --- | --- |
| `/` | トップページ |
| `/pricing` | 料金プラン |
| `/faq` | FAQ |
| `/terms` | 利用規約 |
| `/privacy` | プライバシーポリシー |
| `/tokushoho` | 特定商取引法に基づく表記 |

## Setup

リポジトリルートの`.env`を使用します（詳細は[ルートREADME](../README.md)を参照）。

### Docker経由（推奨）

リポジトリルートから:

```bash
make lp
```

### ローカル実行

```bash
cd lp
bun install
bun dev
```

http://localhost:4321 で起動します。

## コマンド

| コマンド | 内容 |
| --- | --- |
| `bun dev` | 開発サーバー起動 |
| `bun build` | 本番ビルド（`./dist/`に出力） |
| `bun preview` | ビルド結果のプレビュー |
| `bun astro ...` | Astro CLIコマンド（`astro add`, `astro check`など） |

## 参考

- [Astro公式ドキュメント](https://docs.astro.build)

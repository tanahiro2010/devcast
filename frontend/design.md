# DevCast Frontend デザインガイド

DevCast 管理画面(`frontend/`)の現行デザインを整理したドキュメント。LP(`lp/`)のトーンに寄せたダークテーマを採用しており、MUI(テーマ・フォーム系コンポーネント)と Tailwind CSS(レイアウト・カスタムUI)を併用している。

## デザインの方向性

- **ベーステーマ: ダークモード固定**(ライト/ダーク切り替えなし)
- LPの意匠(黒背景・オレンジのアクセント・hairlineボーダー)を管理画面にも統一して適用し、ログイン画面〜ダッシュボードまで一貫したトーンにする
- 情報密度は中程度。編集的でミニマルなレイアウト(大きな余白、細い罫線、控えめな装飾)

## カラーパレット

| 用途 | 値 | 使用箇所 |
| --- | --- | --- |
| Accent(primary) | `#ff5a1f` | ボタン、アクティブなナビ項目のドット、kicker見出し、警告的なステータス表示 |
| 背景(default) | `#0a0a0a` | ページ全体・Sidebar背景 |
| 背景(paper) | `#111111` | MUIのPaper系コンポーネント |
| テキスト(primary) | `#f5f5f5` (≒ `text-white` / `text-neutral-100`) | 見出し・本文の主テキスト |
| テキスト(secondary) | `#a3a3a3` (≒ `text-neutral-400`〜`500`) | 補助テキスト、kickerラベル |
| ボーダー(divider) | `rgba(255,255,255,0.1)` (≒ `border-white/10`) | カード・テーブル・セクション区切り全般 |
| 成功 | Tailwind `emerald-400` | 配信済み(published)、同期済み(synced)ステータス |
| 警告/エラー | Accent色 or MUIの `error` パレット(dark mode既定) | 未同期ステータス、エラーAlert |

Tailwind側は `App.css` の `@theme` で `--color-accent: #ff5a1f` として定義し、`bg-accent` / `text-accent`(`.accent`ユーティリティ経由)で参照する。MUI側は `theme.ts` の `palette.primary.main` に同じ値を設定し、両ライブラリで色がずれないようにしている。

## タイポグラフィ

- **本文フォント**: Roboto(`index.html` でGoogle Fontsから読み込み。MUIテーマは `'Google Sans', 'Roboto', 'Helvetica', 'Arial', sans-serif` を指定)
- **等幅フォント**: IBM Plex Mono(コードブロック・配信先タグ・テーブルの一部列など、LPと同じ用途で使用)。Tailwindの `font-mono` ユーティリティが `--font-mono` トークン経由でこれを参照する
- **kicker(ラベル見出し)**: `text-[10px]`〜`text-[11px]` + `uppercase` + `.kicker`(`letter-spacing: 0.12em`)。"PUBLISHED" "PUBLISHING OVERVIEW" のような小さな大文字ラベルに使用

## レイアウト構造

```
<section class="flex min-h-screen">
  <Sidebar />                 <!-- 幅 240px(md:w-60)、モバイルはオフキャンバス -->
  <main class="flex-1 flex flex-col items-center bg-[#0a0a0a]">
    <Header />                 <!-- h-16、内容は max-w-4xl で中央寄せ -->
    <div class="max-w-4xl w-full px-4 sm:px-6 md:px-10 py-6 sm:py-10">
      ページコンテンツ
    </div>
  </main>
</section>
```

- ページコンテンツは常に `max-w-4xl` で中央寄せし、Sidebar横の余白の中心に配置する(左詰めにしない)
- 角丸は基本 `rounded-lg`(カード・テーブル)、MUIボタンは `borderRadius: 8`、MUIの `Paper` は `borderRadius: 28`
- カード・テーブルの区切りは背景色ではなく `border` (hairline) で表現する

## コンポーネント構成

```
components/
├── layout/
│   └── Sidebar.tsx        レイアウト骨格(ダーク固定、レスポンシブ対応)
├── screen/
│   ├── Loading.tsx         全画面ローディングオーバーレイ
│   └── home/                Home画面専用コンポーネント
│       ├── HomeHeader.tsx
│       ├── PublishingOverview.tsx  (kicker + 見出し)
│       ├── StatsGrid.tsx
│       ├── ArticleSummaryCard.tsx
│       ├── PublicationStatusList.tsx
│       └── ArticleTable.tsx
└── ui/                      汎用UIパーツ
    ├── StatCard.tsx
    └── StatusTag.tsx
```

- `ui/` = 複数画面で使い回せる汎用パーツ、`screen/<page>/` = そのページに閉じた組み立てコンポーネント、というルールで分離している
- ページ本体(`home.tsx` など)はデータを定義して `screen/` コンポーネントを組み立てるだけの薄い構成にする

### Sidebar

- 幅 `w-64`(モバイル)/ `w-60`(`md:`以上)、背景 `#0a0a0a`、右に `border-white/10`
- デスクトップ(`md:`)は常時表示・レイアウトに参加、モバイルは `fixed` + `-translate-x-full` でオフキャンバス化し、ハンバーガーボタン・オーバーレイ・✕ボタンで開閉する
- ナビ項目はアクティブ時のみ左にアクセントカラーのドット(`w-1.5 h-1.5 rounded-full bg-accent`)+ `bg-white/5 text-white`。非アクティブは `text-neutral-400`
- 未実装ルートへの項目は `NavLink` ではなく通常の `<a>` にして、意図しないアクティブ判定を避けている
- フッターに `useProfile()` から取得したユーザー名を表示(kickerラベル "Signed in as" 付き)

### ページヘッダー(例: HomeHeader)

- 高さ `h-16`、下に `border-white/10`、中身は `max-w-4xl` で中央寄せ
- 右側にプライマリアクション(白背景・黒文字のボタン。MUIの `contained` ボタンとは別の、Tailwindで組んだ独自スタイル)

### カード / テーブル

- 外枠 `border border-white/10 rounded-lg overflow-hidden`
- 内部の区切りは `border-r` / `border-b` の hairline(グリッドの各セルに個別指定)
- モバイルではテーブルを `overflow-x-auto` + `min-w-[560px]` で横スクロール可能にする
- ステータス表示は `StatusTag`(draft = accent色、published = emerald-400)、配信状況リストは `✔ Latest`(emerald) / `⚠ Update available`(accent)の2状態

## MUIの利用方針

- MUIは `theme.ts` で `palette.mode: "dark"` を設定し、ログイン画面(`auth.tsx`)・404画面・ローディング画面など「フォーム/状態表示中心の画面」に使用
- ダッシュボード本体(Sidebar・Home以下)は独自のTailwindベースUIで構築し、MUIには依存しない
- `MuiButton` は `disableElevation: true`、`textTransform: "none"`、`borderRadius: 8` で統一
- ログイン画面は `Box` → `Container maxWidth="xs"` → `Stack` の構成で、`overline` バリアントで "DevCast" のkickerを表示し、Sidebarと同じアクセントカラーをボタン(`color="primary"`)に適用する

## レスポンシブ方針

- ブレークポイントは Tailwind の `sm` / `md` を使用(`md` = デスクトップ / サイドバー常時表示の境界)
- 統計グリッドなどは `sm:` 未満で2列、`sm:` 以上で列数を増やす
- 2カラムのカードは `sm:` 未満で縦積み、`sm:` 以上で横並び
- Sidebarは `md:` 未満でオフキャンバス化

## 参考サンプル

`frontend/samples/` に、方向性検討時に作成した静的HTMLモックが残っている。現行デザインは `sample-06-lp-match-dark.html`(ダッシュボード)と `sample-09-login-dark-mui.html`(ログイン、実際に `@mui/material` をESM importして描画)がベースになっている。

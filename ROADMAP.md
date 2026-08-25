# DevCast 開発ロードマップ

最終更新: 2026-08-25

このロードマップは PRD.md の「48. Development Phases」を、現在の実装状況に合わせて並べ直したものです。
厳密なスケジュールではなく、**次に何から手をつけるべきかの優先順位表**として扱ってください。実装しながら順序や粒度は柔軟に変えて構いません。

---

## 現状スナップショット(2026-08-25時点)

### 実装済み
- GitHub OAuthによる認証基盤(login / callback / access token / refresh token / profile)
- JWTベースのセッション管理、`User` / `Credential` / `Session` / `RefreshToken` モデル
- 外部プロバイダー用トークン保存の土台(`ProviderToken` モデル、`/v1/providers` API)
- フロントの認証フロー(`AuthContext` / `useAuthState` / `AuthMiddleware` / `GuestMiddleware`)
- 共通APIクライアント、エラーハンドリング基盤
- サイドバーナビ(記事一覧/配信先設定/タグ管理/分析/設定へのリンクのみ、中身は未実装)

### まだ存在しない(重要)
- **Article / Publication / Tag / Revision のDBモデル自体が一切ない**
- 記事CRUD、Markdownエディタ、ダッシュボード画面
- Qiita / DEV.to / はてなブログへの実際の投稿ロジック
- Content API、APIキー発行

→ つまり「土台(認証・連携トークン管理)はできたが、プロダクトの核である記事管理はこれから」という段階です。Publisher連携より先に、まずCore CMSを完成させる必要があります。

---

## Phase 1 — Core CMS(最優先・ここから着手)

PRDのPhase 1相当。これがないと他の何も作れないので最優先。

- [ ] `Article` テーブル/モデル(id, workspaceId, authorId, title, slug, description, body, language, status, revision, timestamps)
- [ ] `Tag` テーブル/モデル + Article との多対多
- [ ] 記事CRUD API(`POST/GET/PUT/DELETE /v1/articles`)
- [ ] フロント: Markdownエディタ画面(入力 + プレビュー + Title/Slug/Description/Tags)
- [ ] フロント: Draft保存 / 記事一覧(ダッシュボード)画面
- [ ] 既存のSidebarリンクに実体を接続

**完了条件**: DevCast単体で記事を書いて下書き保存・編集・削除できる。

---

## Phase 2 — Publisher基盤

Article ができてから着手。まだ特定サービスには繋がず、抽象レイヤーだけ作る。

- [ ] `Publication` テーブル/モデル(articleId, provider, externalId, externalUrl, status, sourceRevision, syncedAt, lastError)
- [ ] Revisionシステム(Article更新でincrement、Publication側との比較でOutdated判定)
- [ ] `Publisher` インターフェース(publish / update / validate)の定義

**完了条件**: Publisherの型は揃っているが、まだ実装(具象クラス)はゼロでもよい。

---

## Phase 3 — Qiita連携

3つの外部連携の中で最も実装が軽い(OAuth + REST API)ため最初に着手する候補。

- [ ] Qiita OAuth(read_qiita / write_qiita)
- [ ] `QiitaPublisher`(publish / update)
- [ ] Qiita Transformer(Tags, Title, Body差異吸収)
- [ ] Connectionsページ(接続/切断UI)

**完了条件**: DevCastの記事をQiitaへ公開・更新できる。

---

## Phase 4 — DEV.to連携

API Key方式なのでOAuthフローが不要、Qiitaより実装コストが低い。並行着手も可能。

- [ ] API Key登録・暗号化保存・Connection Test
- [ ] `DevToPublisher`(publish / update)
- [ ] Forem API向けTransformer(title, description, body_markdown, tags, published, canonical_url)

**完了条件**: DevCastの記事をDEV.toへ公開・更新できる。

---

## Phase 5 — はてなブログ連携(GitHub経由)

GitHub OAuthの基盤(`Libraries/GitHub.php`)が既にあるため、着手ハードルは見た目より低い。ただしRepository操作・Front Matter生成・Commitロジックが新規に必要。

- [ ] owner/repository をPublication Targetとして登録
- [ ] `articles/<slug>.md` 生成 + Front Matter(emoji, type, topics, published)
- [ ] Commit実行(`HatenaPublisher`相当)

**完了条件**: DevCastの記事更新がGitHub経由ではてなブログへ反映される。

---

## Phase 6 — Multi-Publisher統合

Qiita/DEV.to/はてなブログのうち最低2つが動いてから着手すると効果を実感しやすい。

- [ ] 記事編集画面でのPublish先マルチセレクトUI
- [ ] 各Publisherの独立実行(Partial Success許容、rollbackしない)
- [ ] ダッシュボードでの同期状態表示(✅ Latest / ⚠ Outdated)
- [ ] Provider単位のエラー保存・表示・Retry UI

**完了条件**: PRD 37章のMVP完成フロー(ログイン→執筆→複数先Publish→編集→Outdated検出→Sync All)が通しで動く。

---

## Phase 7 — Content API

Publisher機能と並行で進めても問題ない(Article基盤にのみ依存)。早めにフロント/外部連携の需要があるなら Phase 3〜5 より先に上げてもよい。

- [ ] Public API(`GET /v1/public/:username/articles`, `/:slug`)— 認証不要
- [ ] APIキー発行・失効(`ApiKey` モデル、Hash保存、作成直後のみplaintext表示)
- [ ] Authenticated API(`GET /v1/articles`, `/v1/tags` 等)
- [ ] Rate Limiting

**完了条件**: 自分のポートフォリオサイトからAPI Key経由で記事一覧を取得できる。

---

## Phase 8 — Developer Experience

- [ ] OpenAPI Specification
- [ ] API Docs UI(Scalar or Swagger UI)
- [ ] Next.js / Astro 実装例

---

## Phase 9 — Security Review

MVPリリース直前に必ず実施。

- [ ] OAuth(state検証, Redirect URI検証, CSRF, 可能な範囲でPKCE)
- [ ] Authorization(全リソースへの `workspaceId` 所有権チェック、IDOR対策)
- [ ] Secrets(DEV.to API Key / OAuth Token / GitHub Credentialの暗号化保存確認)
- [ ] Rate Limit / Input Validation / Markdown Sanitization / CORS / CSP

---

## Phase 10 — Beta公開

- [ ] devcast.work へのデプロイ
- [ ] Public Beta告知

---

## Post-Beta(参考・優先度は状況次第)

PRD側では以下の順序を提案していますが、ユーザーの反応を見てから決めるのが良いです。

```
AI Translation → Article Variants → Chrome Prompt API → Cloud AI Fallback
→ Webhooks → RSS → Scheduled Publishing → Analytics → Team Workspace
```

---

## 進め方の指針(柔軟性を持たせるための注意点)

1. **Phase 1(Core CMS)は必須の一本道。** ここを飛ばして連携機能に手を出すと土台がないため手戻りが大きい。
2. **Phase 3〜5(各Publisher連携)は順序の入れ替え・並行実装が可能。** DEV.toはOAuth不要で一番軽いので、モチベーション維持のために先に着手するのもあり。
3. **Phase 7(Content API)はPhase 3〜6と並行しても支障ない。** Article基盤さえあれば独立して進められる。
4. 各Phaseの「完了条件」は目安であり、フル機能を待たずに次のPhaseへ進んでも構わない(例: Qiitaのpublishだけ動けばDEV.to着手に移ってもよい)。
5. Post-MVP(AI翻訳等)には現時点では着手しない — PRD Non-Goalsにも明記されている通り。

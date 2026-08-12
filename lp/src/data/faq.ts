import { pricingFaqs, type Faq } from "./pricing";

// Product/security-oriented questions that don't fit naturally on the
// pricing page, but belong on the standalone FAQ hub.
export const productFaqs: Faq[] = [
	{
		q: "GitHubアカウントがなくても使えますか？",
		a: "MVPではGitHub OAuthログインを第一候補としています。今後、他の認証方式の追加も検討予定です。",
	},
	{
		q: "記事はMarkdownで書けますか？",
		a: "はい。DevCastのエディタはMarkdown入力とプレビューに対応しています。",
	},
	{
		q: "1つの記事を複数の媒体に同時公開できますか？",
		a: "はい。Publish画面でQiita・DEV.to・はてなブログを選択し、まとめて公開できます。1媒体の投稿が失敗しても、他の媒体はそのまま公開されます（Partial Success）。",
	},
	{
		q: "記事を更新した場合、各媒体にはどう反映されますか？",
		a: "記事を更新するとRevisionが更新され、各Publisherの同期状態（Latest / Outdated）が自動的に表示されます。Outdatedな媒体だけをSync Allで再投稿できます。",
	},
	{
		q: "連携先の認証情報は安全に保存されますか？",
		a: "OAuthアクセストークンやDEV Community APIキーは暗号化して保存し、DevCastが発行するAPIキーはハッシュ化して保存します。APIキーは発行直後のみ平文を表示し、以後は再表示しません。",
	},
];

export const allFaqs: Faq[] = [...productFaqs, ...pricingFaqs];

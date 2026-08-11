export type PlanFeatureValue = boolean | string;

export interface Plan {
	id: "free" | "pro" | "business";
	name: string;
	tagline: string;
	price: string;
	period: string;
	annualNote?: string;
	description: string;
	featured: boolean;
	features: string[];
	cta: { label: string; href: string };
}

export const plans: Plan[] = [
	{
		id: "free",
		name: "Free",
		tagline: "個人で始める",
		price: "¥0",
		period: "",
		description: "DevCastのコア機能を無料で。まずはMulti-Publishを体験してください。",
		featured: false,
		features: [
			"記事数無制限",
			"全Publisher接続（Qiita / DEV.to / はてなブログ）",
			"Multi-Publish / Sync Detection",
			"Public Content API",
			"API Key 1個",
		],
		cta: { label: "無料で始める", href: "https://app.devcast.work/signup" },
	},
	{
		id: "pro",
		name: "Pro",
		tagline: "継続的に発信するエンジニアへ",
		price: "¥980",
		period: "/月",
		annualNote: "年払い ¥9,800（2ヶ月分お得）",
		description: "AI翻訳や記事履歴など、DevCast独自の生産性機能をフル活用。",
		featured: true,
		features: [
			"Freeの全機能",
			"Private Content API（API Key 5個）",
			"Article History 無制限",
			"AI Translation（月20回）",
			"Scheduled Publishing",
		],
		cta: { label: "Proを始める", href: "https://app.devcast.work/signup?plan=pro" },
	},
	{
		id: "business",
		name: "Business",
		tagline: "チーム・組織向け",
		price: "¥2,980",
		period: "/月",
		annualNote: "年払い ¥29,800（2ヶ月分お得）",
		description: "Team WorkspaceとAnalyticsで、組織の技術発信を横断管理。",
		featured: false,
		features: [
			"Proの全機能",
			"Team Workspace（Owner / Admin / Editor / Viewer）",
			"Webhooks",
			"Analytics統合",
			"Custom Domain",
		],
		cta: { label: "お問い合わせ", href: "mailto:hello@devcast.work" },
	},
];

export interface ComparisonRow {
	label: string;
	free: PlanFeatureValue;
	pro: PlanFeatureValue;
	business: PlanFeatureValue;
}

export interface ComparisonGroup {
	category: string;
	rows: ComparisonRow[];
}

export const comparisonGroups: ComparisonGroup[] = [
	{
		category: "コンテンツ管理",
		rows: [
			{ label: "記事数", free: "無制限", pro: "無制限", business: "無制限" },
			{ label: "Publisher接続（Qiita / DEV.to / はてな）", free: true, pro: true, business: true },
			{ label: "Multi-Publish / Sync Detection", free: true, pro: true, business: true },
			{ label: "Article History（版管理）", free: "直近のみ", pro: "無制限・差分表示", business: "無制限・差分表示" },
			{ label: "Scheduled Publishing", free: false, pro: true, business: true },
		],
	},
	{
		category: "Content API",
		rows: [
			{ label: "Public Content API", free: true, pro: true, business: true },
			{ label: "Private Content API", free: false, pro: true, business: true },
			{ label: "API Key数", free: "1個", pro: "5個", business: "無制限" },
			{ label: "Rate Limit", free: "標準", pro: "拡張", business: "最大" },
			{ label: "Custom Domain配信", free: false, pro: false, business: true },
		],
	},
	{
		category: "AI",
		rows: [
			{ label: "AI Translation", free: false, pro: "月20回", business: "無制限 + Cloud AI優先" },
			{ label: "AI Localization（将来提供）", free: false, pro: "順次対応", business: "順次対応" },
		],
	},
	{
		category: "チーム・組織",
		rows: [
			{ label: "Team Workspace", free: false, pro: false, business: true },
			{ label: "Webhooks", free: false, pro: false, business: true },
			{ label: "Analytics統合", free: false, pro: "簡易版", business: "フル（媒体横断）" },
		],
	},
	{
		category: "サポート",
		rows: [{ label: "サポート窓口", free: "コミュニティ", pro: "メール", business: "優先サポート" }],
	},
];

export interface Faq {
	q: string;
	a: string;
}

export const homeFaqs: Faq[] = [
	{
		q: "Publisher連携はすべて無料ですか？",
		a: "はい。Qiita・DEV.to・はてなブログへの接続と公開はFreeプランで無制限にご利用いただけます。",
	},
	{
		q: "既存の記事をインポートできますか？",
		a: "Qiita / DEV.to / はてなブログからの記事インポート機能を今後提供予定です。",
	},
	{
		q: "AI翻訳の内容はそのまま公開されますか？",
		a: "いいえ。翻訳結果は必ずユーザーが確認・編集してから公開する設計になっています。",
	},
];

export const pricingFaqs: Faq[] = [
	...homeFaqs,
	{
		q: "プランはいつでも変更できますか？",
		a: "はい。アップグレード・ダウングレードはいつでも可能です。日割り計算で差額を精算します。",
	},
	{
		q: "支払い方法は何がありますか？",
		a: "クレジットカードに対応しています（Visa, Mastercard, American Express）。",
	},
	{
		q: "年払いにするとどれくらいお得ですか？",
		a: "年払いを選択すると、月払いの2ヶ月分相当を割引した価格になります。",
	},
	{
		q: "Free と Pro の違いは何ですか？",
		a: "Publisherへの接続とMulti-Publish自体はFreeでも無制限です。Proでは記事履歴の無制限化、AI翻訳、予約投稿など、DevCast独自の生産性機能が使えるようになります。",
	},
	{
		q: "Businessプランのチーム機能はいつ使えますか？",
		a: "Team WorkspaceはPost-MVPで順次提供予定です。Businessプランをご契約いただくと、提供開始時に優先的にご案内します。",
	},
	{
		q: "解約するとデータはどうなりますか？",
		a: "解約後も記事データは一定期間保持されます。Freeプランへダウングレードした場合、Pro / Business限定機能へのアクセスはできなくなりますが、記事自体が失われることはありません。",
	},
];

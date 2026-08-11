// Business/legal identity used across the legal pages (Tokushoho, Terms,
// Privacy). Update these placeholders with real information before the
// service goes live — Tokushoho disclosure requires accurate values.
export const business = {
	/** 販売事業者名（法人名 or 個人事業主の氏名） */
	legalName: "田中博悠",
	/** 運営統括責任者の氏名 */
	representative: "田中博悠",
	/** 郵便番号 */
	postalCode: "669-1535",
	/** 所在地 */
	address: "兵庫県三田市南が丘二丁目13番65号",
	/** 電話番号 */
	phone: "070-9043-3499",
	/** お問い合わせ用メールアドレス */
	email: "herentongkegu087@gmail.com",
	/**
	 * 個人事業主等で、所在地・電話番号を常時公開せず「請求があれば開示」
	 * とする場合は true。法人の場合は false にして常時表示する。
	 */
	disclosureOnRequest: true,
} as const;

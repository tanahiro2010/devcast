// Business/legal identity used across the legal pages (Tokushoho, Terms,
// Privacy). Update these placeholders with real information before the
// service goes live — Tokushoho disclosure requires accurate values.
export const business = {
	/** 販売事業者名（法人名 or 個人事業主の氏名） */
	legalName: "［事業者名を記載］",
	/** 運営統括責任者の氏名 */
	representative: "［運営責任者の氏名を記載］",
	/** 郵便番号 */
	postalCode: "［郵便番号を記載］",
	/** 所在地 */
	address: "［所在地を記載］",
	/** 電話番号 */
	phone: "［電話番号を記載］",
	/** お問い合わせ用メールアドレス */
	email: "［お問い合わせ用メールアドレスを記載］",
	/**
	 * 個人事業主等で、所在地・電話番号を常時公開せず「請求があれば開示」
	 * とする場合は true。法人の場合は false にして常時表示する。
	 */
	disclosureOnRequest: true,
} as const;

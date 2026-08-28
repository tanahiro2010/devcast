import type { Article } from "../../types/api"

type ArticleStatus = Article["status"][number]["status"]

const label: Record<ArticleStatus, string> = {
  draft: "● draft",
  pending: "● pending",
  published: "● published",
}

const style: Record<ArticleStatus, string> = {
  draft: "text-neutral-400",
  pending: "accent",
  published: "text-emerald-400",
}

type Props = { status: ArticleStatus }

const StatusTag = ({ status }: Props) => (
  <span className={style[status]}>{label[status]}</span>
)

export { StatusTag }
export type { ArticleStatus }

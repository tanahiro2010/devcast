import type { ArticleStatus } from "../../types/article"

const label: Record<ArticleStatus, string> = {
  draft: "● draft",
  published: "● published",
}

const style: Record<ArticleStatus, string> = {
  draft: "accent",
  published: "text-emerald-400",
}

type Props = { status: ArticleStatus }

const StatusTag = ({ status }: Props) => (
  <span className={style[status]}>{label[status]}</span>
)

export { StatusTag }

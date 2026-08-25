type ArticleStatus = "draft" | "published"

type Article = {
  title: string
  targets: string
  status: ArticleStatus
  updated: string
}

type PublicationState = "synced" | "pending"

type PublicationStatus = {
  platform: string
  state: PublicationState
}

type Stat = {
  label: string
  value: number
}

export type { Article, ArticleStatus, PublicationState, PublicationStatus, Stat }

import { HomeHeader } from "../../components/screen/home/HomeHeader"
import { StatsGrid } from "../../components/screen/home/StatsGrid"
import { ArticleSummaryCard } from "../../components/screen/home/ArticleSummaryCard"
import { ArticleTable } from "../../components/screen/home/ArticleTable"
import type { Article, PublicationStatus, Stat } from "../../types/article"

const stats: Stat[] = [
  { label: "Published", value: 42 },
  { label: "Drafts", value: 7 },
  { label: "Targets", value: 3 },
  { label: "Synced today", value: 18 },
]

const publicationStatus: PublicationStatus[] = [
  { platform: "Qiita", state: "synced" },
  { platform: "DEV.to", state: "synced" },
  { platform: "はてなブログ", state: "pending" },
]

const articles: Article[] = [
  { title: "PHP-DIとSlim Frameworkの設計指針", targets: "hatena", status: "draft", updated: "2026-08-19" },
  { title: "Multi-Publisher CMSを設計する", targets: "qiita, dev.to, hatena", status: "published", updated: "2026-08-15" },
]

const Home = () => {
  return (
    <>
      <HomeHeader />
      <div className="w-full px-4 sm:px-6 md:px-10 py-6 sm:py-10 max-w-4xl">
        <p className="kicker text-[11px] font-semibold accent uppercase mb-3">Publishing overview</p>
        <h2 className="text-[1.4rem] sm:text-[1.6rem] font-semibold tracking-tight leading-tight mb-6 sm:mb-8 text-white">
          今日も、書いたら配信するだけ。
        </h2>

        <StatsGrid stats={stats} />
        <ArticleSummaryCard
          title="React 19のCompilerを試してみた"
          revision={5}
          updatedAt="3時間前"
          tags={["react", "frontend"]}
          publicationStatus={publicationStatus}
        />
        <ArticleTable articles={articles} />
      </div>
    </>
  )
}

export default Home

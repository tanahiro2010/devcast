import type { Provider } from "../../types/api"
import type { ArticlesWithMetadata } from "../../lib/api/v1/articles"
import type { Stat } from "../../components/ui/StatCard"
import type { PublicationStatus } from "../../components/screen/home/PublicationStatusList"
import { useQuery } from "@tanstack/react-query"
import { HomeHeader } from "../../components/screen/home/HomeHeader"
import { StatsGrid } from "../../components/screen/home/StatsGrid"
import { ArticleSummaryCard } from "../../components/screen/home/ArticleSummaryCard"
import { ArticleTable } from "../../components/screen/home/ArticleTable"
import { Loading } from "../../components/screen/Loading"
import { client } from "../../lib/api"


const publicationStatus: PublicationStatus[] = [
  { platform: "Qiita", state: "synced" },
  { platform: "DEV.to", state: "synced" },
  { platform: "はてなブログ", state: "pending" },
]

const Home = () => {
  const { data, isPending, error } = useQuery<[Provider[], ArticlesWithMetadata]>({
    queryKey: ["providers", "articles"],
    queryFn: () => Promise.all([
      client.v1.providers.getProviders(),
      client.v1.articles.getArticlesWithMetadata()
    ])
  })

  if (isPending) return <Loading />
  if (error) return <div></div>

  const [providers, { metadata, articles }] = data
  const stats: Stat[] = [
    { label: "Publish", value: metadata.published_count },
    { label: "Drafts",  value: metadata.draft_count },
    { label: "Targets", value: providers.length },
    { label: "Total",   value: metadata.total_count }
  ]

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

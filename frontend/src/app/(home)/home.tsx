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
import { ErrorState } from "../../components/screen/ErrorState"
import { client } from "../../lib/api"

const Home = () => {
  const { data, isPending, error, refetch } = useQuery<[Provider[], ArticlesWithMetadata]>({
    queryKey: ["providers", "articles"],
    queryFn: () => Promise.all([
      client.v1.providers.getProviders(),
      client.v1.articles.getArticlesWithMetadata()
    ])
  })

  if (isPending) return <Loading />
  if (error) return <ErrorState onRetry={() => refetch()} />

  const [providers, { metadata, articles }] = data
  const stats: Stat[] = [
    { label: "Publish", value: metadata.published_count },
    { label: "Drafts",  value: metadata.draft_count },
    { label: "Targets", value: providers.length },
    { label: "Total",   value: metadata.total_count }
  ]
  const publicationStatus: PublicationStatus[] = providers.map((provider: Provider) => ({
    platform: provider.provider,
    state: provider.expires_at < new Date() ? "active" : "inactive"
  }))

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

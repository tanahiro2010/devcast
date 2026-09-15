import { StatusTag, type ArticleStatus } from "../../ui/StatusTag"
import type { Article } from "../../../types/api"

type Props = { articles: Article[] }

// A single article can be at a different stage per provider. Roll the
// per-provider statuses up into one badge for this summary row: an
// in-flight publish (pending) is the most actionable state, so it wins
// over an already-published provider, which in turn wins over an
// all-draft row.
const derivePrimaryStatus = (statuses: Article["status"]): ArticleStatus => {
  if (statuses.some((s) => s.status === "pending")) return "pending"
  if (statuses.some((s) => s.status === "published")) return "published"
  return "draft"
}

const formatDate = (date: Date) => new Date(date).toLocaleDateString("sv-SE")

const HEADER_ITEMS = [
  "Title", "Targets", "Status", "Updated"
]

const ArticleTable = ({ articles }: Props) => (
  <div className="mt-6 sm:mt-10 border border-white/10 rounded-lg overflow-hidden">
    <div className="overflow-x-auto">
      <table className="w-full text-[13px] min-w-[560px]">
        <thead className="text-neutral-500 kicker text-[10px] uppercase border-b border-white/10">
          <tr>
            {HEADER_ITEMS.map((item: string) => (
              <HeaderItem>{item}</HeaderItem>
            ))}
            {/* <th className="text-left px-4 sm:px-6 py-3 font-medium">Title</th>
            <th className="text-left px-4 sm:px-6 py-3 font-medium">Targets</th>
            <th className="text-left px-4 sm:px-6 py-3 font-medium">Status</th>
            <th className="text-left px-4 sm:px-6 py-3 font-medium">Updated</th> */}
          </tr>
        </thead>
        <tbody className="divide-y divide-white/10">
          {articles.map((article) => (
            <tr key={article.id}>
              <td className="px-4 sm:px-6 py-4 font-medium text-white whitespace-nowrap">{article.title}</td>
              <td className="px-4 sm:px-6 py-4 font-mono text-neutral-400 whitespace-nowrap">
                {article.status.map((s) => s.provider).join(", ")}
              </td>
              <td className="px-4 sm:px-6 py-4 whitespace-nowrap">
                <StatusTag status={derivePrimaryStatus(article.status)} />
              </td>
              <td className="px-4 sm:px-6 py-4 text-neutral-500 whitespace-nowrap">{formatDate(article.updated_at)}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  </div>
)

const HeaderItem = ({ children }: { children: string }) => (
  <th className="text-left px-4 sm:px-6 py-3 font-medium">{ children }</th>
)

export { ArticleTable }

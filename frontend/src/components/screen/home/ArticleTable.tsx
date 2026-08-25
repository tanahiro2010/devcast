import { StatusTag } from "../../ui/StatusTag"
import type { Article } from "../../../types/article"

type Props = { articles: Article[] }

const ArticleTable = ({ articles }: Props) => (
  <div className="mt-6 sm:mt-10 border border-white/10 rounded-lg overflow-hidden">
    <div className="overflow-x-auto">
      <table className="w-full text-[13px] min-w-[560px]">
        <thead className="text-neutral-500 kicker text-[10px] uppercase border-b border-white/10">
          <tr>
            <th className="text-left px-4 sm:px-6 py-3 font-medium">Title</th>
            <th className="text-left px-4 sm:px-6 py-3 font-medium">Targets</th>
            <th className="text-left px-4 sm:px-6 py-3 font-medium">Status</th>
            <th className="text-left px-4 sm:px-6 py-3 font-medium">Updated</th>
          </tr>
        </thead>
        <tbody className="divide-y divide-white/10">
          {articles.map((article) => (
            <tr key={article.title}>
              <td className="px-4 sm:px-6 py-4 font-medium text-white whitespace-nowrap">{article.title}</td>
              <td className="px-4 sm:px-6 py-4 font-mono text-neutral-400 whitespace-nowrap">{article.targets}</td>
              <td className="px-4 sm:px-6 py-4 whitespace-nowrap">
                <StatusTag status={article.status} />
              </td>
              <td className="px-4 sm:px-6 py-4 text-neutral-500 whitespace-nowrap">{article.updated}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  </div>
)

export { ArticleTable }

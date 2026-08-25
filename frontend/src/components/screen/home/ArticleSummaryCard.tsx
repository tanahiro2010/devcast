import { PublicationStatusList } from "./PublicationStatusList"
import type { PublicationStatus } from "../../../types/article"

type Props = {
  title: string
  revision: number
  updatedAt: string
  tags: string[]
  publicationStatus: PublicationStatus[]
}

const ArticleSummaryCard = ({ title, revision, updatedAt, tags, publicationStatus }: Props) => (
  <div className="border border-white/10 rounded-lg overflow-hidden">
    <div className="grid grid-cols-1 sm:grid-cols-2">
      <div className="p-5 sm:p-8 border-b sm:border-b-0 sm:border-r border-white/10">
        <p className="kicker text-[11px] text-neutral-500 uppercase mb-4">Article</p>
        <p className="font-semibold mb-1 text-white">{title}</p>
        <p className="text-sm text-neutral-500 mb-6">revision {revision} · updated {updatedAt}</p>
        <div className="font-mono text-[13px] text-neutral-400 leading-6 bg-white/5 rounded-md p-4 break-words">
          # {title}<br />
          <span className="text-neutral-500">tags: {tags.join(", ")}</span>
        </div>
      </div>
      <div className="p-5 sm:p-8">
        <p className="kicker text-[11px] text-neutral-500 uppercase mb-4">Publication status</p>
        <PublicationStatusList items={publicationStatus} />
      </div>
    </div>
  </div>
)

export { ArticleSummaryCard }

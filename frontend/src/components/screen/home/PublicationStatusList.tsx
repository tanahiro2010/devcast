type PublicationState = "active" | "inactive"

type PublicationStatus = {
  platform: string
  state: PublicationState
}

type Props = { items: PublicationStatus[] }

const PublicationStatusList = ({ items }: Props) => (
  <div className="space-y-3 font-mono text-[13px]">
    {items.map((item) => (
      <div key={item.platform} className="flex justify-between items-center text-neutral-200">
        <span>{item.platform}</span>
        <span className={item.state === "active" ? "text-emerald-400" : "accent whitespace-nowrap"}>
          {item.state === "active" ? "✔ Active" : "⚠ API Key Inactive"}
        </span>
      </div>
    ))}
  </div>
)

export { PublicationStatusList }
export type { PublicationState, PublicationStatus }

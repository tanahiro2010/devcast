import { cn } from "../../lib/utils"

type Stat = {
  label: string
  value: number
}

type Props = Stat & { className?: string }

const StatCard = ({ label, value, className = "" }: Props) => (
  <div className={cn("p-4 sm:p-5", className)}>
    <p className="kicker text-[10px] uppercase text-neutral-500 mb-2">{label}</p>
    <p className="text-xl sm:text-2xl font-semibold text-white">{value}</p>
  </div>
)

export { StatCard }
export type { Stat }

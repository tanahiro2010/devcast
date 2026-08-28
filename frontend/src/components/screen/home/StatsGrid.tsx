import { StatCard, type Stat } from "../../ui/StatCard"

// Border sides drawn per grid cell so the 2x2 (mobile) / 1x4 (desktop)
// layout keeps a single hairline between every stat, never a doubled edge.
const borders = [
  "border-r border-b sm:border-b-0 border-white/10",
  "border-b sm:border-b-0 sm:border-r border-white/10",
  "border-r border-white/10",
  "",
]

type Props = { stats: Stat[] }

const StatsGrid = ({ stats }: Props) => (
  <div className="grid grid-cols-2 sm:grid-cols-4 gap-0 border border-white/10 rounded-lg overflow-hidden mb-6 sm:mb-10">
    {stats.map((stat, i) => (
      <StatCard key={stat.label} {...stat} className={borders[i] ?? ""} />
    ))}
  </div>
)

export { StatsGrid }

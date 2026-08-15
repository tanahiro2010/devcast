import { cn } from "../../lib/utils"

type Props = {
  children: React.ReactNode
  className: string
}

const Center = ({ children, className = "" }: Props) => (
  <div className={cn("h-screen flex flex-col items-center justify-center", className)}>{ children }</div>
)

export { Center }
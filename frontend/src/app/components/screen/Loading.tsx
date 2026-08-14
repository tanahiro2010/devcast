import { CircularProgress } from "@mui/material"

const Loading = () => {
  return (
    <section className="fixed inset-0 z-50 flex items-center justify-center bg-white/70">
      <CircularProgress />
    </section>
  )
}

export { Loading }
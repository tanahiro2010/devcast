import { CircularProgress } from "@mui/material"

const Loading = () => {
  return (
    <section className="fixed inset-0 z-50 flex items-center justify-center bg-[#0a0a0a]/80">
      <CircularProgress />
    </section>
  )
}

export { Loading }
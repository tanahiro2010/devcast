import { Outlet } from "react-router-dom"
import { useQuery } from "@tanstack/react-query"
import { Sidebar } from "../../components/layout/Sidebar"
import { Loading } from "../../components/screen/Loading"
import { client } from "../../lib/api"
import { ProfileContext } from "../../contexts/ProfileContext"

const Layout = () => {
  const { data, isPending, error } = useQuery({
    queryKey: ["profile"],
    queryFn: client.auth.getProfile
  })

  if (isPending) return <Loading />
  if (error) return (
    <div></div>
  )

  return (
    <section className="flex min-h-screen">
      <ProfileContext.Provider value={data}>
        <Sidebar />
        <main className="flex-1 min-w-0 bg-[#0a0a0a] text-neutral-100 flex flex-col items-center">
          <Outlet />
        </main>
      </ProfileContext.Provider>
    </section>
  )
}

export default Layout
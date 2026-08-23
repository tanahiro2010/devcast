import type { Profile } from "../../types/profile"
import { createContext } from "react"
import { Outlet } from "react-router-dom"
import { useQuery } from "@tanstack/react-query"
import { Sidebar } from "../../components/layout/Sidebar"
import { Loading } from "../../components/screen/Loading"
import { client } from "../../lib/api"

const ProfileContext = createContext<undefined | Profile>("profile")

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
    <section>
      <ProfileContext.Provider value={data}>
        <Sidebar />
        <main>
          <Outlet />
        </main>
      </ProfileContext.Provider>
    </section>
  )
}

export default Layout
export { ProfileContext }
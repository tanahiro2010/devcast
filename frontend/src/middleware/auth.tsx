import { useQuery } from "@tanstack/react-query"
import { Navigate, Outlet } from "react-router-dom"
import { client } from "../lib/api"
import { Loading } from "../components/screen/Loading"

const AuthMiddleware = () => {
  const refreshToken = localStorage.getItem("refresh_token")
  const hasAccessToken = !!sessionStorage.getItem("access_token")

  const { isPending, isError } = useQuery({
    queryKey: ["auth", "access_token"],
    queryFn: async () => {
      const accessToken = await client.auth.getAccessToken()
      sessionStorage.setItem("access_token", accessToken)
      return accessToken
    },
    enabled: !!refreshToken && !hasAccessToken,
    retry: false,
    gcTime: 0,
  })

  if (!refreshToken) {
    return <Navigate to="/_auth" replace />
  }

  if (hasAccessToken) {
    return <Outlet />
  }

  if (isPending) {
    return <Loading />
  }

  if (isError) {
    localStorage.removeItem("refresh_token")
    sessionStorage.removeItem("access_token")
    return <Navigate to="/_auth" replace />
  }

  return <Outlet />
}

export default AuthMiddleware
import { useQuery } from "@tanstack/react-query"
import { Navigate, Outlet } from "react-router-dom"
import { client } from "../lib/api"
import { Loading } from "../components/screen/Loading"
import { useAuth } from "../contexts/AuthContext"

const AuthMiddleware = () => {
  const refreshToken = localStorage.getItem("refresh_token")
  const { accessToken, setAccessToken } = useAuth()
  const hasAccessToken = !!accessToken

  const { isPending, isError } = useQuery({
    queryKey: ["auth", "access_token"],
    queryFn: async () => {
      const accessToken = await client.auth.getAccessToken()
      setAccessToken(accessToken)
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
    setAccessToken(null)
    return <Navigate to="/_auth" replace />
  }

  return <Outlet />
}

export default AuthMiddleware
import { useQuery } from "@tanstack/react-query"
import { client } from "../lib/api"
import { useAuth } from "../contexts/AuthContext"

type AuthState = "authenticated" | "unauthenticated" | "pending"

const useAuthState = (): AuthState => {
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
    return "unauthenticated"
  }

  if (hasAccessToken) {
    return "authenticated"
  }

  if (isPending) {
    return "pending"
  }

  if (isError) {
    localStorage.removeItem("refresh_token")
    setAccessToken(null)
    return "unauthenticated"
  }

  return "authenticated"
}

export { useAuthState }
export type { AuthState }

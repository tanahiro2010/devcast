import type { ApiResponse } from "../types/response"
import { useEffect, useState } from "react"
import { useNavigate } from "react-router-dom"
import { getConfig } from "../config/config"
import { Loading } from "../components/screen/Loading"

type RefreshTokenResponseData = {
  token: string;
  refresh_token: string;
  expiresIn: number;
}

const AuthMiddleware = () => {
  const navigate = useNavigate()
  const [isLoading, setIsLoading] = useState<boolean>(true)
  const [_, setError] = useState<Error | null>(null)

  useEffect(() => {
    // Check if user is authenticated
    const fetchAccessToken = async (token: string) => {
      const config = getConfig()
      const response = await fetch(`${config.apiBaseUrl}/auth/refresh`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Authorization": `Bearer ${token}`
        }
      })
      const data: ApiResponse<RefreshTokenResponseData> = await response.json()

      setIsLoading(false)

      if ("details" in data) {
        console.error(data)
        localStorage.removeItem("token")
        return navigate("/_auth")
      }

      sessionStorage.setItem("token", data.data.token)
      localStorage.setItem("token", data.data.refresh_token)
    }

    const token = localStorage.getItem("token")
    if (!token) {
      navigate("/_auth")
    }

    const accessToken = sessionStorage.getItem('token')
    if (!accessToken) {
      try {
        fetchAccessToken(token!)
      } catch (e) {
        console.log(e)
        setError(e as Error)
        setIsLoading(false)
      }
    }


  }, [navigate])

  if (isLoading) return <Loading />
  return null
}

export default AuthMiddleware
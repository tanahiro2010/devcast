import { useEffect, useState, useMemo } from "react"
import { getConfig } from "../config/config"
import { Loading } from "../components/screen/Loading"
import { type ApiResponse } from "../types/response"

type OAuthResponseData = {
  url: string
}

const Auth = () => {
  const [isLoading, setIsLoading] = useState<boolean>(true)
  const [endpoint, setEndpoint] = useState<string | null>(null)
  const [error, setError] = useState<Error | null>(null)
  const config = useMemo(() => getConfig(), [])

  useEffect(() => {
    const fetchOAuthEndpoint = async () => {
      const response = await fetch(`${config.apiBaseUrl}/auth`)
      const data: ApiResponse<OAuthResponseData> = await response.json()
      
      if (response.ok && "data" in data) {
        const { url } = data.data
        console.log(`URL: ${url}`)
        setEndpoint(url)
      } else {
        console.error(data)
        setError(new Error(data.message, { cause: data }))
      }

      setIsLoading(false)
    }

    fetchOAuthEndpoint()
  }, [config])

  if (isLoading || !endpoint) return <Loading />
  if (error) return (
    <section></section>
  )
  return (
    <section>
      <a href={endpoint}>GitHubでログイン</a>
    </section>
  )
}

export default Auth
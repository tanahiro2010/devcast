import { useQuery } from "@tanstack/react-query"
import { useSearchParams, useNavigate } from "react-router-dom"
import { useEffect } from "react"
import { client } from "../../lib/api"
import { Loading } from "../../components/screen/Loading"


const Callback = () => {
  const navigate = useNavigate()
  const [searchParams, _] = useSearchParams();
  const { data, isPending, error } = useQuery({
    queryKey: ["auth/callback"],
    queryFn: async () => {
      const token = searchParams.get('token')
      if (!token) throw new Error('トークンが設定されていません')
      sessionStorage.setItem('access_token', token)

      return await client.auth.getRefreshToken()
    }
  })

  useEffect(() => {
    if (error || !data) return

    const { accessToken, refreshToken } = data
    sessionStorage.setItem('access_token', accessToken)
    localStorage.setItem('refresh_token', refreshToken)

    navigate("/")
  }, [isPending])

  if (error || data) return (
    <div>
      Error
    </div>
  )

  return <Loading />
}

export default Callback
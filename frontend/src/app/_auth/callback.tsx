import { useQuery } from "@tanstack/react-query"
import { redirect, useSearchParams } from "react-router-dom"
import { useEffect } from "react"
import { client } from "../../lib/api"
import { Loading } from "../../components/screen/Loading"


const Callback = () => {
  const [searchParams, _] = useSearchParams();
  const { isPending, error } = useQuery({
    queryKey: ["auth/callback"],
    queryFn: async () => {
      const token = searchParams.get('token')
      if (!token) throw new Error('トークンが設定されていません')
      sessionStorage.setItem('access_token', token)

      const result = await client.auth.getRefreshToken()
      return localStorage.setItem('refresh_token', result)
    }
  })

  useEffect(() => {
    redirect('/')
  }, [isPending])

  if (isPending) return <Loading />
  else if (error) return (
    <div>
      Error
    </div>
  )
}

export default Callback
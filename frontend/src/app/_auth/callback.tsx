import { useQuery } from "@tanstack/react-query"
import { useSearchParams, useNavigate, Link } from "react-router-dom"
import { useEffect, useState } from "react"
import { Box, Button, Container, Stack, Typography } from "@mui/material"
import { client } from "../../lib/api"
import { Loading } from "../../components/screen/Loading"


const Callback = () => {
  const navigate = useNavigate()
  const [searchParams, _] = useSearchParams();
  const [isSessionNotFound, setIsSessionNotFound] = useState<boolean>(false)
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
    if (error) {
      if (error.message === "SESSION_NOT_FOUND") {
        setIsSessionNotFound(true)
      }
      return  
    }
    if (!data) return

    const { accessToken, refreshToken } = data
    sessionStorage.setItem('access_token', accessToken)
    localStorage.setItem('refresh_token', refreshToken)

    navigate("/")
  }, [isPending])

  if (error) {
    if (isSessionNotFound) return (
      <section>
        <Box sx={{ bgcolor: "background.default", minHeight: "100vh", display: "flex", alignItems: "center" }}>
          <Container maxWidth="xs">
            <Stack spacing={4} sx={{ alignItems: "center" }}>
              <Typography variant="h2" sx={{ fontWeight: 500, color: "text.primary" }}>
                :(
              </Typography>

              <Stack spacing={1} sx={{ alignItems: "center", textAlign: "center", width: "100%" }}>
                <Typography variant="h5" component="h1">
                  セッションが見つかりませんでした
                </Typography>
                <Typography variant="body1" color="text.secondary">
                  ログイン情報の有効期限が切れているか、無効になっている可能性があります。もう一度ログインをお試しください。
                </Typography>
              </Stack>

              <Button component={Link} to="/_auth" variant="contained" color="primary" size="large">
                ログインページに戻る
              </Button>
            </Stack>
          </Container>
        </Box>
      </section>
    )

    return (
      <section>
        <Box sx={{ bgcolor: "background.default", minHeight: "100vh", display: "flex", alignItems: "center" }}>
          <Container maxWidth="xs">
            <Stack spacing={4} sx={{ alignItems: "center" }}>
              <Typography variant="h2" sx={{ fontWeight: 500, color: "text.primary" }}>
                :(
              </Typography>

              <Stack spacing={1} sx={{ alignItems: "center", textAlign: "center", width: "100%" }}>
                <Typography variant="h5" component="h1">
                  ログインに失敗しました
                </Typography>
                <Typography variant="body1" color="text.secondary">
                  {error.message || "処理中に問題が発生しました。時間をおいて再度お試しください。"}
                </Typography>
              </Stack>

              <Button component={Link} to="/_auth" variant="contained" color="primary" size="large">
                ログインページに戻る
              </Button>
            </Stack>
          </Container>
        </Box>
      </section>
    )
  }

  if (!data) return (
    <section></section>
  )

  return <Loading />
}

export default Callback
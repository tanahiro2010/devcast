import type { ApiResponse } from "../types/response"
import { useEffect, useState, useMemo } from "react"
import { getConfig } from "../config/config"
import {
  Alert,
  Box,
  Button,
  Container,
  Divider,
  Stack,
  SvgIcon,
  Typography,
} from "@mui/material"
import { Loading } from "../components/screen/Loading"

type OAuthResponseData = {
  url: string
}

const GitHubIcon = () => (
  <SvgIcon viewBox="0 0 24 24" fontSize="small">
    <path d="M12 .5C5.65.5.5 5.65.5 12c0 5.09 3.29 9.4 7.86 10.93.57.1.79-.25.79-.55 0-.27-.01-1.17-.02-2.12-3.2.7-3.88-1.36-3.88-1.36-.52-1.34-1.28-1.69-1.28-1.69-1.04-.72.08-.7.08-.7 1.16.08 1.77 1.19 1.77 1.19 1.03 1.77 2.7 1.26 3.36.96.1-.75.4-1.26.73-1.55-2.56-.29-5.26-1.28-5.26-5.7 0-1.26.45-2.29 1.19-3.1-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.18 1.18a11 11 0 0 1 5.8 0c2.2-1.49 3.17-1.18 3.17-1.18.64 1.59.24 2.76.12 3.05.74.81 1.18 1.84 1.18 3.1 0 4.43-2.7 5.4-5.28 5.69.42.36.78 1.07.78 2.16 0 1.56-.01 2.82-.01 3.2 0 .3.21.66.8.55A11.5 11.5 0 0 0 23.5 12c0-6.35-5.15-11.5-11.5-11.5Z" />
  </SvgIcon>
)

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

    try {
      fetchOAuthEndpoint()
    } catch (e) {
      console.error(e)
      setError(e as Error)
      setIsLoading(false)
    }
  }, [config])

  if (isLoading) return <Loading />

  return (
    <Box sx={{ bgcolor: "background.default", minHeight: "100vh", display: "flex", alignItems: "center" }}>
      <Container maxWidth="xs">
        <Stack spacing={4} sx={{ alignItems: "center" }}>
          <Stack spacing={1} sx={{ alignItems: "center", textAlign: "center", width: "100%", fontWeight: "bold" }}>
            <Typography variant="h4" component="h1">
              DevCast にログイン
            </Typography>
            <Typography variant="body1" color="text.secondary">
              続行するには GitHub アカウントを使用してください
            </Typography>
          </Stack>

          {error ? (
            <Stack spacing={2} sx={{ width: "100%" }}>
              <Alert severity="error">ログイン情報の取得に失敗しました。時間をおいて再度お試しください。</Alert>
              <Button variant="outlined" fullWidth onClick={() => window.location.reload()}>
                再読み込み
              </Button>
            </Stack>
          ) : (
            <Button
              component="a"
              href={endpoint ?? "#"}
              variant="contained"
              color="primary"
              fullWidth
              size="large"
              startIcon={<GitHubIcon />}
              disabled={!endpoint}
            >
              GitHub でログイン
            </Button>
          )}

          <Divider flexItem sx={{ width: "100%" }} />

          <Typography variant="caption" color="text.secondary" sx={{ textAlign: "center", width: "100%" }}>
            ログインすることで、DevCast の利用規約に同意したものとみなされます。
          </Typography>
        </Stack>
      </Container>
    </Box>
  )
}

export default Auth
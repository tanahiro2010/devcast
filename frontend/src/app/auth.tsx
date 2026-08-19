import { useQuery } from "@tanstack/react-query"
import { client } from "../lib/api"
import {
  Alert,
  Box,
  Button,
  Container,
  Divider,
  Stack,
  Typography,
} from "@mui/material"
import { Loading } from "../components/screen/Loading"
import { GitHubIcon } from "../components/icons/github"

const Auth = () => {
  const { data, isPending, error } = useQuery({
    queryKey: ["auth/get_url"],
    queryFn: client.auth.getAuthUrl
  })

  if (isPending) return <Loading />

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
              href={data}
              variant="contained"
              color="primary"
              fullWidth
              size="large"
              startIcon={<GitHubIcon />}
              disabled={!data}
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
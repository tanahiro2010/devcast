import { Link } from "react-router-dom"
import { Box, Button, Container, Stack, Typography } from "@mui/material"

const NotFound = () => (
  <Box sx={{ bgcolor: "background.default", minHeight: "100vh", display: "flex", alignItems: "center" }}>
    <Container maxWidth="xs">
      <Stack spacing={4} sx={{ alignItems: "center" }}>
        <Typography variant="h2" sx={{ fontWeight: 500, color: "text.primary" }}>
          404
        </Typography>

        <Stack spacing={1} sx={{ alignItems: "center", textAlign: "center", width: "100%" }}>
          <Typography variant="h5" component="h1">
            ページが見つかりません
          </Typography>
          <Typography variant="body1" color="text.secondary">
            お探しのページは削除されたか、非公開になっている可能性があります。
          </Typography>
        </Stack>

        <Button component={Link} to="/_auth" variant="contained" color="primary" size="large">
          ログインページに戻る
        </Button>
      </Stack>
    </Container>
  </Box>
)

export default NotFound
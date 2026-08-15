import { Link } from "react-router-dom"
import { Button, Paper, Typography } from "@mui/material"
import { Center } from "../components/layout/Center"

const NotFound = () => (
  <section className="bg-[#f8f9fa]">
    <Center className="w-full h-screen px-4">
      <Paper
        elevation={0}
        className="w-full sm:w-[420px]"
        sx={{
          border: "1px solid #e0e0e0",
          borderRadius: "28px",
          px: { xs: 4, sm: 6 },
          py: 6,
        }}
      >
        <Center className="h-auto space-y-4 text-center">
          <Typography sx={{ fontSize: 56, fontWeight: 500, color: "#202124", lineHeight: 1 }}>
            404
          </Typography>

          <div className="space-y-1">
            <Typography component="h1" sx={{ fontSize: 20, fontWeight: 500, color: "#202124" }}>
              ページが見つかりません
            </Typography>
            <Typography sx={{ fontSize: 14, color: "#5f6368" }}>
              お探しのページは削除されたか、非公開になっている可能性があります。
            </Typography>
          </div>

          <Button
            component={Link}
            to="/_auth"
            variant="contained"
            disableElevation
            sx={{
              bgcolor: "#1a1a1a",
              textTransform: "none",
              fontSize: 15,
              px: 3,
              py: 1,
              borderRadius: "8px",
              "&:hover": { bgcolor: "#000" },
            }}
          >
            ログインページに戻る
          </Button>
        </Center>
      </Paper>
    </Center>
  </section>
)

export default NotFound
import { createTheme } from "@mui/material/styles"

const theme = createTheme({
  palette: {
    mode: "dark",
    primary: {
      main: "#ff5a1f",
    },
    text: {
      primary: "#f5f5f5",
      secondary: "#a3a3a3",
    },
    background: {
      default: "#0a0a0a",
      paper: "#111111",
    },
    divider: "rgba(255, 255, 255, 0.1)",
  },
  shape: {
    borderRadius: 8,
  },
  typography: {
    fontFamily: "'Google Sans', 'Roboto', 'Helvetica', 'Arial', sans-serif",
    h5: {
      fontWeight: 500,
    },
  },
  components: {
    MuiPaper: {
      styleOverrides: {
        root: {
          borderRadius: 28,
        },
        outlined: {
          borderColor: "rgba(255, 255, 255, 0.1)",
        },
      },
    },
    MuiButton: {
      defaultProps: {
        disableElevation: true,
      },
      styleOverrides: {
        root: {
          textTransform: "none",
          fontSize: 15,
          borderRadius: 8,
        },
        sizeMedium: {
          padding: "10px 24px",
        },
      },
    },
  },
})

export { theme }

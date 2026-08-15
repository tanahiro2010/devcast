import { createTheme } from "@mui/material/styles"

const theme = createTheme({
  palette: {
    primary: {
      main: "#1a73e8",
    },
    text: {
      primary: "#202124",
      secondary: "#5f6368",
    },
    background: {
      default: "#f8f9fa",
    },
    divider: "#dadce0",
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
          borderColor: "#dadce0",
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

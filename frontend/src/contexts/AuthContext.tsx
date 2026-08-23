import { createContext, useCallback, useContext, useState, type ReactNode } from "react"

interface AuthContextValue {
  accessToken: string | null
  setAccessToken: (token: string | null) => void
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined)

// apiFetch runs outside the React tree, so it can't call useContext.
// This mirror is kept in sync by AuthProvider's setAccessToken and lets
// apiFetch read the current token synchronously.
let accessTokenRef: string | null = null
const getAccessToken = () => accessTokenRef

const AuthProvider = ({ children }: { children: ReactNode }) => {
  const [accessToken, setAccessTokenState] = useState<string | null>(accessTokenRef)

  const setAccessToken = useCallback((token: string | null) => {
    accessTokenRef = token
    setAccessTokenState(token)
  }, [])

  return (
    <AuthContext.Provider value={{ accessToken, setAccessToken }}>
      {children}
    </AuthContext.Provider>
  )
}

const useAuth = (): AuthContextValue => {
  const context = useContext(AuthContext)
  if (!context) {
    throw new Error("useAuth must be used within an AuthProvider")
  }
  return context
}

export { AuthProvider, useAuth, getAccessToken }

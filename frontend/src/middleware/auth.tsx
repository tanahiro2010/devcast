import { Navigate, Outlet } from "react-router-dom"
import { Loading } from "../components/screen/Loading"
import { useAuthState } from "../hooks/useAuthState"

const AuthMiddleware = () => {
  const status = useAuthState()

  if (status === "pending") {
    return <Loading />
  }

  if (status === "unauthenticated") {
    return <Navigate to="/_auth" replace />
  }

  return <Outlet />
}

export default AuthMiddleware

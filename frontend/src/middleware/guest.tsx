import { Navigate, Outlet } from "react-router-dom"
import { Loading } from "../components/screen/Loading"
import { useAuthState } from "../hooks/useAuthState"

const GuestMiddleware = () => {
  const status = useAuthState()

  if (status === "pending") {
    return <Loading />
  }

  if (status === "authenticated") {
    return <Navigate to="/" replace />
  }

  return <Outlet />
}

export default GuestMiddleware

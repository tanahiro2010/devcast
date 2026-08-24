import type { Route } from "@util-tools/react-router-dsl"
import AuthMiddleware from "../middleware/auth"
import Layout from "../app/(home)/layout"
import NotFound from "../app/not-found"
import Callback from "../app/_auth/callback"
import Auth from "../app/auth"
import Home from "../app/(home)/home"
import { Navigate } from "react-router-dom"

const routes: Route[] = [
  { type: "page", path: "*", index: false, element: <NotFound /> },
  { type: "page", path: "signup", index: false, element: <Navigate to={"/_auth"}/> },
  { type: "page", path: "login", index: false, element: <Navigate to={"/_auth"}/> },
  {
    type: "group",
    path: "_auth",
    children: [
      {
        type: "page",
        index: true,
        element: <Auth />
      },
      {
        type: "page",
        index: false,
        path: "callback",
        element: <Callback />
      },
    ]
  },
  {
    type: "page",
    element: <NotFound />,
    index: false,
    path: "/not-found"
  },
  {
    type: "layout",
    element: <AuthMiddleware />,
    children: [
      {
        type: "layout",
        element: <Layout />,
        children: [
          { type: "page", index: true, element: <Home /> }
        ]
      },
    ]
  }
]

export { routes }
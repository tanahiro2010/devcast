import type { Route } from "@util-tools/react-router-dsl";
import AuthMiddleware from "../middleware/auth";
import NotFound from "../app/not-found";
import Callback from "../app/_auth/callback";
import Auth from "../app/auth";
import Home from "../app/home";

const routes: Route[] = [
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
      { type: "page", index: true,  element: <Home /> },
      { type: "page", path: "*", index: false, element: <NotFound /> }
    ]
  }
];

export { routes };
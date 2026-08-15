import type { Route } from "@util-tools/react-router-dsl";
import Callback from "../app/_auth/callback";
import Auth from "../app/auth";

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
];

export { routes };
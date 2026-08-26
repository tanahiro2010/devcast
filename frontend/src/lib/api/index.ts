import { AuthApi } from "./auth"
import { client as v1Client } from "./v1"

class Client {
  readonly auth = new AuthApi()
  readonly v1 = v1Client
}

const client = new Client()

export { Client, client }
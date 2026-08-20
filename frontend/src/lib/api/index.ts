import { AuthApi } from "./auth"

class Client {
  readonly auth = new AuthApi()
}

const client = new Client()

export { Client, client }
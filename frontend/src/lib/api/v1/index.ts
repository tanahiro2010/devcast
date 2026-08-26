import { ProvidersApi } from './providers'

class Client {
  readonly providers = new ProvidersApi()
}

const client = new Client()

export { Client, client }
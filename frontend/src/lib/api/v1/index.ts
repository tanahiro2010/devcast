import { ProvidersApi } from './providers'
import { ArticlesApi } from './articles'

class Client {
  readonly providers = new ProvidersApi()
  readonly articles = new ArticlesApi()
}

const client = new Client()

export { Client, client }
import type { Provider } from "../../../types/api"
import { apiFetch } from "../client"

type _ProvidersApi = {
  getProviders: () => Promise<Provider[]>
  registerProvider: (provider: string, token: string, expireAt: Date) => Promise<void>
  deleteProvider: (provider: number) => Promise<void>
}

class ProvidersApi implements _ProvidersApi {
  async getProviders(): Promise<Provider[]> {
    const response = await apiFetch('/v1/providers', {
      method: 'GET'
    })
    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.details.message || 'Failed to get providers')
    }

    return data.data.providers as Provider[]
  }

  async getProvider(provider: string): Promise<Provider | null> {
    const response = await apiFetch(`/v1/providers/${provider}`, {
      method: 'GET'
    })
    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.details.message || 'Failed to get provider')
    }

    return data.data.provider as Provider | null
  }

  async registerProvider(provider: string, token: string, expireAt: Date): Promise<void> {
    const response = await apiFetch('/v1/providers', {
      method: 'POST',
      body: JSON.stringify({
        provider,
        token,
        expires_at: expireAt.toISOString()
      })
    })
    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.details.message || 'Failed to register provider')
    }
  }

  async deleteProvider(providerId: number): Promise<void> {
    const response = await apiFetch(`/v1/providers/${providerId}`, {
      method: 'DELETE'
    })
    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.details.message || 'Failed to delete provider')
    }
  }
}

export { ProvidersApi }

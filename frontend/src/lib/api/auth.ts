import { apiFetch } from "./client"

class AuthApi {
  constructor() {

  }
  async getAuthUrl(): Promise<string> {
    const response = await apiFetch('/auth')
    const data = await response.json()
    return data.url
  }

  async getRefreshToken(): Promise<string> {
    const response = await apiFetch('/api/refresh', {
      method: 'POST'
    })
    const data = await response.json()

    return data.refresh_token
  }

  async getAccessToken(): Promise<string> {
    const response = await apiFetch('/api/access', {
      method: 'POST',
      body: JSON.stringify({ refresh_token: localStorage.getItem('refresh_token') })
    })
    const data = await response.json()
    return data.access_token
  }
}

export { AuthApi }
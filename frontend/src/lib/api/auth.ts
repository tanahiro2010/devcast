import { apiFetch } from "./client"

class AuthApi {
  constructor() {

  }
  async getAuthUrl(): Promise<string> {
    const response = await apiFetch('/auth')
    const data = await response.json()
    return data.data.url
  }

  async getRefreshToken(): Promise<{ refreshToken: string, accessToken: string }> {
    const response = await apiFetch('/auth/token/refresh_token', {
      method: 'GET',
    })
    const data = await response.json()

    return { refreshToken: data.data.refresh_token, accessToken: data.data.access_token }
  }

  async getAccessToken(): Promise<string> {
    const response = await apiFetch('/auth/token/access_token', {
      method: 'POST',
      body: JSON.stringify({ grant_type: 'refresh_token', refresh_token: localStorage.getItem('refresh_token') })
    })
    const data = await response.json()
    return data.access_token
  }
}

export { AuthApi }
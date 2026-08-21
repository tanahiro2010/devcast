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
    const response = await apiFetch('/auth/refresh', {
      method: 'POST'
    })
    const data = await response.json()

    return { refreshToken: data.refresh_token, accessToken: data.access_token }
  }

  async getAccessToken(): Promise<string> {
    const response = await apiFetch('/access', {
      method: 'POST',
      body: JSON.stringify({ refresh_token: localStorage.getItem('refresh_token') })
    })
    const data = await response.json()
    return data.access_token
  }
}

export { AuthApi }
import type { Profile } from "../../types/api"
import { apiFetch } from "./client"

type _AuthApi = {
  getAuthUrl: () => Promise<string>
  getRefreshToken: () => Promise<{ refreshToken: string, accessToken: string }>
  getAccessToken: () => Promise<string>
  getProfile: () => Promise<Profile>
}

class AuthApi implements _AuthApi {
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
    if (!response.ok) {
      throw new Error(data.details.code || 'Failed to get refresh token')
    }

    return { refreshToken: data.data.refresh_token, accessToken: data.data.access_token }
  }

  async getAccessToken(): Promise<string> {
    const refreshToken = localStorage.getItem('refresh_token')
    if (!refreshToken) {
      throw new Error('Refresh token not found')
    }

    const path = '/auth/token/access_token?refresh_token=' + encodeURIComponent(refreshToken)
    const response = await apiFetch(path, {
      method: 'GET',
    })
    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.details.message || 'Failed to get access token')
    }

    return data.data.access_token
  }

  async getProfile(): Promise<Profile> {
    const response = await apiFetch('/auth/profile', {
      method: 'GET'
    })
    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.details.message || 'Failed to get profile')
    }

    return data.data.profile as Profile
  }
}

export { AuthApi }
import { getConfig } from "../../config/config"

const apiFetch = async (path: string, options: RequestInit = {}): Promise<Response> => {
  const { apiBaseUrl } = getConfig()

  const url = apiBaseUrl + path
  const accessToken = sessionStorage.getItem('access_token')
  const response = await fetch(url, Object.assign({ 
    headers: { 
      'Content-Type': 'application/json',
      ...(accessToken ? { 'Authorization': 'Bearer ' + accessToken } : {})
    }
  }, options))

  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`)
  }

  return response
}

export { apiFetch }
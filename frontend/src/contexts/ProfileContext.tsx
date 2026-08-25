import { createContext, useContext } from "react"
import type { Profile } from "../types/api"

const ProfileContext = createContext<Profile | undefined>(undefined)

const useProfile = (): Profile => {
  const context = useContext(ProfileContext)
  if (!context) {
    throw new Error("useProfile must be used within a ProfileContext.Provider")
  }
  return context
}

export { ProfileContext, useProfile }

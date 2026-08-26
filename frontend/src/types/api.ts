type Subscription = {
  stripe_subscription_id: string
  stripe_price_id:        string
  
  status: string

  current_period_start: Date
  current_period_end:   Date
}

type Provider = {
  id: number
  provider: string
  expires_at: Date
}

type Profile = {
  id:       number
  username: string
  email:    string
  subscription: Subscription | null
  providers: Provider[]
}

type Article = {
  id: number
  
  title: string
  tags: string[]
  content: string

  status: {
    provider: string
    status: "published" | "draft" | "pending"
  }[]

  updated_at: Date
  created_at: Date
}

export type { Profile, Subscription, Provider, Article }
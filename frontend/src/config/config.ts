const isProduction = import.meta.env.PROD

type BaseConfig<T extends boolean> = {
  apiBaseUrl: string
  isProduction: T
}

type ConfigT = {
  production: BaseConfig<true>
  development: BaseConfig<false>
}


const Config: ConfigT = {
  production: {
    apiBaseUrl: 'https://api.devcast.work',
    isProduction: true
  },
  development: {
    apiBaseUrl: 'http://localhost:8000',
    isProduction: false
  }
}

const getConfig = () => {
  if (isProduction) {
    return Config.production;
  } else {
    return Config.development;
  }
}

export { getConfig }
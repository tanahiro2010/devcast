const isProduction = import.meta.env.PROD;

const Config = {
  production: {
    apiBaseUrl: 'https://api.devcast.work'
  },
  development: {
    apiBaseUrl: 'http://localhost:3000'
  }
}

const getConfig = () => {
  if (isProduction) {
    return Config.production;
  } else {
    return Config.development;
  }
}

export { getConfig };
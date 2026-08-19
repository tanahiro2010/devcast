import { QueryClient, QueryClientProvider } from '@tanstack/react-query'
import { ReactQueryDevtools } from '@tanstack/react-query-devtools'
import { RouteProvider } from '@util-tools/react-router-dsl'
import { routes } from './config/routes'
import './App.css'

const client = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 1000 * 60 * 5,
      gcTime: 1000 * 60 * 10,
      retry: 2,
    }
  },
})

function App() {
  return (
    <QueryClientProvider client={client}>
      <RouteProvider routes={routes} />
      <ReactQueryDevtools />
    </QueryClientProvider>
  )
}

export default App

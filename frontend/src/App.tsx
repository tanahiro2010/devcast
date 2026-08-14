import { RouteProvider } from '@util-tools/react-router-dsl'
import { routes } from './config/routes'
import './App.css'

function App() {
  return <RouteProvider routes={routes} />
}

export default App

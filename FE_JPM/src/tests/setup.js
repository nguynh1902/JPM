import { config } from '@vue/test-utils'

// Mock global plugins or components if necessary
config.global.stubs = {
  'v-select': true,
  'router-link': true
}

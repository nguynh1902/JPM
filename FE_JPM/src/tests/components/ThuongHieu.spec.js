import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import ThuongHieu from '@/views/admin/MayThuy/ThuongHieu.vue'
import { createTestingPinia } from '@pinia/testing'
import { VueQueryPlugin } from '@tanstack/vue-query'

describe('ThuongHieu.vue', () => {
  it('renders table headers correctly', () => {
    const wrapper = mount(ThuongHieu, {
      global: {
        plugins: [
            createTestingPinia({
                initialState: {
                    auth: { user: { id: 1, name: 'Admin' } }
                }
            }),
            VueQueryPlugin
        ],
        stubs: ['v-select', 'router-link']
      }
    })

    expect(wrapper.text()).toContain('Mã Thương Hiệu')
    expect(wrapper.text()).toContain('Tên Thương Hiệu')
    expect(wrapper.text()).toContain('Trạng thái')
  })
})

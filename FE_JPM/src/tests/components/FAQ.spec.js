import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import FAQ from '@/views/admin/NoiDung/FAQ.vue'
import { createTestingPinia } from '@pinia/testing'
import { VueQueryPlugin } from '@tanstack/vue-query'

describe('FAQ.vue', () => {
  it('renders table headers correctly', () => {
    const wrapper = mount(FAQ, {
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

    expect(wrapper.text()).toContain('Câu Hỏi & Trả Lời')
    expect(wrapper.text()).toContain('Chuyên Mục')
    expect(wrapper.text()).toContain('Trạng Thái')
    expect(wrapper.text()).toContain('Hành Động')
  })
})

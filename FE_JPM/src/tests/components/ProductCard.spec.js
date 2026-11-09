import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { createTestingPinia } from '@pinia/testing';
import ProductCard from '@/components/client/ProductCard.vue';

const routerLinkStub = {
  template: '<a><slot /></a>'
};

describe('ProductCard.vue', () => {
  it('renders skeleton correctly in loading state', () => {
    const wrapper = mount(ProductCard, {
      props: {
        loading: true,
      },
      global: {
        plugins: [createTestingPinia()],
        stubs: {
          'router-link': routerLinkStub,
        },
      },
    });

    expect(wrapper.find('.skeleton-box').exists()).toBe(true);
    expect(wrapper.findAll('.skeleton-line').length).toBeGreaterThan(0);
  });

  it('renders a priced product correctly and emits add-to-cart on button click', async () => {
    const product = {
      id: 101,
      ma_san_pham: 'SKU-101',
      ten_san_pham: 'Bugi Thủy NGK BPR6ES',
      slug: 'bugi-thuy-ngk-bpr6es',
      gia_ban: 120000,
      is_gia_lien_he: false,
      hinh_anh_chinh: '/images/bugi.jpg',
      danh_muc: { ten_danh_muc: 'Phụ Tùng Bugi' },
    };

    const wrapper = mount(ProductCard, {
      props: { product },
      global: {
        plugins: [createTestingPinia()],
        stubs: {
          'router-link': routerLinkStub,
        },
      },
    });

    expect(wrapper.text()).toContain('Bugi Thủy NGK BPR6ES');
    expect(wrapper.text()).toContain('SKU-101');
    expect(wrapper.text()).toContain('Phụ Tùng Bugi');
    expect(wrapper.text()).toContain('120.000 đ');

    const button = wrapper.find('.btn-cart-action');
    expect(button.exists()).toBe(true);
    expect(button.attributes('title')).toContain('Thêm Bugi Thủy NGK BPR6ES vào giỏ hàng');
    expect(button.find('.bi-cart2').exists()).toBe(true);

    await button.trigger('click');
    expect(wrapper.emitted('add-to-cart')).toBeTruthy();
    expect(wrapper.emitted('add-to-cart')[0][0]).toEqual(product);
  });

  it('renders a quote-required product with balanced quote button', async () => {
    const product = {
      id: 202,
      ma_phu_tung: 'OEM-YAM-999',
      ten_phu_tung: 'Hộp Số Động Cơ Yamaha V8',
      gia_ban: 0,
      is_gia_lien_he: true,
      hinh_anh: '',
      thuong_hieu: { name: 'Yamaha Marine' },
    };

    const wrapper = mount(ProductCard, {
      props: { product },
      global: {
        plugins: [createTestingPinia()],
        stubs: {
          'router-link': routerLinkStub,
        },
      },
    });

    expect(wrapper.text()).toContain('Hộp Số Động Cơ Yamaha V8');
    expect(wrapper.text()).toContain('OEM-YAM-999');
    expect(wrapper.text()).toContain('Liên hệ');

    const button = wrapper.find('.btn-quote-action');
    expect(button.exists()).toBe(true);
    expect(button.attributes('title')).toContain('Yêu cầu báo giá cho Hộp Số Động Cơ Yamaha V8');
    expect(button.find('.bi-file-earmark-text').exists()).toBe(true);
  });
});

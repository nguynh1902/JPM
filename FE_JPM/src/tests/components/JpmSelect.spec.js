import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import JpmSelect from '@/components/client/JpmSelect.vue';

describe('JpmSelect.vue', () => {
  const options = [
    { id: 1, name: 'Yamaha' },
    { id: 2, name: 'Honda' },
    { id: 3, name: 'Mercury' },
  ];

  it('renders placeholder when no value is selected', () => {
    const wrapper = mount(JpmSelect, {
      props: {
        modelValue: null,
        options,
        placeholder: 'Chọn thương hiệu',
      },
    });

    expect(wrapper.text()).toContain('Chọn thương hiệu');
    expect(wrapper.find('.custom-dropdown-panel').exists()).toBe(false);
  });

  it('opens dropdown on box click and emits update:modelValue when an option is clicked', async () => {
    const wrapper = mount(JpmSelect, {
      props: {
        modelValue: null,
        options,
        placeholder: 'Chọn thương hiệu',
      },
    });

    const box = wrapper.find('.custom-select-box');
    await box.trigger('click');

    expect(wrapper.find('.custom-dropdown-panel').exists()).toBe(true);

    const items = wrapper.findAll('.dropdown-item-option');
    expect(items.length).toBe(3);
    expect(items[0].text()).toContain('Yamaha');

    await items[0].trigger('click');
    expect(wrapper.emitted('update:modelValue')).toBeTruthy();
    expect(wrapper.emitted('update:modelValue')[0]).toEqual([1]);
  });

  it('filters options with search field when searchable is true', async () => {
    const wrapper = mount(JpmSelect, {
      props: {
        modelValue: null,
        options,
        searchable: true,
      },
    });

    await wrapper.find('.custom-select-box').trigger('click');
    const searchInput = wrapper.find('.dropdown-search-box input');
    expect(searchInput.exists()).toBe(true);

    await searchInput.setValue('Hon');
    const items = wrapper.findAll('.dropdown-item-option');
    expect(items.length).toBe(1);
    expect(items[0].text()).toContain('Honda');
  });

  it('displays correct label for primitive options', () => {
    const wrapper = mount(JpmSelect, {
      props: {
        modelValue: 2024,
        options: [2022, 2023, 2024],
      },
    });

    expect(wrapper.find('.select-display-text').text()).toContain('2024');
  });
});

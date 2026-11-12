<template>
  <div class="jpm-select-container" :class="{ 'is-active': isOpen }" ref="selectContainerRef">
    <!-- Nhãn phía trên nếu có -->
    <label v-if="label" class="filter-label mb-2 d-flex align-items-center justify-content-between">
      <span>{{ label }}</span>
      <span v-if="loading" class="spinner-border spinner-border-sm text-danger ms-2" role="status"></span>
    </label>

    <div
      class="custom-select-wrapper position-relative"
      :class="{ 'z-active': isOpen, 'is-disabled': disabled }"
    >
      <!-- Khung hiển thị giá trị được chọn (Toggle Box) -->
      <div
        class="custom-select-box d-flex align-items-center justify-content-between"
        :class="{
          'is-focused': isOpen,
          'has-value': hasSelectedValue,
          'is-disabled': disabled,
          'is-loading': loading,
          'has-error': !!error
        }"
        tabindex="0"
        role="combobox"
        :aria-expanded="isOpen"
        :aria-disabled="disabled"
        @click="toggleDropdown"
        @keydown="handleKeyDown"
      >
        <!-- Text giá trị đang chọn hoặc Placeholder -->
        <span class="select-display-text text-truncate">
          <template v-if="loading">
            <span class="text-muted"><i class="bi bi-arrow-repeat spin me-1"></i>Đang tải...</span>
          </template>
          <template v-else-if="hasSelectedValue">
            {{ selectedOptionLabel }}
          </template>
          <template v-else>
            <span class="text-placeholder">{{ placeholder }}</span>
          </template>
        </span>

        <!-- Icon bên phải: Mũi tên [v] -->
        <div class="select-actions d-flex align-items-center ms-2">
          <i
            class="bi bi-chevron-down select-arrow"
            :class="{ 'rotated': isOpen }"
          ></i>
        </div>
      </div>

      <!-- Menu Dropdown Xổ Xuống -->
      <transition name="dropdown-fade">
        <div
          v-if="isOpen && !disabled"
          class="custom-dropdown-panel position-absolute w-100 shadow-lg"
          role="listbox"
        >
          <!-- Ô tìm kiếm nếu danh sách dài hoặc searchable = true -->
          <div v-if="isSearchVisible" class="dropdown-search-box p-2 border-bottom">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search text-muted"></i>
              </span>
              <input
                ref="searchInputRef"
                type="text"
                class="form-control form-control-sm border-start-0 ps-0 shadow-none"
                placeholder="Tìm kiếm..."
                v-model="searchQuery"
                @click.stop
                @keydown.stop="handleSearchKeyDown"
              />
              <button
                v-if="searchQuery"
                type="button"
                class="btn btn-outline-secondary border-start-0 bg-white"
                @click.stop="searchQuery = ''"
              >
                <i class="bi bi-x"></i>
              </button>
            </div>
          </div>

          <!-- Danh sách Options -->
          <ul class="jpm-select-options custom-dropdown-list list-unstyled mb-0 custom-scrollbar">
            <!-- Mục Tất cả / Bỏ chọn nếu có allOptionLabel -->
            <li
              v-if="allOptionLabel && !searchQuery"
              class="dropdown-item-option"
              :class="{ 'active': !hasSelectedValue }"
              @click="handleSelect('')"
            >
              {{ allOptionLabel }}
            </li>

            <!-- Trạng thái không có kết quả -->
            <li v-if="filteredOptions.length === 0" class="dropdown-item-empty text-center py-3 text-muted">
              <i class="bi bi-inbox d-block mb-1" style="font-size: 1.25rem;"></i>
              <span>{{ emptyText || 'Không tìm thấy dữ liệu' }}</span>
            </li>

            <!-- Các Options hợp lệ -->
            <li
              v-for="(opt, idx) in filteredOptions"
              :key="getOptionValue(opt)"
              class="dropdown-item-option d-flex align-items-center justify-content-between"
              :class="{
                'active': isOptionSelected(opt),
                'highlighted': highlightedIndex === idx,
                'disabled': opt.disabled
              }"
              @click="!opt.disabled && handleSelect(opt)"
              @mouseenter="highlightedIndex = idx"
            >
              <div class="option-text-wrap text-truncate me-2">
                <span class="option-label text-truncate">{{ getOptionLabel(opt) }}</span>
                <small v-if="opt.subtitle" class="option-subtitle d-block text-muted">{{ opt.subtitle }}</small>
              </div>
              <span v-if="opt.badge" class="badge bg-light text-dark jpm-badge-sm">{{ opt.badge }}</span>
            </li>
          </ul>
        </div>
      </transition>
    </div>

    <!-- Thông báo lỗi nếu có -->
    <div v-if="error" class="error-feedback text-danger mt-1 small">
      <i class="bi bi-exclamation-circle me-1"></i>{{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number, null],
    default: ''
  },
  options: {
    type: Array,
    default: () => []
  },
  placeholder: {
    type: String,
    default: 'Vui lòng chọn...'
  },
  label: {
    type: String,
    default: ''
  },
  allOptionLabel: {
    type: String,
    default: ''
  },
  valueKey: {
    type: String,
    default: 'id'
  },
  labelKey: {
    type: String,
    default: 'name'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  searchable: {
    type: Boolean,
    default: false
  },
  clearable: {
    type: Boolean,
    default: true
  },
  error: {
    type: String,
    default: ''
  },
  emptyText: {
    type: String,
    default: 'Không có dữ liệu'
  }
});

const emit = defineEmits(['update:modelValue', 'change', 'clear']);

const selectContainerRef = ref(null);
const searchInputRef = ref(null);
const isOpen = ref(false);
const searchQuery = ref('');
const highlightedIndex = ref(-1);

// ─── HELPERS ĐỌC OPTIONS ĐA DẠNG ──────────────────────────────
function getOptionValue(opt) {
  if (opt === null || opt === undefined) return '';
  if (typeof opt === 'string' || typeof opt === 'number') return opt;
  if (opt.value !== undefined) return opt.value;
  if (opt[props.valueKey] !== undefined) return opt[props.valueKey];
  return opt.id !== undefined ? opt.id : '';
}

function getOptionLabel(opt) {
  if (opt === null || opt === undefined) return '';
  if (typeof opt === 'string' || typeof opt === 'number') return String(opt);
  if (opt.label !== undefined) return opt.label;
  if (opt[props.labelKey] !== undefined) return String(opt[props.labelKey]);
  return opt.name || opt.ten_loai_dong_co || opt.ten_model || opt.ten_danh_muc || String(getOptionValue(opt));
}

function isOptionSelected(opt) {
  const val = getOptionValue(opt);
  return String(val) === String(props.modelValue);
}

// ─── COMPUTED ────────────────────────────────────────────────
const hasSelectedValue = computed(() => {
  return props.modelValue !== '' && props.modelValue !== null && props.modelValue !== undefined;
});

const selectedOptionLabel = computed(() => {
  if (!hasSelectedValue.value) return '';
  const match = props.options.find(opt => String(getOptionValue(opt)) === String(props.modelValue));
  return match ? getOptionLabel(match) : String(props.modelValue);
});

const isSearchVisible = computed(() => {
  if (props.searchable) return true;
  return props.options.length > 8; // Tự động bật tìm kiếm khi danh sách có trên 8 mục
});

const filteredOptions = computed(() => {
  if (!searchQuery.value.trim()) return props.options;
  const q = searchQuery.value.toLowerCase().trim();
  return props.options.filter(opt => {
    const label = getOptionLabel(opt).toLowerCase();
    const sub = opt.subtitle ? opt.subtitle.toLowerCase() : '';
    return label.includes(q) || sub.includes(q);
  });
});

// ─── ACTIONS ─────────────────────────────────────────────────
function toggleDropdown() {
  if (props.disabled || props.loading) return;
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    searchQuery.value = '';
    highlightedIndex.value = -1;
    if (isSearchVisible.value) {
      nextTick(() => {
        searchInputRef.value?.focus();
      });
    }
  }
}

function closeDropdown() {
  isOpen.value = false;
  searchQuery.value = '';
  highlightedIndex.value = -1;
}

function handleSelect(opt) {
  const val = opt === '' ? '' : getOptionValue(opt);
  emit('update:modelValue', val);
  emit('change', opt === '' ? null : opt);
  closeDropdown();
}

function handleClear() {
  emit('update:modelValue', '');
  emit('clear');
  emit('change', null);
}

// ─── KEYBOARD NAVIGATION ─────────────────────────────────────
function handleKeyDown(e) {
  if (props.disabled) return;

  if (e.key === 'Enter' || e.key === ' ') {
    e.preventDefault();
    toggleDropdown();
  } else if (e.key === 'Escape') {
    closeDropdown();
  } else if (e.key === 'ArrowDown') {
    e.preventDefault();
    if (!isOpen.value) {
      toggleDropdown();
    } else if (highlightedIndex.value < filteredOptions.value.length - 1) {
      highlightedIndex.value++;
    }
  } else if (e.key === 'ArrowUp') {
    e.preventDefault();
    if (highlightedIndex.value > 0) {
      highlightedIndex.value--;
    }
  }
}

function handleSearchKeyDown(e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    if (highlightedIndex.value >= 0 && highlightedIndex.value < filteredOptions.value.length) {
      handleSelect(filteredOptions.value[highlightedIndex.value]);
    } else if (filteredOptions.value.length === 1) {
      handleSelect(filteredOptions.value[0]);
    }
  } else if (e.key === 'ArrowDown') {
    e.preventDefault();
    if (highlightedIndex.value < filteredOptions.value.length - 1) {
      highlightedIndex.value++;
    }
  } else if (e.key === 'ArrowUp') {
    e.preventDefault();
    if (highlightedIndex.value > 0) {
      highlightedIndex.value--;
    }
  } else if (e.key === 'Escape') {
    closeDropdown();
  }
}

// ─── CLICK OUTSIDE LISTENER ──────────────────────────────────
function handleClickOutside(event) {
  if (selectContainerRef.value && !selectContainerRef.value.contains(event.target)) {
    closeDropdown();
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
});

// Khi cha đổi giá trị hoặc loading, tự reset search
watch(() => props.options, () => {
  highlightedIndex.value = -1;
});
</script>

<style scoped>
.jpm-select-container {
  position: relative;
  width: 100%;
}

.jpm-select-container.is-active {
  z-index: 1050;
}

.custom-select-wrapper {
  position: relative;
}

.custom-select-wrapper.z-active {
  z-index: 1050;
}

.custom-select-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 14px;
  height: 44px;
  border-radius: var(--jpm-radius-control, 8px);
  border: 1px solid var(--jpm-neutral-200, #E2E8F0);
  background-color: var(--jpm-neutral-0, #FFFFFF);
  cursor: pointer;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
  font-size: 0.875rem;
  font-weight: 400;
  color: var(--jpm-neutral-700, #334155);
  font-family: var(--jpm-font-body, 'Roboto', sans-serif);
  outline: none;
  user-select: none;
}

.custom-select-box:hover:not(.is-disabled) {
  border-color: var(--jpm-neutral-300, #CBD5E1);
}

.custom-select-box.is-focused {
  border-color: var(--jpm-color-red-marine, #F82249);
  box-shadow: 0 0 0 3px rgba(248, 34, 73, 0.12);
}

.custom-select-box.has-value {
  color: var(--jpm-color-navy-dark, #0A1128);
  font-weight: 500;
}

.custom-select-box.is-disabled {
  background-color: var(--jpm-neutral-100, #F1F5F9);
  color: var(--jpm-neutral-400, #94A3B8);
  cursor: not-allowed;
  opacity: 0.7;
}

.custom-select-box.has-error {
  border-color: #dc3545;
}

.text-placeholder {
  color: var(--jpm-neutral-400, #94A3B8);
}

.select-arrow {
  font-size: 0.75rem;
  color: var(--jpm-neutral-500, #64748B);
  transition: transform 0.25s ease;
}

.select-arrow.rotated {
  transform: rotate(180deg);
  color: var(--jpm-color-red-marine, #F82249);
}

/* Panel Menu Dropdown */
.custom-dropdown-panel {
  position: absolute;
  top: calc(100% + 4px);
  left: 0;
  width: 100%;
  background-color: var(--jpm-neutral-0, #FFFFFF);
  border: 1px solid var(--jpm-neutral-200, #E2E8F0);
  border-radius: var(--jpm-radius-control, 8px);
  box-shadow: var(--jpm-shadow-lg, 0 12px 24px -4px rgba(10, 17, 40, 0.12));
  overflow: hidden;
  z-index: 1060;
}

/* Đảm bảo danh sách options bên trong Panel luôn ở normal flow, độc lập hoàn toàn khỏi mọi style ngoài */
.custom-dropdown-panel .jpm-select-options,
.custom-dropdown-panel .custom-dropdown-list,
.custom-dropdown-panel ul {
  position: static !important;
  top: auto !important;
  left: auto !important;
  right: auto !important;
  bottom: auto !important;
  width: 100% !important;
  min-width: 0 !important;
  max-width: 100% !important;
  border: none !important;
  border-radius: 0 !important;
  box-shadow: none !important;
  background: transparent !important;
  margin: 0 !important;
  padding: 4px 0 !important;
  max-height: 280px !important;
  overflow-y: auto !important;
  overflow-x: hidden !important;
  display: block !important;
  list-style: none !important;
}

.dropdown-item-option {
  padding: 10px 14px;
  font-size: 0.875rem;
  color: var(--jpm-neutral-800, #1E293B);
  cursor: pointer;
  transition: background-color 0.15s ease, color 0.15s ease;
  line-height: 1.35;
}

.dropdown-item-option:hover,
.dropdown-item-option.highlighted {
  background-color: var(--jpm-neutral-100, #F1F5F9);
  color: var(--jpm-neutral-900, #0F172A);
}

.dropdown-item-option.active {
  background-color: #fef2f4;
  color: var(--jpm-color-red-marine, #F82249);
  font-weight: 600;
}

.dropdown-item-option.active:hover,
.dropdown-item-option.active.highlighted {
  background-color: #fee2e6;
  color: var(--jpm-color-red-marine, #F82249);
}

.dropdown-item-option.disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.option-subtitle {
  font-size: 0.75rem;
}

.jpm-badge-sm {
  font-size: 0.7rem;
  border-radius: var(--jpm-radius-badge, 6px);
  padding: 2px 6px;
}

/* Scrollbar tinh chỉnh */
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: var(--jpm-neutral-300, #CBD5E1);
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

/* Animation mở đóng */
.dropdown-fade-enter-active,
.dropdown-fade-leave-active {
  transition: opacity 0.18s ease, transform 0.18s ease;
}
.dropdown-fade-enter-from,
.dropdown-fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

.spin {
  display: inline-block;
  animation: spin 1s linear infinite;
}
@keyframes spin {
  100% {
    transform: rotate(360deg);
  }
}
</style>

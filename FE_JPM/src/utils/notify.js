import { toast } from 'vue-sonner';

const DEFAULT_DURATIONS = {
  success: 3000,
  info: 3200,
  warning: 4200,
  error: 4500,
  default: 3200,
};

function mergeOptions(type, opts) {
  const base = { duration: DEFAULT_DURATIONS[type] ?? DEFAULT_DURATIONS.default };
  if (opts && typeof opts === 'object') {
    return { ...base, ...opts };
  }
  return base;
}

export const notify = {
  success(message, opts) {
    if (!message) return;
    return toast.success(String(message), mergeOptions('success', opts));
  },
  info(message, opts) {
    if (!message) return;
    return toast.info(String(message), mergeOptions('info', opts));
  },
  warning(message, opts) {
    if (!message) return;
    return toast.warning(String(message), mergeOptions('warning', opts));
  },
  error(message, opts) {
    if (!message) return;
    return toast.error(String(message), mergeOptions('error', opts));
  },
  show(message, opts) {
    if (!message) return;
    return toast(String(message), mergeOptions('default', opts));
  },
  clear(id) {
    return toast.dismiss(id);
  },
};

export function createToaster() {
  return notify;
}

export default notify;

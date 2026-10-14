import { computed } from 'vue';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../api/axiosClient';

// Bộ dữ liệu cấu hình dự phòng an toàn (Graceful Fallback)
// Tuyệt đối đảm bảo giao diện không bao giờ bị lỗi nếu API tạm thời gián đoạn
export const DEFAULT_SETTINGS = {
  homepage_youtube_url: 'https://www.youtube.com/watch?v=5CVzOqvqI38',
  homepage_youtube_id: '5CVzOqvqI38',
  homepage_hero_title: 'JPM MARINE',
  homepage_hero_subtitle: 'PHỤ TÙNG CHÍNH HÃNG',
  company_name: 'JPM Marine',
  company_address: '123 Đường Bạch Đằng, Q. Bình Thạnh, TP. Hồ Chí Minh, Việt Nam',
  company_hotline: '0909 123 456',
  company_email: 'contact@jpm-marine.vn',
  business_hours: 'Thứ 2 - Thứ 7: 08:00 - 17:30 (Chủ nhật nghỉ)',
  social_facebook: 'https://facebook.com/jpmmarine',
  social_youtube: 'https://youtube.com/@jpmmarine',
  social_zalo: 'https://zalo.me/0909123456',
  social_instagram: 'https://instagram.com/jpmmarine',
  footer_about_text: 'Nhà phân phối phụ tùng động cơ thủy gắn ngoài hàng đầu tại Việt Nam. Uy tín, chất lượng và đồng hành cùng bạn trên mọi hành trình vươn khơi.',
  copyright_text: '© 2026 JPM Marine. Bảo lưu mọi quyền.'
};

export function useSiteSettings() {
  const { data, isLoading, isError, refetch } = useQuery({
    queryKey: ['public-site-settings'],
    queryFn: async () => {
      const response = await axiosClient.get('/public/cau-hinh');
      return response.data?.data || response.data || {};
    },
    staleTime: 1000 * 60 * 30, // 30 phút
    gcTime: 1000 * 60 * 60,    // 60 phút
    refetchOnWindowFocus: false,
    retry: 2
  });

  const settings = computed(() => {
    if (!data.value || Object.keys(data.value).length === 0) {
      return DEFAULT_SETTINGS;
    }
    // Gộp dữ liệu API với Fallback để không bị thiếu bất kỳ trường nào
    return {
      ...DEFAULT_SETTINGS,
      ...data.value,
      homepage_youtube_id: data.value.homepage_youtube_id || DEFAULT_SETTINGS.homepage_youtube_id
    };
  });

  return {
    settings,
    isLoading,
    isError,
    refetch
  };
}

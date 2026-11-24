import { computed } from 'vue';
import { useQuery } from '@tanstack/vue-query';
import axiosClient from '../api/axiosClient';

// Bộ dữ liệu cấu hình dự phòng an toàn (Graceful Fallback)
// Tuyệt đối đảm bảo giao diện không bao giờ bị lỗi nếu API tạm thời gián đoạn
export const DEFAULT_SETTINGS = {
  homepage_youtube_url: 'https://www.youtube.com/watch?v=5CVzOqvqI38',
  homepage_youtube_id: '5CVzOqvqI38',
  homepage_hero_title: 'JPM MARINE',
  homepage_hero_subtitle: 'PHỤ TÙNG TÀU THUYỀN NHẬT BẢN',
  store_banner_image: '/client/image/shop_banner.png',
  store_banner_title: 'Cửa Hàng Phụ Tùng',
  store_banner_subtitle: 'Khám phá hệ sinh thái phụ tùng động cơ thủy, dầu nhớt và vật tư hàng hải chính hãng từ các thương hiệu dẫn đầu.',
  lookup_banner_image: '/client/image/tracuu_banner.png',
  lookup_banner_title: 'Tra Cứu Phụ Tùng',
  lookup_banner_subtitle: 'Tra cứu chính xác phụ tùng thay thế, sơ đồ rã máy chi tiết và mã kỹ thuật tiêu chuẩn cho từng model động cơ thủy của bạn.',
  news_banner_image: '/client/image/news_banner.png',
  news_banner_title: 'Tin Tức & Cẩm Nang',
  news_banner_subtitle: 'Tổng hợp những kiến thức chuyên sâu về vận hành tàu thuyền, cẩm nang bảo dưỡng động cơ thủy và xu hướng công nghệ hàng hải.',
  support_banner_image: '/client/image/hotro_banner.png',
  support_banner_title: 'Trung Tâm Hỗ Trợ & Kỹ Thuật',
  support_banner_subtitle: 'Đồng hành cùng mọi hải trình của bạn. Đội ngũ kỹ sư và chuyên viên JPM Marine luôn sẵn sàng tư vấn tra cứu mã phụ tùng OEM và hỗ trợ 24/7.',
  company_name: 'JPM Marine',
  company_address: '123 Đường Bạch Đằng, Q. Bình Thạnh, TP. Hồ Chí Minh, Việt Nam',
  company_hotline: '0909 123 456',
  company_email: 'contact@jpm-marine.vn',
  business_hours: 'Thứ 2 - Thứ 7: 08:00 - 17:30 (Chủ nhật nghỉ)',
  social_facebook: 'https://facebook.com/jpmmarine',
  social_youtube: 'https://youtube.com/@jpmmarine',
  social_zalo: 'https://zalo.me/0909123456',
  social_instagram: '',
  footer_about_text: 'Nhà phân phối phụ tùng động cơ thủy gắn ngoài hàng đầu tại Việt Nam. Uy tín, chất lượng và đồng hành cùng bạn trên mọi hành trình vươn khơi.',
  copyright_text: '© 2026 JPM Marine. Bảo lưu mọi quyền.'
};

export function useSiteSettings() {
  const { data, isLoading, isError, refetch } = useQuery({
    queryKey: ['public-site-settings-v2'],
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
      homepage_hero_subtitle:
        !data.value.homepage_hero_subtitle || data.value.homepage_hero_subtitle === 'PHỤ TÙNG CHÍNH HÃNG'
          ? DEFAULT_SETTINGS.homepage_hero_subtitle
          : data.value.homepage_hero_subtitle,
      homepage_youtube_id: data.value.homepage_youtube_id || DEFAULT_SETTINGS.homepage_youtube_id,
      store_banner_image: data.value.store_banner_image || DEFAULT_SETTINGS.store_banner_image,
      news_banner_image: data.value.news_banner_image || DEFAULT_SETTINGS.news_banner_image,
      lookup_banner_image: data.value.lookup_banner_image || DEFAULT_SETTINGS.lookup_banner_image,
      support_banner_image: data.value.support_banner_image || DEFAULT_SETTINGS.support_banner_image
    };
  });

  return {
    settings,
    isLoading,
    isError,
    refetch
  };
}

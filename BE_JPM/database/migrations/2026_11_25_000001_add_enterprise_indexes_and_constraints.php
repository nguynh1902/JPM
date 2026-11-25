<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function safeAddIndex(string $table, array $columns, string $indexName, bool $unique = false): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }
        foreach ($columns as $col) {
            if (!Schema::hasColumn($table, $col)) {
                return;
            }
        }

        try {
            Schema::table($table, function (Blueprint $t) use ($columns, $indexName, $unique) {
                if ($unique) {
                    $t->unique($columns, $indexName);
                } else {
                    $t->index($columns, $indexName);
                }
            });
        } catch (\Throwable $e) {
            // Index already exists or duplicate data constraint handled safely
        }
    }

    private function safeDropIndex(string $table, string $indexName, bool $unique = false): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }
        try {
            Schema::table($table, function (Blueprint $t) use ($indexName, $unique) {
                if ($unique) {
                    $t->dropUnique($indexName);
                } else {
                    $t->dropIndex($indexName);
                }
            });
        } catch (\Throwable $e) {
            // Ignore if index does not exist
        }
    }

    public function up(): void
    {
        // 1. Tồn kho: Composite unique & lookup indexes
        $this->safeAddIndex('ton_khos', ['id_kho', 'id_san_pham', 'loai_san_pham'], 'idx_ton_khos_unique_kho_sp', true);
        $this->safeAddIndex('ton_khos', ['id_san_pham', 'loai_san_pham'], 'idx_ton_khos_sp_loai');
        $this->safeAddIndex('ton_khos', ['so_luong_dang_giu'], 'idx_ton_khos_dang_giu');

        // 2. Chi tiết đơn hàng
        $this->safeAddIndex('chi_tiet_don_hangs', ['id_don_hang'], 'idx_ctdh_don_hang');
        $this->safeAddIndex('chi_tiet_don_hangs', ['id_san_pham', 'loai_san_pham'], 'idx_ctdh_sp_loai');

        // 3. Thẻ kho (Audit ledger)
        $this->safeAddIndex('the_khos', ['id_kho', 'created_at'], 'idx_the_khos_kho_created');
        $this->safeAddIndex('the_khos', ['id_san_pham', 'loai_san_pham'], 'idx_the_khos_sp_loai');
        $this->safeAddIndex('the_khos', ['id_tham_chieu', 'loai_giao_dich'], 'idx_the_khos_ref_type');

        // 4. Địa chỉ khách hàng
        $this->safeAddIndex('dia_chis', ['id_tai_khoan', 'la_mac_dinh'], 'idx_dia_chis_user_default');

        // 5. Nhật ký hệ thống
        $this->safeAddIndex('system_logs', ['user_id', 'created_at'], 'idx_sys_logs_user_created');
        $this->safeAddIndex('system_logs', ['action'], 'idx_sys_logs_action');
        $this->safeAddIndex('system_logs', ['created_at'], 'idx_sys_logs_created');

        // 6. Đơn hàng (Dashboard, tra cứu & lịch sử)
        $this->safeAddIndex('don_hangs', ['trang_thai', 'created_at'], 'idx_don_hangs_status_created');
        $this->safeAddIndex('don_hangs', ['trang_thai_thanh_toan'], 'idx_don_hangs_pay_status');
        $this->safeAddIndex('don_hangs', ['khach_hang_sdt'], 'idx_don_hangs_sdt');
        $this->safeAddIndex('don_hangs', ['khach_hang_email'], 'idx_don_hangs_email');

        // 7. Yêu cầu báo giá
        $this->safeAddIndex('yeu_cau_bao_gias', ['trang_thai', 'created_at'], 'idx_ycbg_status_created');
        $this->safeAddIndex('yeu_cau_bao_gias', ['khach_hang_sdt'], 'idx_ycbg_sdt');
        $this->safeAddIndex('yeu_cau_bao_gias', ['khach_hang_email'], 'idx_ycbg_email');

        // 8. Giao dịch thanh toán (Unique chống trùng webhook cấp DB)
        $this->safeAddIndex('giao_dich_thanh_toans', ['ma_giao_dich'], 'idx_gdtt_ma_gd_unique', true);
        $this->safeAddIndex('giao_dich_thanh_toans', ['ma_tham_chieu'], 'idx_gdtt_ma_ref');

        // 9. Sản phẩm & Phụ tùng (Catalog filtering & sorting)
        $this->safeAddIndex('san_phams', ['trang_thai', 'id'], 'idx_san_phams_status_id');
        $this->safeAddIndex('san_phams', ['danh_muc_san_pham_id', 'trang_thai'], 'idx_san_phams_cat_status');
        $this->safeAddIndex('phu_tungs', ['trang_thai', 'id'], 'idx_phu_tungs_status_id');
        $this->safeAddIndex('phu_tungs', ['trang_thai', 'gia_ban'], 'idx_phu_tungs_status_price');

        // 10. Đơn nhập hàng & Lịch sử thông báo
        $this->safeAddIndex('don_nhap_hangs', ['trang_thai', 'created_at'], 'idx_dnh_status_created');
        $this->safeAddIndex('chi_tiet_don_nhap_hangs', ['id_don_nhap_hang'], 'idx_ctdnh_po');
        $this->safeAddIndex('lich_su_thong_baos', ['ma_tham_chieu'], 'idx_lstb_ref');
        $this->safeAddIndex('lich_su_thong_baos', ['created_at'], 'idx_lstb_created');
    }

    public function down(): void
    {
        $this->safeDropIndex('ton_khos', 'idx_ton_khos_unique_kho_sp', true);
        $this->safeDropIndex('ton_khos', 'idx_ton_khos_sp_loai');
        $this->safeDropIndex('ton_khos', 'idx_ton_khos_dang_giu');
        $this->safeDropIndex('chi_tiet_don_hangs', 'idx_ctdh_don_hang');
        $this->safeDropIndex('chi_tiet_don_hangs', 'idx_ctdh_sp_loai');
        $this->safeDropIndex('the_khos', 'idx_the_khos_kho_created');
        $this->safeDropIndex('the_khos', 'idx_the_khos_sp_loai');
        $this->safeDropIndex('the_khos', 'idx_the_khos_ref_type');
        $this->safeDropIndex('dia_chis', 'idx_dia_chis_user_default');
        $this->safeDropIndex('system_logs', 'idx_sys_logs_user_created');
        $this->safeDropIndex('system_logs', 'idx_sys_logs_action');
        $this->safeDropIndex('system_logs', 'idx_sys_logs_created');
        $this->safeDropIndex('don_hangs', 'idx_don_hangs_status_created');
        $this->safeDropIndex('don_hangs', 'idx_don_hangs_pay_status');
        $this->safeDropIndex('don_hangs', 'idx_don_hangs_sdt');
        $this->safeDropIndex('don_hangs', 'idx_don_hangs_email');
        $this->safeDropIndex('yeu_cau_bao_gias', 'idx_ycbg_status_created');
        $this->safeDropIndex('yeu_cau_bao_gias', 'idx_ycbg_sdt');
        $this->safeDropIndex('yeu_cau_bao_gias', 'idx_ycbg_email');
        $this->safeDropIndex('giao_dich_thanh_toans', 'idx_gdtt_ma_gd_unique', true);
        $this->safeDropIndex('giao_dich_thanh_toans', 'idx_gdtt_ma_ref');
        $this->safeDropIndex('san_phams', 'idx_san_phams_status_id');
        $this->safeDropIndex('san_phams', 'idx_san_phams_cat_status');
        $this->safeDropIndex('phu_tungs', 'idx_phu_tungs_status_id');
        $this->safeDropIndex('phu_tungs', 'idx_phu_tungs_status_price');
        $this->safeDropIndex('don_nhap_hangs', 'idx_dnh_status_created');
        $this->safeDropIndex('chi_tiet_don_nhap_hangs', 'idx_ctdnh_po');
        $this->safeDropIndex('lich_su_thong_baos', 'idx_lstb_ref');
        $this->safeDropIndex('lich_su_thong_baos', 'idx_lstb_created');
    }
};

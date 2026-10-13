<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Thêm các Index chuyên sâu cho hiệu năng cao tải.
     * Các index này được chọn lựa kỹ dựa trên pattern query thực tế của JPM.
     */
    public function up(): void
    {
        // === BẢNG USERS: Tối ưu Đăng nhập & Tra cứu tài khoản ===
        Schema::table('users', function (Blueprint $table) {
            // Composite index cho đăng nhập (SELECT WHERE email OR phone)
            if (!$this->indexExists('users', 'idx_users_email_status')) {
                $table->index(['email', 'status'], 'idx_users_email_status');
            }
            if (!$this->indexExists('users', 'idx_users_phone_status')) {
                $table->index(['phone', 'status'], 'idx_users_phone_status');
            }
        });

        // === BẢNG PERSONAL_ACCESS_TOKENS: Tối ưu Token Lookup ===
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Index trên tokenable_id để xóa token cũ khi logout nhanh hơn
            if (!$this->indexExists('personal_access_tokens', 'idx_pat_tokenable_created')) {
                $table->index(['tokenable_id', 'created_at'], 'idx_pat_tokenable_created');
            }
        });

        // === BẢNG DON_HANGS: Tối ưu Query Đơn Hàng ===
        Schema::table('don_hangs', function (Blueprint $table) {
            if (!$this->indexExists('don_hangs', 'idx_don_hangs_khach_status')) {
                $table->index(['id_khach_hang', 'trang_thai'], 'idx_don_hangs_khach_status');
            }
            if (!$this->indexExists('don_hangs', 'idx_don_hangs_created')) {
                $table->index('created_at', 'idx_don_hangs_created');
            }
        });

        // === BẢNG TIN_TUCS: Tối ưu Listing Tin tức ===
        Schema::table('tin_tucs', function (Blueprint $table) {
            if (!$this->indexExists('tin_tucs', 'idx_tin_tucs_status_created')) {
                $table->index(['status', 'created_at'], 'idx_tin_tucs_status_created');
            }
        });

        // === BẢNG PHU_TUNGS: Tối ưu Tra cứu phụ tùng ===
        Schema::table('phu_tungs', function (Blueprint $table) {
            if (!$this->indexExists('phu_tungs', 'idx_phu_tungs_ma_phu_tung')) {
                $table->index('ma_phu_tung', 'idx_phu_tungs_ma_phu_tung');
            }
        });

        // === BẢNG NOTIFICATIONS: Tối ưu đọc thông báo theo user ===
        Schema::table('notifications', function (Blueprint $table) {
            if (!$this->indexExists('notifications', 'idx_notif_notifiable_read')) {
                $table->index(['notifiable_id', 'read_at'], 'idx_notif_notifiable_read');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if ($this->indexExists('users', 'idx_users_email_status')) {
                $table->dropIndex('idx_users_email_status');
            }
            if ($this->indexExists('users', 'idx_users_phone_status')) {
                $table->dropIndex('idx_users_phone_status');
            }
        });
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            if ($this->indexExists('personal_access_tokens', 'idx_pat_tokenable_created')) {
                $table->dropIndex('idx_pat_tokenable_created');
            }
        });
        Schema::table('don_hangs', function (Blueprint $table) {
            if ($this->indexExists('don_hangs', 'idx_don_hangs_khach_status')) {
                $table->dropIndex('idx_don_hangs_khach_status');
            }
            if ($this->indexExists('don_hangs', 'idx_don_hangs_created')) {
                $table->dropIndex('idx_don_hangs_created');
            }
        });
        Schema::table('tin_tucs', function (Blueprint $table) {
            if ($this->indexExists('tin_tucs', 'idx_tin_tucs_status_created')) {
                $table->dropIndex('idx_tin_tucs_status_created');
            }
        });
        Schema::table('phu_tungs', function (Blueprint $table) {
            if ($this->indexExists('phu_tungs', 'idx_phu_tungs_ma_phu_tung')) {
                $table->dropIndex('idx_phu_tungs_ma_phu_tung');
            }
        });
        Schema::table('notifications', function (Blueprint $table) {
            if ($this->indexExists('notifications', 'idx_notif_notifiable_read')) {
                $table->dropIndex('idx_notif_notifiable_read');
            }
        });
    }

    /**
     * Kiểm tra index đã tồn tại chưa để tránh lỗi khi migrate.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $exists = DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$indexName]
        );
        return !empty($exists);
    }
};

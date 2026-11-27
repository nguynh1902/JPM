<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function dropExistingColumns(string $table, array $columns): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $toDrop = array_values(array_filter(
            $columns,
            fn (string $col) => Schema::hasColumn($table, $col)
        ));

        if (!empty($toDrop)) {
            Schema::table($table, function (Blueprint $t) use ($toDrop) {
                $t->dropColumn($toDrop);
            });
        }
    }

    public function up(): void
    {
        // 1. users: bỏ gender (không sử dụng ở FE/BE)
        $this->dropExistingColumns('users', ['gender']);

        // 2. khach_hangs: bỏ ten_cong_ty & các trường CRM không dùng trong UI/nghiệp vụ
        $this->dropExistingColumns('khach_hangs', [
            'ten_cong_ty',
            'ngay_sinh',
            'gioi_tinh',
            'hang_thanh_vien',
            'diem_thuong',
            'ghi_chu',
        ]);

        // 3. nhan_viens: bỏ các trường HR không dùng (vai trò/trạng thái nằm ở roles & users.status)
        $this->dropExistingColumns('nhan_viens', [
            'ngay_sinh',
            'ngay_vao_lam',
            'phong_ban',
            'chuc_vu',
            'trang_thai_lam_viec',
        ]);

        // 4. dia_chis: bỏ 3 cột mã địa giới hành chính & loai_dia_chi không sử dụng
        $this->dropExistingColumns('dia_chis', [
            'id_phuong_xa',
            'id_quan_huyen',
            'id_tinh_thanh',
            'loai_dia_chi',
        ]);

        // 5. san_phams: bỏ mo_ta_ngan (chỉ dùng mo_ta_chi_tiet)
        $this->dropExistingColumns('san_phams', ['mo_ta_ngan']);

        // 6. don_hangs: bỏ cột vận chuyển trùng lặp & 2 FK legacy không còn dùng
        $this->dropExistingColumns('don_hangs', [
            'don_vi_van_chuyen',
            'id_don_vi_van_chuyen',
            'id_phuong_thuc_thanh_toan',
        ]);

        // 7. chi_tiet_don_hangs: bỏ ghi_chu không dùng
        $this->dropExistingColumns('chi_tiet_don_hangs', ['ghi_chu']);

        // 8. chi_tiet_so_dos: bỏ width, height (Client chỉ dùng pin tọa độ x, y)
        $this->dropExistingColumns('chi_tiet_so_dos', ['width', 'height']);
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'gender')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('gender', 20)->nullable()->after('phone');
            });
        }

        if (Schema::hasTable('khach_hangs')) {
            Schema::table('khach_hangs', function (Blueprint $table) {
                if (!Schema::hasColumn('khach_hangs', 'ten_cong_ty')) {
                    $table->string('ten_cong_ty')->nullable()->after('ho_ten');
                }
                if (!Schema::hasColumn('khach_hangs', 'ngay_sinh')) {
                    $table->date('ngay_sinh')->nullable()->after('so_dien_thoai');
                }
                if (!Schema::hasColumn('khach_hangs', 'gioi_tinh')) {
                    $table->tinyInteger('gioi_tinh')->nullable()->after('ngay_sinh');
                }
                if (!Schema::hasColumn('khach_hangs', 'hang_thanh_vien')) {
                    $table->string('hang_thanh_vien', 50)->nullable()->after('gioi_tinh');
                }
                if (!Schema::hasColumn('khach_hangs', 'diem_thuong')) {
                    $table->integer('diem_thuong')->default(0)->after('hang_thanh_vien');
                }
                if (!Schema::hasColumn('khach_hangs', 'ghi_chu')) {
                    $table->text('ghi_chu')->nullable()->after('diem_thuong');
                }
            });
        }

        if (Schema::hasTable('nhan_viens')) {
            Schema::table('nhan_viens', function (Blueprint $table) {
                if (!Schema::hasColumn('nhan_viens', 'ngay_sinh')) {
                    $table->date('ngay_sinh')->nullable()->after('so_dien_thoai');
                }
                if (!Schema::hasColumn('nhan_viens', 'ngay_vao_lam')) {
                    $table->date('ngay_vao_lam')->nullable()->after('ngay_sinh');
                }
                if (!Schema::hasColumn('nhan_viens', 'phong_ban')) {
                    $table->string('phong_ban', 100)->nullable()->after('ngay_vao_lam');
                }
                if (!Schema::hasColumn('nhan_viens', 'chuc_vu')) {
                    $table->string('chuc_vu', 100)->nullable()->after('phong_ban');
                }
                if (!Schema::hasColumn('nhan_viens', 'trang_thai_lam_viec')) {
                    $table->string('trang_thai_lam_viec', 50)->nullable()->after('chuc_vu');
                }
            });
        }

        if (Schema::hasTable('dia_chis')) {
            Schema::table('dia_chis', function (Blueprint $table) {
                if (!Schema::hasColumn('dia_chis', 'id_phuong_xa')) {
                    $table->string('id_phuong_xa', 20)->nullable()->after('dia_chi_cu_the');
                }
                if (!Schema::hasColumn('dia_chis', 'id_quan_huyen')) {
                    $table->string('id_quan_huyen', 20)->nullable()->after('id_phuong_xa');
                }
                if (!Schema::hasColumn('dia_chis', 'id_tinh_thanh')) {
                    $table->string('id_tinh_thanh', 20)->nullable()->after('id_quan_huyen');
                }
                if (!Schema::hasColumn('dia_chis', 'loai_dia_chi')) {
                    $table->string('loai_dia_chi', 20)->nullable()->after('la_mac_dinh');
                }
            });
        }

        if (Schema::hasTable('san_phams') && !Schema::hasColumn('san_phams', 'mo_ta_ngan')) {
            Schema::table('san_phams', function (Blueprint $table) {
                $table->text('mo_ta_ngan')->nullable()->after('slug');
            });
        }

        if (Schema::hasTable('don_hangs')) {
            Schema::table('don_hangs', function (Blueprint $table) {
                if (!Schema::hasColumn('don_hangs', 'id_phuong_thuc_thanh_toan')) {
                    $table->unsignedBigInteger('id_phuong_thuc_thanh_toan')->nullable();
                }
                if (!Schema::hasColumn('don_hangs', 'id_don_vi_van_chuyen')) {
                    $table->unsignedBigInteger('id_don_vi_van_chuyen')->nullable();
                }
                if (!Schema::hasColumn('don_hangs', 'don_vi_van_chuyen')) {
                    $table->string('don_vi_van_chuyen')->nullable();
                }
            });
        }

        if (Schema::hasTable('chi_tiet_don_hangs') && !Schema::hasColumn('chi_tiet_don_hangs', 'ghi_chu')) {
            Schema::table('chi_tiet_don_hangs', function (Blueprint $table) {
                $table->text('ghi_chu')->nullable();
            });
        }

        if (Schema::hasTable('chi_tiet_so_dos')) {
            Schema::table('chi_tiet_so_dos', function (Blueprint $table) {
                if (!Schema::hasColumn('chi_tiet_so_dos', 'width')) {
                    $table->double('width')->nullable();
                }
                if (!Schema::hasColumn('chi_tiet_so_dos', 'height')) {
                    $table->double('height')->nullable();
                }
            });
        }
    }
};

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KhoSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Seed Khos
        $khoIds = [];
        $khos = [
            ['ten_kho' => 'Kho Trung Tâm', 'ma_kho' => 'KHO-TT', 'dia_chi' => '123 Đường A, Quận 1, TP.HCM', 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];
        
        foreach ($khos as $kho) {
            $khoIds[] = DB::table('khos')->insertGetId($kho);
        }

        // Get existing Products and Spare parts to ensure valid IDs
        $sanPhams = DB::table('san_phams')->pluck('id')->toArray();
        $phuTungs = DB::table('phu_tungs')->pluck('id')->toArray();

        if (empty($sanPhams) && empty($phuTungs)) {
            // No products exist yet, we can't seed inventory. Just return.
            return;
        }

        // 2. Seed Don Nhap Hang
        for ($i = 1; $i <= 5; $i++) {
            $idKho = $khoIds[array_rand($khoIds)];
            
            $maPO = 'PO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            
            $idDonNhap = DB::table('don_nhap_hangs')->insertGetId([
                'ma_po' => $maPO,
                'id_nha_cung_cap' => rand(1, 3), 
                'id_kho' => $idKho,
                'id_tien_te' => 1,
                'ty_gia' => 1,
                'ngay_nhap' => $now->copy()->subDays(rand(1, 30)),
                'ghi_chu' => 'Nhập hàng đợt ' . $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $tongTienPO = 0;

            // 3. Chi Tiet Don Nhap, Ton Kho, The Kho
            for ($j = 1; $j <= 3; $j++) {
                // Randomly choose between san_pham and phu_tung based on availability
                $types = [];
                if (!empty($sanPhams)) $types[] = 'san_pham';
                if (!empty($phuTungs)) $types[] = 'phu_tung';
                
                $loaiSanPham = $types[array_rand($types)];
                $idSanPham = ($loaiSanPham === 'san_pham') ? $sanPhams[array_rand($sanPhams)] : $phuTungs[array_rand($phuTungs)];
                
                $soLuongNhap = rand(10, 50);
                $donGiaNhap = rand(100000, 5000000);
                $thanhTien = $soLuongNhap * $donGiaNhap;
                
                $tongTienPO += $thanhTien;
                
                DB::table('chi_tiet_don_nhap_hangs')->insert([
                    'id_don_nhap_hang' => $idDonNhap,
                    'id_san_pham' => $idSanPham,
                    'loai_san_pham' => $loaiSanPham,
                    'so_luong_nhap' => $soLuongNhap,
                    'don_gia_nhap' => $donGiaNhap,
                    'thanh_tien' => $thanhTien,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // Update or create TonKho
                $tonKho = DB::table('ton_khos')
                            ->where('id_kho', $idKho)
                            ->where('id_san_pham', $idSanPham)
                            ->where('loai_san_pham', $loaiSanPham)
                            ->first();
                            
                if ($tonKho) {
                    DB::table('ton_khos')->where('id', $tonKho->id)->update([
                        'so_luong_ton' => $tonKho->so_luong_ton + $soLuongNhap,
                        'updated_at' => $now,
                    ]);
                    $tonCuoi = $tonKho->so_luong_ton + $soLuongNhap;
                } else {
                    DB::table('ton_khos')->insert([
                        'id_kho' => $idKho,
                        'id_san_pham' => $idSanPham,
                        'loai_san_pham' => $loaiSanPham,
                        'so_luong_ton' => $soLuongNhap,
                        'so_luong_dang_giu' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                    $tonCuoi = $soLuongNhap;
                }

                // Add TheKho
                DB::table('the_khos')->insert([
                    'id_kho' => $idKho,
                    'id_san_pham' => $idSanPham,
                    'loai_san_pham' => $loaiSanPham,
                    'loai_giao_dich' => 'NHAP_KHO',
                    'so_luong_thay_doi' => $soLuongNhap,
                    'so_luong_ton_cuoi' => $tonCuoi,
                    'id_tham_chieu' => $idDonNhap,
                    'ma_tham_chieu' => 'PO-' . str_pad($idDonNhap, 5, '0', STR_PAD_LEFT),
                    'ghi_chu' => 'Nhập hàng từ NCC',
                    'created_by' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Update total PO amount
            DB::table('don_nhap_hangs')->where('id', $idDonNhap)->update(['tong_tien' => $tongTienPO]);
        }
    }
}

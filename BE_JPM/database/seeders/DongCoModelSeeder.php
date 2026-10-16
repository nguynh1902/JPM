<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DongCoModelSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $data = [
            // HONDA MODELS
            ['thuong_hieu_id' => 2, 'loai_dong_co_id' => 1, 'ma_model' => 'MDL-BF200', 'ten_model' => 'BF200A4 LA VIN# BAEJ-1200001 To BAEJ-1299999', 'nam_san_xuat' => 2004, 'ma_luc' => 200, 'loai_nhip' => null, 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['thuong_hieu_id' => 2, 'loai_dong_co_id' => 1, 'ma_model' => 'MDL-BF150', 'ten_model' => 'BF150A4 LA VIN# BANJ-1300001 To BANJ-1399999', 'nam_san_xuat' => 2004, 'ma_luc' => 150, 'loai_nhip' => null, 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['thuong_hieu_id' => 2, 'loai_dong_co_id' => 1, 'ma_model' => 'MDL-BF135', 'ten_model' => 'BF135A4 XA VIN# BARJ-1000001 To BARJ-1099999', 'nam_san_xuat' => 2004, 'ma_luc' => 135, 'loai_nhip' => null, 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['thuong_hieu_id' => 2, 'loai_dong_co_id' => 1, 'ma_model' => 'MDL-BF225', 'ten_model' => 'BF225AK1 LA VIN# BAGJ-1400001 To BAGJ-1499999', 'nam_san_xuat' => 2007, 'ma_luc' => 225, 'loai_nhip' => null, 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['thuong_hieu_id' => 2, 'loai_dong_co_id' => 1, 'ma_model' => 'MDL-BF250', 'ten_model' => 'BF250A XA VIN# BBJJ-1000001 To BBJJ-1099999', 'nam_san_xuat' => 2012, 'ma_luc' => 250, 'loai_nhip' => null, 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['thuong_hieu_id' => 2, 'loai_dong_co_id' => 1, 'ma_model' => 'MDL-BF100', 'ten_model' => 'BF100A LTA VIN# BBMJ-1000001 To BBMJ-1099999', 'nam_san_xuat' => 2015, 'ma_luc' => 100, 'loai_nhip' => null, 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],

            // YAMAHA MODELS
            ['thuong_hieu_id' => 1, 'loai_dong_co_id' => 1, 'ma_model' => 'F250TUR', 'ten_model' => 'F250TUR', 'nam_san_xuat' => 2005, 'ma_luc' => 250, 'loai_nhip' => null, 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['thuong_hieu_id' => 1, 'loai_dong_co_id' => 1, 'ma_model' => 'LZ250TURC', 'ten_model' => 'LZ250TURC', 'nam_san_xuat' => 2004, 'ma_luc' => 250, 'loai_nhip' => null, 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['thuong_hieu_id' => 1, 'loai_dong_co_id' => 1, 'ma_model' => 'F150TLR', 'ten_model' => 'F150TLR', 'nam_san_xuat' => 2006, 'ma_luc' => 150, 'loai_nhip' => null, 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['thuong_hieu_id' => 1, 'loai_dong_co_id' => 1, 'ma_model' => 'F115TXR', 'ten_model' => 'F115TXR', 'nam_san_xuat' => 2008, 'ma_luc' => 115, 'loai_nhip' => null, 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['thuong_hieu_id' => 1, 'loai_dong_co_id' => 1, 'ma_model' => 'F200XB', 'ten_model' => 'F200XB', 'nam_san_xuat' => 2013, 'ma_luc' => 200, 'loai_nhip' => null, 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['thuong_hieu_id' => 1, 'loai_dong_co_id' => 1, 'ma_model' => 'F250XCA', 'ten_model' => 'F250XCA', 'nam_san_xuat' => 2011, 'ma_luc' => 250, 'loai_nhip' => null, 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['thuong_hieu_id' => 1, 'loai_dong_co_id' => 1, 'ma_model' => 'VF250LA', 'ten_model' => 'VF250LA (VMAX SHO)', 'nam_san_xuat' => 2010, 'ma_luc' => 250, 'loai_nhip' => null, 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],

            // MERCURY MARINE MODELS (thuong_hieu_id: 7)
            ['thuong_hieu_id' => 7, 'loai_dong_co_id' => 1, 'ma_model' => 'MERC-300-V8', 'ten_model' => '300HP V8 2B529482 & Up', 'nam_san_xuat' => 2018, 'ma_luc' => 300, 'loai_nhip' => '4 STROKE', 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['thuong_hieu_id' => 7, 'loai_dong_co_id' => 1, 'ma_model' => 'MERC-300-PRO-XS', 'ten_model' => '300HP V8 Pro XS 2B529482 & Up', 'nam_san_xuat' => 2018, 'ma_luc' => 300, 'loai_nhip' => '4 STROKE', 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['thuong_hieu_id' => 7, 'loai_dong_co_id' => 1, 'ma_model' => 'MERC-300-SEAPRO', 'ten_model' => '300HP V8 SeaPro 2B529482 & Up', 'nam_san_xuat' => 2018, 'ma_luc' => 300, 'loai_nhip' => '4 STROKE', 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['thuong_hieu_id' => 7, 'loai_dong_co_id' => 1, 'ma_model' => 'MERC-300-VERADO', 'ten_model' => '300HP Verado (L6) 2B529482 & Up', 'nam_san_xuat' => 2018, 'ma_luc' => 300, 'loai_nhip' => '4 STROKE', 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['thuong_hieu_id' => 7, 'loai_dong_co_id' => 1, 'ma_model' => 'MERC-300-R', 'ten_model' => '300HP R V8 2B529482 & Up', 'nam_san_xuat' => 2018, 'ma_luc' => 300, 'loai_nhip' => '4 STROKE', 'trang_thai' => 1, 'created_at' => $now, 'updated_at' => $now]
        ];
        DB::table('dong_co_models')->upsert($data, ['ma_model'], ['thuong_hieu_id', 'loai_dong_co_id', 'ten_model', 'nam_san_xuat', 'ma_luc', 'loai_nhip', 'trang_thai', 'updated_at']);
    }
}

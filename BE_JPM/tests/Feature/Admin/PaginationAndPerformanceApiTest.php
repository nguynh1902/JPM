<?php

namespace Tests\Feature\Admin;

use App\Domain\IAM\Models\User;
use App\Domain\MarineCatalog\Models\ThuongHieu;
use App\Domain\MarineCatalog\Models\LoaiDongCo;
use App\Domain\MarineCatalog\Models\DongCoModel;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\YeuCauBaoGia;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $this->user->assignRole($role);
});

it('can paginate dong co models with meta', function () {
    $th = ThuongHieu::create([
        'code' => 'YAM',
        'name' => 'Yamaha Marine',
        'status' => 1
    ]);
    $ldc = LoaiDongCo::create([
        'ma_loai_dong_co' => 'OB',
        'ten_loai_dong_co' => 'Outboard',
        'trang_thai' => 1
    ]);

    for ($i = 1; $i <= 5; $i++) {
        DongCoModel::create([
            'thuong_hieu_id' => $th->id,
            'loai_dong_co_id' => $ldc->id,
            'ma_model' => "F{$i}00",
            'ten_model' => "Yamaha F{$i}00 HP",
            'nam_san_xuat' => 2020 + $i,
            'ma_luc' => "{$i}00"
        ]);
    }

    $response = $this->actingAs($this->user)->getJson('/api/admin/dong-co-model?per_page=2&page=1');

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
             ])
             ->assertJsonCount(2, 'data')
             ->assertJsonStructure([
                 'data',
                 'meta' => ['current_page', 'last_page', 'per_page', 'total']
             ])
             ->assertJsonPath('meta.total', 5)
             ->assertJsonPath('meta.per_page', 2);
});

it('can fetch all dong co models when all flag is present', function () {
    $th = ThuongHieu::create([
        'code' => 'HON',
        'name' => 'Honda Marine',
        'status' => 1
    ]);
    $ldc = LoaiDongCo::create([
        'ma_loai_dong_co' => 'OB',
        'ten_loai_dong_co' => 'Outboard',
        'trang_thai' => 1
    ]);

    for ($i = 1; $i <= 3; $i++) {
        DongCoModel::create([
            'thuong_hieu_id' => $th->id,
            'loai_dong_co_id' => $ldc->id,
            'ma_model' => "BF{$i}0",
            'ten_model' => "Honda BF{$i}0 HP",
            'nam_san_xuat' => 2022,
            'ma_luc' => "{$i}0"
        ]);
    }

    $response = $this->actingAs($this->user)->getJson('/api/admin/dong-co-model?all=true');

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
             ])
             ->assertJsonCount(3, 'data');
});

it('can paginate don hang with meta', function () {
    for ($i = 1; $i <= 3; $i++) {
        DonHang::create([
            'ma_don_hang' => "DH-TEST-{$i}",
            'id_khach_hang' => 1,
            'id_dia_chi_giao_hang' => 1,
            'tong_tien' => 1000000 * $i,
            'trang_thai' => 1,
            'trang_thai_thanh_toan' => 0
        ]);
    }

    $response = $this->actingAs($this->user)->getJson('/api/admin/don-hang?per_page=2&page=1');

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
             ])
             ->assertJsonCount(2, 'data')
             ->assertJsonStructure([
                 'data',
                 'meta' => ['current_page', 'last_page', 'per_page', 'total']
             ])
             ->assertJsonPath('meta.total', 3);
});

it('can paginate yeu cau bao gia with meta', function () {
    for ($i = 1; $i <= 3; $i++) {
        YeuCauBaoGia::create([
            'ma_yeu_cau' => "BG-TEST-{$i}",
            'khach_hang_ten' => "Khach Test {$i}",
            'khach_hang_sdt' => "090000000{$i}",
            'khach_hang_email' => "test{$i}@jpm.vn",
            'trang_thai' => 0
        ]);
    }

    $response = $this->actingAs($this->user)->getJson('/api/admin/yeu-cau-bao-gia?per_page=2&page=1');

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
             ])
             ->assertJsonCount(2, 'data')
             ->assertJsonStructure([
                 'data',
                 'meta' => ['current_page', 'last_page', 'per_page', 'total']
             ])
             ->assertJsonPath('meta.total', 3);
});

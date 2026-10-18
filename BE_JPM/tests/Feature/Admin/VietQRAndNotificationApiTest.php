<?php

namespace Tests\Feature\Admin;

use App\Domain\IAM\Models\User;
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

it('can generate public vietqr for order and check status', function () {
    $order = DonHang::create([
        'ma_don_hang' => 'DH-TEST-QR-001',
        'tong_tien' => 5000000,
        'tien_coc_yeu_cau' => 1000000,
        'da_thu' => 0,
        'trang_thai' => 0,
        'trang_thai_thanh_toan' => 0,
        'khach_hang_ten' => 'Nguyen Van A',
        'khach_hang_sdt' => '0912345678',
    ]);

    $response = $this->getJson('/api/public/vietqr/don-hang/' . $order->ma_don_hang);
    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'order_code' => 'DH-TEST-QR-001',
                 'is_paid' => false,
             ]);

    $statusRes = $this->getJson('/api/public/vietqr/check-status/order/' . $order->ma_don_hang);
    $statusRes->assertStatus(200)
              ->assertJson([
                  'success' => true,
                  'order_code' => 'DH-TEST-QR-001',
                  'paid_amount' => 0,
              ]);
});

it('can reconcile payment webhook and notify customer via simulation', function () {
    $order = DonHang::create([
        'ma_don_hang' => 'DH-TEST-RECON-002',
        'tong_tien' => 2000000,
        'tien_coc_yeu_cau' => 500000,
        'da_thu' => 0,
        'trang_thai' => 0,
        'trang_thai_thanh_toan' => 0,
        'khach_hang_ten' => 'Le Van B',
        'khach_hang_sdt' => '0987654321',
    ]);

    $payload = [
        'amountIn' => 500000,
        'transactionContent' => 'JPM DH-TEST-RECON-002 COC',
        'referenceNumber' => 'TX_BANK_TEST_001',
        'gateway' => 'VietinBank Test',
    ];

    $response = $this->postJson('/api/webhooks/payment/reconciliation', $payload);
    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'matched' => 'don_hang',
                 'order_code' => 'DH-TEST-RECON-002',
                 'paid_added' => 500000,
             ]);

    $this->assertDatabaseHas('don_hangs', [
        'ma_don_hang' => 'DH-TEST-RECON-002',
        'da_thu' => 500000,
    ]);

    $this->assertDatabaseHas('giao_dich_thanh_toans', [
        'ma_giao_dich' => 'TX_BANK_TEST_001',
        'ma_tham_chieu' => 'DH-TEST-RECON-002',
    ]);

    $this->assertDatabaseHas('lich_su_thong_baos', [
        'ma_tham_chieu' => 'DH-TEST-RECON-002',
        'so_dien_thoai' => '0987654321',
    ]);
});

it('can dispatch shipping notification to customer and truck driver', function () {
    $order = DonHang::create([
        'ma_don_hang' => 'DH-TEST-SHIP-003',
        'tong_tien' => 3000000,
        'tien_coc_yeu_cau' => 0,
        'da_thu' => 3000000,
        'trang_thai' => 1,
        'trang_thai_thanh_toan' => 2,
        'khach_hang_ten' => 'Chu Tau Nam Du',
        'khach_hang_sdt' => '0901234567',
        'dia_chi_text' => 'Ben tau Rach Gia, Kien Giang',
    ]);

    $shippingData = [
        'ten_nha_xe' => 'Chanh Xe To Chau',
        'bien_so_xe' => '68C-999.88',
        'ten_tai_xe' => 'Bac Tai Sau',
        'sdt_tai_xe' => '0919888777',
        'so_kien_hang' => 2,
        'dia_diem_tra_hang' => 'Ben tau Rach Gia',
        'notify_customer' => true,
        'notify_driver' => true,
    ];

    $response = $this->actingAs($this->user)->postJson('/api/admin/thong-bao/gui-chanh-xe/' . $order->id, $shippingData);
    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
             ]);

    $this->assertDatabaseHas('lich_su_thong_baos', [
        'so_dien_thoai' => '0901234567',
        'ma_tham_chieu' => 'DH-TEST-SHIP-003',
    ]);

    $this->assertDatabaseHas('lich_su_thong_baos', [
        'so_dien_thoai' => '0919888777',
        'ma_tham_chieu' => 'DH-TEST-SHIP-003',
    ]);
});

it('can flush and warm up catalog cache', function () {
    $response = $this->actingAs($this->user)->postJson('/api/admin/he-thong/clear-cache', [
        'warmup' => true,
    ]);

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
             ])
             ->assertJsonStructure([
                 'stats' => [
                     'thuong_hieu',
                     'loai_dong_co',
                     'dong_co_model',
                     'nhom_phu_tung',
                 ]
             ]);
});

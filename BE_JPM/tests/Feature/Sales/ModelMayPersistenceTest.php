<?php

namespace Tests\Feature\Sales;

use App\Domain\IAM\Models\User;
use App\Domain\MarineCatalog\Models\ThuongHieu;
use App\Domain\PIM\Models\NhomPhuTung;
use App\Domain\PIM\Models\PhuTung;
use App\Domain\Sales\Models\ChiTietBaoGia;
use App\Domain\Sales\Models\ChiTietDonHang;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\YeuCauBaoGia;
use App\Domain\Sales\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $this->admin->assignRole($role);

    $brand = ThuongHieu::create([
        'code' => 'MER',
        'name' => 'Mercury Marine',
        'status' => 1,
    ]);
    $group = NhomPhuTung::create([
        'ma_nhom' => 'COWL',
        'ten_nhom' => 'Accent Panel',
        'trang_thai' => 1,
    ]);

    $this->partA = PhuTung::create([
        'thuong_hieu_id' => $brand->id,
        'nhom_phu_tung_id' => $group->id,
        'ma_phu_tung' => '8M0142205',
        'ten_phu_tung' => 'ACCENT COWL ASSEMBLY Redline Red',
        'gia_ban' => 1000000,
        'is_gia_lien_he' => false,
        'trang_thai' => 1,
    ]);
    $this->partB = PhuTung::create([
        'thuong_hieu_id' => $brand->id,
        'nhom_phu_tung_id' => $group->id,
        'ma_phu_tung' => 'LKR6E',
        'ten_phu_tung' => 'Bu gi Iridium',
        'gia_ban' => 250000,
        'is_gia_lien_he' => false,
        'trang_thai' => 1,
    ]);
});

it('persists model_may on quote items and carries it over when converting to an order', function () {
    $res = $this->postJson('/api/public/yeu-cau-bao-gia', [
        'loai_yeu_cau' => 'quote',
        'khach_hang_ten' => 'Nguyen Van A',
        'khach_hang_sdt' => '0901234567',
        'chi_tiet' => [
            [
                'id_phu_tung' => $this->partA->id,
                'loai_san_pham' => 'phu_tung',
                'ten_phu_tung' => $this->partA->ten_phu_tung,
                'ma_phu_tung' => $this->partA->ma_phu_tung,
                'model_may' => 'MERCURY 300HP V8 2B529482 & Up',
                'so_luong' => 1,
            ],
            [
                'id_phu_tung' => $this->partA->id,
                'loai_san_pham' => 'phu_tung',
                'ten_phu_tung' => $this->partA->ten_phu_tung,
                'ma_phu_tung' => $this->partA->ma_phu_tung,
                'model_may' => 'MERCURY 250HP V8',
                'so_luong' => 2,
            ],
            [
                'id_phu_tung' => $this->partB->id,
                'loai_san_pham' => 'phu_tung',
                'ten_phu_tung' => $this->partB->ten_phu_tung,
                'ma_phu_tung' => $this->partB->ma_phu_tung,
                'so_luong' => 1,
            ],
        ],
    ]);

    $res->assertStatus(200)->assertJsonPath('success', true);

    $quote = YeuCauBaoGia::with('chiTiet')->firstOrFail();
    expect($quote->chiTiet)->toHaveCount(3);
    expect($quote->chiTiet->pluck('model_may')->all())->toBe([
        'MERCURY 300HP V8 2B529482 & Up',
        'MERCURY 250HP V8',
        null,
    ]);

    $updatePayload = [
        'trang_thai' => 1,
        'tong_tien' => 3250000,
        'chi_tiet' => $quote->chiTiet->map(fn ($ct) => [
            'id' => $ct->id,
            'price' => 1000000,
            'qty' => $ct->so_luong,
            'model_may' => $ct->model_may,
        ])->all(),
    ];

    $this->actingAs($this->admin)
        ->putJson("/api/admin/yeu-cau-bao-gia/{$quote->id}", $updatePayload)
        ->assertStatus(200);

    $convertRes = $this->actingAs($this->admin)
        ->postJson("/api/admin/yeu-cau-bao-gia/{$quote->id}/chuyen-don");
    expect($convertRes->status())->toBe(200, $convertRes->getContent());

    $order = DonHang::with('chiTiet')->firstOrFail();
    expect($order->chiTiet)->toHaveCount(3);
    expect($order->chiTiet->pluck('model_may')->all())->toBe([
        'MERCURY 300HP V8 2B529482 & Up',
        'MERCURY 250HP V8',
        null,
    ]);
});

it('processes model_may in OrderService and exposes it on order items', function () {
    $donHang = DonHang::create([
        'ma_don_hang' => 'DH-TEST-MODEL-01',
        'khach_hang_ten' => 'Tran Van B',
        'khach_hang_sdt' => '0987654321',
        'tong_tien' => 2000000,
        'trang_thai' => 0,
        'trang_thai_thanh_toan' => 0,
    ]);

    $service = new OrderService();
    $processed = $service->processItemsAndInventory([
        [
            'id_phu_tung' => $this->partA->id,
            'loai_san_pham' => 'phu_tung',
            'so_luong' => 2,
            'model_may' => 'YAMAHA F200',
        ],
    ], $donHang->id, true);

    foreach ($processed['items'] as $row) {
        ChiTietDonHang::create($row);
    }

    $detail = ChiTietDonHang::firstOrFail();
    expect($detail->model_may)->toBe('YAMAHA F200');
});

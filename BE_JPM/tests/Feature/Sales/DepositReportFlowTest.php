<?php

namespace Tests\Feature\Sales;

use App\Domain\Communication\Models\LichSuThongBao;
use App\Domain\IAM\Models\User;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\YeuCauBaoGia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $this->admin->assignRole($role);
});

it('records customer deposit report without receipt, keeps quote awaiting verification, and logs admin alert', function () {
    $quote = YeuCauBaoGia::create([
        'ma_yeu_cau' => 'YCBG-TEST-DEP-01',
        'loai_yeu_cau' => 'quote',
        'khach_hang_ten' => 'Nguyen Van Test',
        'khach_hang_sdt' => '0901112233',
        'tong_tien' => 31499750,
        'tien_coc_yeu_cau' => 20000000,
        'da_thu' => 0,
        'trang_thai' => 1,
        'token' => 'tok-dep-test-01',
    ]);

    $res = $this->postJson('/api/public/yeu-cau-bao-gia/tok-dep-test-01/action', [
        'action' => 'report_deposit',
    ]);

    $res->assertStatus(200)->assertJsonPath('success', true);

    $quote->refresh();
    expect((int) $quote->trang_thai)->toBe(1);
    expect((float) $quote->da_thu)->toBe(0.0);
    expect((bool) $quote->da_bao_chuyen_khoan)->toBeTrue();
    expect($quote->thoi_gian_bao_coc)->not->toBeNull();

    $alert = LichSuThongBao::where('ma_tham_chieu', 'YCBG-TEST-DEP-01')
        ->where('mau_thong_bao', 'khach_bao_coc')
        ->first();
    expect($alert)->not->toBeNull();
    expect($alert->nguoi_nhan_loai)->toBe('admin');
});

it('uploads customer receipt image on report_deposit and preserves it when admin confirms deposit', function () {
    $quote = YeuCauBaoGia::create([
        'ma_yeu_cau' => 'YCBG-TEST-DEP-02',
        'loai_yeu_cau' => 'quote',
        'khach_hang_ten' => 'Tran Van Bill',
        'khach_hang_sdt' => '0904445566',
        'tong_tien' => 15000000,
        'tien_coc_yeu_cau' => 5000000,
        'da_thu' => 0,
        'trang_thai' => 1,
        'token' => 'tok-dep-test-02',
    ]);

    $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
    $fakeImage = UploadedFile::fake()->createWithContent('bien-lai.png', $pngBytes);

    $res = $this->post('/api/public/yeu-cau-bao-gia/tok-dep-test-02/action', [
        'action' => 'report_deposit',
        'file' => $fakeImage,
    ], ['Accept' => 'application/json']);

    $res->assertStatus(200)->assertJsonPath('success', true);

    $quote->refresh();
    expect($quote->anh_chuyen_khoan)->not->toBeEmpty();
    expect((bool) $quote->da_bao_chuyen_khoan)->toBeTrue();

    // Admin rejects report if money not arrived
    $rejectRes = $this->actingAs($this->admin)
        ->postJson("/api/admin/yeu-cau-bao-gia/{$quote->id}/tu-choi-bao-coc");
    $rejectRes->assertStatus(200);
    $quote->refresh();
    expect((bool) $quote->da_bao_chuyen_khoan)->toBeFalse();
    expect($quote->thoi_gian_bao_coc)->toBeNull();

    // Customer reports again & admin confirms deposit
    $this->postJson('/api/public/yeu-cau-bao-gia/tok-dep-test-02/action', [
        'action' => 'report_deposit',
    ])->assertStatus(200);

    $confirmRes = $this->actingAs($this->admin)
        ->postJson("/api/admin/yeu-cau-bao-gia/{$quote->id}/xac-nhan-coc", [
            'amount' => 5000000,
        ]);
    $confirmRes->assertStatus(200);

    $order = DonHang::where('ghi_chu_khach_hang', 'like', '%YCBG-TEST-DEP-02%')->firstOrFail();
    expect($order->anh_chuyen_khoan)->toBe($quote->anh_chuyen_khoan);
    expect((float) $order->da_thu)->toBe(5000000.0);
    expect((int) $order->trang_thai)->toBe(1);

    \App\Domain\Core\Services\ImageUploadService::delete($order->anh_chuyen_khoan);
});

it('verifies phone ownership for direct order reportDeposit and supports admin rejectDepositReport', function () {
    $order = DonHang::create([
        'ma_don_hang' => 'DH-TEST-DEP-03',
        'khach_hang_ten' => 'Le Thi C',
        'khach_hang_sdt' => '0912345678',
        'tong_tien' => 10000000,
        'tien_coc_yeu_cau' => 3000000,
        'da_thu' => 0,
        'trang_thai' => 0,
        'trang_thai_thanh_toan' => 0,
    ]);

    // Wrong phone is rejected
    $this->postJson('/api/public/don-hang/DH-TEST-DEP-03/bao-chuyen-khoan', [
        'phone' => '0999999999',
    ])->assertStatus(403);

    // Matching phone succeeds
    $this->postJson('/api/public/don-hang/DH-TEST-DEP-03/bao-chuyen-khoan', [
        'phone' => '0912345678',
    ])->assertStatus(200);

    $order->refresh();
    expect((bool) $order->da_bao_chuyen_khoan)->toBeTrue();
    expect((int) $order->trang_thai)->toBe(0);

    // Admin rejects report
    $this->actingAs($this->admin)
        ->postJson("/api/admin/don-hang/{$order->id}/tu-choi-bao-coc")
        ->assertStatus(200);

    $order->refresh();
    expect((bool) $order->da_bao_chuyen_khoan)->toBeFalse();
});

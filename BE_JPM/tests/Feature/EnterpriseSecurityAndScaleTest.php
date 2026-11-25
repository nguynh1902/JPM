<?php

namespace Tests\Feature;

use App\Domain\IAM\Models\User;
use App\Domain\IAM\Models\NhanVien;
use App\Domain\Procurement\Models\Kho;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Sales\Models\YeuCauBaoGia;
use App\Domain\CMS\Models\SanPham;
use App\Domain\CMS\Models\DanhMucSanPham;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    $perms = [
        'Xem Tin tức & CMS', 'Soạn thảo Tin tức & CMS', 'Xóa Tin tức & CMS',
        'Xem Đơn hàng', 'Duyệt Đơn hàng', 'Sửa Đơn hàng', 'Xóa Đơn hàng',
        'Xem Tồn kho', 'Cập nhật Tồn kho', 'Xem Thẻ kho', 'Quản lý Danh sách kho',
        'Quản lý Nhân sự', 'Quản lý Vai trò', 'Cấu hình chung',
    ];
    foreach ($perms as $p) {
        Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
    }

    $this->adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $this->staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
    $this->staffRole->syncPermissions(['Xem Đơn hàng', 'Xem Tin tức & CMS', 'Xem Tồn kho', 'Xem Thẻ kho']);

    $this->hrRole = Role::firstOrCreate(['name' => 'hr_manager', 'guard_name' => 'web']);
    $this->hrRole->syncPermissions(['Quản lý Nhân sự']);
});

it('never leaks reset_token in forgot-password and rejects password reset without valid token', function () {
    $user = User::factory()->create(['email' => 'owner@jpm.vn', 'password' => Hash::make('OldPass123!')]);

    $forgot = $this->postJson('/api/forgot-password', ['email' => 'owner@jpm.vn']);
    $forgot->assertStatus(200)
        ->assertJsonMissingPath('reset_token')
        ->assertJson(['success' => true]);

    // Attempting password reset without token must fail validation (422)
    $resetNoToken = $this->postJson('/api/reset-password', [
        'email' => 'owner@jpm.vn',
        'password' => 'HackedPass99!',
        'password_confirmation' => 'HackedPass99!',
    ]);
    $resetNoToken->assertStatus(422);

    // Attempting with invalid token must fail (422)
    $resetBadToken = $this->postJson('/api/reset-password', [
        'email' => 'owner@jpm.vn',
        'token' => str_repeat('a', 64),
        'password' => 'HackedPass99!',
        'password_confirmation' => 'HackedPass99!',
    ]);
    $resetBadToken->assertStatus(422);

    // Password remains unchanged
    expect(Hash::check('OldPass123!', $user->fresh()->password))->toBeTrue();
});

it('blocks unauthenticated payment webhooks and correctly sets payment_status 1 (deposit) vs 2 (full paid)', function () {
    $order = DonHang::create([
        'ma_don_hang' => 'DH-SCALE-001',
        'tong_tien' => 1000000,
        'phi_van_chuyen' => 50000,
        'khach_tu_tra_cuoc' => 1,
        'tien_coc_yeu_cau' => 300000,
        'da_thu' => 0,
        'trang_thai' => 0,
        'trang_thai_thanh_toan' => 0,
        'khach_hang_ten' => 'Chu Tau A',
        'khach_hang_sdt' => '0911111111',
    ]);

    $token = config('services.sepay.webhook_token');

    // Missing auth -> 401
    $this->postJson('/api/webhooks/payment/reconciliation', [
        'amountIn' => 300000,
        'transactionContent' => 'COC DH-SCALE-001',
        'referenceNumber' => 'TX-DEP-01',
    ])->assertStatus(401);

    // Partial deposit -> trang_thai_thanh_toan = 1 (Đã đặt cọc)
    $this->withHeaders(['Authorization' => 'Apikey ' . $token])
        ->postJson('/api/webhooks/payment/reconciliation', [
            'amountIn' => 300000,
            'transactionContent' => 'COC DH-SCALE-001',
            'referenceNumber' => 'TX-DEP-01',
        ])
        ->assertStatus(200)
        ->assertJson(['success' => true, 'payment_status' => 1]);

    // Remaining balance (khach_tu_tra_cuoc = 1 so shipping fee not added -> 700,000 completes 1,000,000)
    $this->withHeaders(['X-Webhook-Token' => $token])
        ->postJson('/api/webhooks/payment/reconciliation', [
            'amountIn' => 700000,
            'transactionContent' => 'TT DH-SCALE-001',
            'referenceNumber' => 'TX-FULL-02',
        ])
        ->assertStatus(200)
        ->assertJson(['success' => true, 'payment_status' => 2]);
});

it('enforces RBAC: blocks read-only staff from CMS delete & Sales write, and blocks HR from resetting admin', function () {
    $staff = User::factory()->create();
    $staff->assignRole($this->staffRole);

    $hr = User::factory()->create();
    $hr->assignRole($this->hrRole);

    $admin = User::factory()->create();
    $admin->assignRole($this->adminRole);
    $adminNv = NhanVien::create([
        'ma_nhan_vien' => 'NV01',
        'id_tai_khoan' => $admin->id,
        'ho_ten' => 'Super Admin',
        'so_dien_thoai' => '0900000001',
    ]);

    // Read-only staff CANNOT delete CMS or mutate orders
    $this->actingAs($staff)->deleteJson('/api/admin/tin-tuc/1')->assertStatus(403);
    $this->actingAs($staff)->postJson('/api/admin/yeu-cau-bao-gia', [])->assertStatus(403);

    // Read-only staff CAN access restored Warehouse GET routes
    Kho::create(['ma_kho' => 'KHO001', 'ten_kho' => 'Kho Tong', 'trang_thai' => 1]);
    $this->actingAs($staff)->getJson('/api/admin/kho')->assertStatus(200);
    $this->actingAs($staff)->getJson('/api/admin/ton-kho')->assertStatus(200);
    $this->actingAs($staff)->getJson('/api/admin/the-kho')->assertStatus(200);

    // Non-admin HR manager CANNOT reset Super Admin password or assign admin role
    $this->actingAs($hr)->postJson('/api/admin/nhan-vien/' . $adminNv->id . '/reset-password')->assertStatus(403);
    $this->actingAs($hr)->postJson('/api/admin/nhan-vien', [
        'name' => 'Rogue Admin',
        'email' => 'rogue@jpm.vn',
        'phone' => '0987654329',
        'role' => 'admin',
    ])->assertStatus(403);
});

it('ignores spoofed user_id on publicStore, rejects fake base64 payloads, and caps public pagination', function () {
    $victim = User::factory()->create(['email' => 'victim@jpm.vn', 'phone' => '0912000111']);

    $res = $this->postJson('/api/public/yeu-cau-bao-gia', [
        'user_id' => $victim->id,
        'loai_yeu_cau' => 'quote',
        'khach_hang_ten' => 'Attacker',
        'khach_hang_sdt' => '0999888777',
        'chi_tiet' => [
            ['ten_phu_tung' => 'Custom part', 'ma_phu_tung' => 'FAKE-SKU', 'so_luong' => 2, 'gia_ban' => 1],
        ],
    ]);
    $res->assertStatus(200);

    $quote = YeuCauBaoGia::latest('id')->first();
    expect($quote->user_id)->toBeNull();
    expect((float) $quote->tong_tien)->toBe(0.0);

    // Reject unsafe SVG/PHP base64 upload on receipt endpoint
    $admin = User::factory()->create();
    $admin->assignRole($this->adminRole);

    $this->actingAs($admin)->postJson('/api/admin/sales/upload-bien-lai', [
        'image_base64' => 'data:image/php;base64,' . base64_encode('<?php echo "pwn"; ?>'),
    ])->assertStatus(422);

    // Public products ?all=1 and per_page=99999 are safely paginated and capped <= 100
    $cat = DanhMucSanPham::create(['ten_danh_muc' => 'Vat Tu', 'slug' => 'vat-tu', 'trang_thai' => 1]);
    SanPham::create([
        'danh_muc_san_pham_id' => $cat->id,
        'ma_san_pham' => 'SP-001',
        'ten_san_pham' => 'Dau Nhot',
        'slug' => 'dau-nhot',
        'gia_ban' => 250000,
        'trang_thai' => 1,
    ]);

    $prodRes = $this->getJson('/api/public/san-pham?all=1&per_page=99999');
    $prodRes->assertStatus(200);
    expect($prodRes->json('data.per_page'))->toBeLessThanOrEqual(100);
});

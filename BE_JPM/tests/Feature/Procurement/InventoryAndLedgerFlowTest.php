<?php

namespace Tests\Feature\Procurement;

use App\Domain\IAM\Models\User;
use App\Domain\MarineCatalog\Models\ThuongHieu;
use App\Domain\PIM\Models\NhomPhuTung;
use App\Domain\PIM\Models\PhuTung;
use App\Domain\Procurement\Models\Kho;
use App\Domain\Procurement\Models\TheKho;
use App\Domain\Procurement\Models\TonKho;
use App\Domain\Sales\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create();
    $role = Role::firstOrCreate(['name' => 'admin']);
    $this->admin->assignRole($role);

    $brand = ThuongHieu::create([
        'code' => 'YAM',
        'name' => 'Yamaha Marine',
        'status' => 1,
    ]);
    $group = NhomPhuTung::create([
        'ma_nhom' => 'LOC',
        'ten_nhom' => 'Loc nhot',
        'trang_thai' => 1,
    ]);

    $this->part = PhuTung::create([
        'thuong_hieu_id' => $brand->id,
        'nhom_phu_tung_id' => $group->id,
        'ma_phu_tung' => '69J-13440-04',
        'ten_phu_tung' => 'Loc nhot dong co Yamaha',
        'gia_ban' => 450000,
        'is_gia_lien_he' => false,
        'trang_thai' => 1,
    ]);
});

it('validates unique warehouse names, blocks deleting stocked warehouses, and preserves soft-deleted names', function () {
    $createRes = $this->actingAs($this->admin)->postJson('/api/admin/kho', [
        'ten_kho' => 'Kho Sai Gon',
        'dia_chi' => 'Quan 7',
        'trang_thai' => 1,
    ]);
    $createRes->assertStatus(200)->assertJsonPath('success', true);
    $khoId = $createRes->json('data.id');
    expect($createRes->json('data.ma_kho'))->toBe('KHO001');

    // Duplicate name (case-insensitive) -> 422
    $this->actingAs($this->admin)->postJson('/api/admin/kho', [
        'ten_kho' => 'kho sai gon',
        'trang_thai' => 1,
    ])->assertStatus(422);

    // Add stock 5 -> cannot delete warehouse
    $tonRes = $this->actingAs($this->admin)->postJson('/api/admin/ton-kho', [
        'id_kho' => $khoId,
        'id_san_pham' => $this->part->id,
        'loai_san_pham' => 'phu_tung',
        'so_luong_ton' => 5,
    ]);
    $tonRes->assertStatus(200);
    $tonId = $tonRes->json('data.id');

    $this->actingAs($this->admin)->deleteJson('/api/admin/kho/' . $khoId)->assertStatus(400);

    // Adjust to 0 -> can delete warehouse, and ledger keeps soft-deleted warehouse name
    $this->actingAs($this->admin)->putJson('/api/admin/ton-kho/' . $tonId, [
        'so_luong_ton' => 0,
        'ghi_chu' => 'Xuat het hang truoc khi dong kho',
    ])->assertStatus(200);

    $this->actingAs($this->admin)->deleteJson('/api/admin/kho/' . $khoId)->assertStatus(200);

    $ledger = $this->actingAs($this->admin)->getJson('/api/admin/the-kho?warehouse_id=' . $khoId);
    $ledger->assertStatus(200);
    expect($ledger->json('data.0.kho.ten_kho'))->toBe('Kho Sai Gon');
});

it('rejects stock creation on locked warehouses, forbids changing warehouse on update, and records DIEU_CHINH ledger entries', function () {
    $activeKho = Kho::create(['ma_kho' => 'KHO001', 'ten_kho' => 'Kho Chinh', 'trang_thai' => 1]);
    $lockedKho = Kho::create(['ma_kho' => 'KHO002', 'ten_kho' => 'Kho Tam Khoa', 'trang_thai' => 0]);

    // Locked warehouse rejects new stock row
    $this->actingAs($this->admin)->postJson('/api/admin/ton-kho', [
        'id_kho' => $lockedKho->id,
        'id_san_pham' => $this->part->id,
        'loai_san_pham' => 'phu_tung',
        'so_luong_ton' => 3,
    ])->assertStatus(422);

    // Active warehouse accepts initial stock and logs NHAP_KHO
    $createTon = $this->actingAs($this->admin)->postJson('/api/admin/ton-kho', [
        'id_kho' => $activeKho->id,
        'id_san_pham' => $this->part->id,
        'loai_san_pham' => 'phu_tung',
        'so_luong_ton' => 10,
        'ghi_chu' => 'Nhap dau ky kho chinh',
    ])->assertStatus(200);

    $tonId = $createTon->json('data.id');

    // Direct warehouse reassignment via PUT is forbidden (422)
    $this->actingAs($this->admin)->putJson('/api/admin/ton-kho/' . $tonId, [
        'id_kho' => $lockedKho->id,
        'so_luong_ton' => 10,
    ])->assertStatus(422);

    // Stocktake decrease logs DIEU_CHINH with custom reason
    $this->actingAs($this->admin)->putJson('/api/admin/ton-kho/' . $tonId, [
        'so_luong_ton' => 7,
        'ghi_chu' => 'Kiem ke hu hong 3 cai',
    ])->assertStatus(200);

    // Filter ledger by DIEU_CHINH even if part is soft-deleted
    $this->part->delete();

    $ledgerRes = $this->actingAs($this->admin)->getJson('/api/admin/the-kho?loai_giao_dich=DIEU_CHINH&keyword=69J-13440-04');
    $ledgerRes->assertStatus(200)
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.loai_giao_dich', 'DIEU_CHINH')
        ->assertJsonPath('data.0.so_luong_thay_doi', -3)
        ->assertJsonPath('data.0.so_luong_ton_cuoi', 7)
        ->assertJsonPath('data.0.ghi_chu', 'Kiem ke hu hong 3 cai');
});

it('computes accurate XNT period summary and OrderService skips locked warehouses', function () {
    $lockedKho = Kho::create(['ma_kho' => 'KHO001', 'ten_kho' => 'Kho Khoa', 'trang_thai' => 0]);
    $activeKho = Kho::create(['ma_kho' => 'KHO002', 'ten_kho' => 'Kho Hoat Dong', 'trang_thai' => 1]);

    // Locked kho has 20 units, active kho has 8 units
    TonKho::create([
        'id_kho' => $lockedKho->id,
        'id_san_pham' => $this->part->id,
        'loai_san_pham' => 'phu_tung',
        'so_luong_ton' => 20,
        'so_luong_dang_giu' => 0,
    ]);

    $activeTon = $this->actingAs($this->admin)->postJson('/api/admin/ton-kho', [
        'id_kho' => $activeKho->id,
        'id_san_pham' => $this->part->id,
        'loai_san_pham' => 'phu_tung',
        'so_luong_ton' => 8,
    ])->json('data.id');

    // Simulate customer order deducting 3 units -> must come from activeKho (not lockedKho!)
    $orderService = new OrderService();
    $orderService->processItemsAndInventory([
        [
            'id_san_pham' => $this->part->id,
            'loai_san_pham' => 'phu_tung',
            'so_luong' => 3,
        ]
    ], 99);

    expect(TonKho::where('id_kho', $lockedKho->id)->value('so_luong_ton'))->toBe(20);
    expect(TonKho::find($activeTon)->so_luong_ton)->toBe(5);

    // Adjust -1 unit
    $this->actingAs($this->admin)->putJson('/api/admin/ton-kho/' . $activeTon, [
        'so_luong_ton' => 4,
    ])->assertStatus(200);

    // Verify XNT summary report math for activeKho: 0 opening + 8 in - 3 out - 1 adj = 4 closing
    $today = now()->toDateString();
    $xntRes = $this->actingAs($this->admin)->getJson("/api/admin/the-kho/bao-cao-xnt?warehouse_id={$activeKho->id}&tu_ngay={$today}&den_ngay={$today}");
    $xntRes->assertStatus(200)
        ->assertJsonPath('totals.ton_dau_ky', 0)
        ->assertJsonPath('totals.nhap_trong_ky', 8)
        ->assertJsonPath('totals.xuat_trong_ky', 3)
        ->assertJsonPath('totals.dieu_chinh_trong_ky', -1)
        ->assertJsonPath('totals.ton_cuoi_ky', 4);
});

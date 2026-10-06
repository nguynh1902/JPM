<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Domain\IAM\Models\User;
use App\Domain\IAM\Models\KhachHang;
use App\Domain\Sales\Models\DonHang;
use App\Domain\Core\Resources\DonHangResource;
use App\Domain\Core\Resources\PhienBanResource;
use App\Domain\MarineCatalog\Models\PhienBan;

$baseUrl = 'http://localhost:8000/api';
$results = [];

function assertTest($name, $condition, $details = '') {
    global $results;
    $results[] = [
        'step' => $name,
        'pass' => $condition,
        'details' => $details
    ];
    if (!$condition) {
        echo "FAIL: $name - $details\n";
    } else {
        echo "PASS: $name\n";
    }
}

// Ensure Admin Exists
$admin = User::where('email', 'admin_super@example.com')->first();
$adminToken = $admin->createToken('admin')->plainTextToken;

// ==========================================
// 1. CUSTOMER DELETE — TEST CASE #0001
// ==========================================
echo "\n--- TEST CASE #0001: CUSTOMER DELETE ---\n";
// Create Customer without Order
$u1 = User::create(['name'=>'Test Del 1', 'email'=>uniqid('d1_').'@test.com', 'password'=>bcrypt('123'), 'status'=>1]);
$kh1 = KhachHang::create(['id_tai_khoan'=>$u1->id, 'ho_ten'=>'Test Del 1', 'so_dien_thoai'=>rand(100000000, 999999999)]);

$resDel1 = Http::withToken($adminToken)->delete($baseUrl . '/admin/khach-hang/' . $kh1->id);
assertTest('Customer Delete (No Order)', $resDel1->status() === 200, "Status: " . $resDel1->status() . " Body: " . $resDel1->body());
assertTest('Customer Delete DB Check', KhachHang::find($kh1->id) === null, 'Record should be soft/hard deleted');

// Create Customer WITH Order
$u2 = User::create(['name'=>'Test Del 2', 'email'=>uniqid('d2_').'@test.com', 'password'=>bcrypt('123'), 'status'=>1]);
$kh2 = KhachHang::create(['id_tai_khoan'=>$u2->id, 'ho_ten'=>'Test Del 2', 'so_dien_thoai'=>rand(100000000, 999999999)]);
DonHang::create(['ma_don_hang'=>uniqid('DH_'), 'id_khach_hang'=>$kh2->id, 'tong_tien'=>1000]);

$resDel2 = Http::withToken($adminToken)->delete($baseUrl . '/admin/khach-hang/' . $kh2->id);
assertTest('Customer Delete (With Order)', $resDel2->status() === 500, "Status: " . $resDel2->status() . " Body: " . $resDel2->body());
// Check if error is friendly or SQLSTATE
$body2 = $resDel2->json();
$isFriendly = isset($body2['message']) && strpos($body2['message'], 'Khng th? xa') !== false;
assertTest('Customer Delete Error is Friendly', $isFriendly, 'Should not throw SQLSTATE');

// Clean up
DonHang::where('id_khach_hang', $kh2->id)->delete();
$kh2->delete(); $u2->delete();

// ==========================================
// 2. RESOURCE SERIALIZATION REGRESSION (The 14 fixes)
// ==========================================
echo "\n--- RESOURCE SERIALIZATION ---\n";
try {
    $dh = DonHang::create(['ma_don_hang'=>uniqid('DHR_'), 'id_khach_hang'=>1, 'tong_tien'=>1000]);
    $res = DonHangResource::make($dh)->resolve();
    assertTest('DonHangResource Serialization', isset($res['id_don_hang']), 'Resource failed to serialize id_don_hang');
    $dh->delete();
} catch (\Exception $e) {
    assertTest('DonHangResource Serialization', false, 'Exception: ' . $e->getMessage());
}

try {
    $pb = PhienBan::create(['ten_phien_ban'=>'Test PB']);
    $res = PhienBanResource::make($pb)->resolve();
    assertTest('PhienBanResource Serialization', isset($res['id_phien_ban']) || array_key_exists('id_phien_ban', $res), 'Resource serialized');
    $pb->delete();
} catch (\Exception $e) {
    assertTest('PhienBanResource Serialization', false, 'Exception: ' . $e->getMessage());
}

file_put_contents('runtime_regression_results.json', json_encode($results, JSON_PRETTY_PRINT));
echo "\nTests completed.\n";




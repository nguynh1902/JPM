<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Domain\IAM\Models\User;
use App\Domain\IAM\Models\KhachHang;
use Spatie\Permission\Models\Role;

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
$admin = User::firstOrCreate(
    ['email' => 'admin_super@example.com'],
    ['name' => 'Super Admin', 'password' => bcrypt('password123'), 'phone' => '0999999999']
);
Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']); // if multiple guards
$admin->assignRole('admin');
$adminToken = $admin->createToken('admin')->plainTextToken;

// ==========================================
// FLOW 01: CUSTOMER INTEGRATION
// ==========================================
echo "\n--- FLOW 01: CUSTOMER INTEGRATION ---\n";
$testEmail = 'customer_e2e_' . time() . '@example.com';
$testPhone = '09' . rand(10000000, 99999999);

// 1. Client Register
$resReg = Http::post($baseUrl . '/register', [
    'ho' => 'Nguyen',
    'ten' => 'Van A',
    'email' => $testEmail,
    'password' => 'pass1234',
    'password_confirmation' => 'pass1234',
    'phone' => $testPhone
]);
assertTest('Client Register (API)', $resReg->status() === 201, "Status: " . $resReg->status());

// Check DB directly
$customerUser = User::where('email', $testEmail)->first();
$khachHang = KhachHang::where('id_tai_khoan', $customerUser->id)->first();
assertTest('Client Register (DB)', $customerUser && $customerUser->name === 'Nguyen Van A' && $khachHang, 'DB user & khachhang must map correctly from ho + ten');

// 2. Client Login & Profile
$resLogin = Http::post($baseUrl . '/login', [
    'login_id' => $testEmail,
    'password' => 'pass1234'
]);
assertTest('Client Login (API)', $resLogin->status() === 200, "Status: " . $resLogin->status());
$clientToken = $resLogin->json('data.token');

$resProfile = Http::withToken($clientToken)->get($baseUrl . '/client/profile');
assertTest('Client Profile (API)', $resProfile->json('name') === 'Nguyen Van A', 'Profile name mismatch');

// 3. Admin Edit Customer (Update phone & name)
$newPhone = '09' . rand(10000000, 99999999);
$resAdminEdit = Http::withToken($adminToken)->withHeaders(['Accept' => 'application/json'])
    ->post($baseUrl . '/admin/khach-hang/' . $khachHang->id . '?_method=PUT', [
        'name' => 'Nguyen Van B',
        'phone' => $newPhone,
        'email' => $testEmail,
        'status' => 1
    ]);
assertTest('Admin Edit Customer (API)', $resAdminEdit->status() === 200, "Status: " . $resAdminEdit->status() . " Response: " . $resAdminEdit->body());

// Check DB directly
$customerUser->refresh();
assertTest('Admin Edit Customer (DB)', $customerUser->name === 'Nguyen Van B', 'DB name must be updated by Admin');

// 4. Client Read Updated Profile
$resProfileUpdated = Http::withToken($clientToken)->get($baseUrl . '/client/profile');
assertTest('Client Read Updated Profile (API)', $resProfileUpdated->json('name') === 'Nguyen Van B' && $resProfileUpdated->json('phone') === $newPhone, 'Profile did not sync from Admin update');

// 5. Admin Reset Password
$resResetPw = Http::withToken($adminToken)->post($baseUrl . '/admin/khach-hang/' . $khachHang->id . '/reset-password');
assertTest('Admin Reset Password (API)', $resResetPw->status() === 200, "Status: " . $resResetPw->status());

// Try to login with old password
$resLoginFail = Http::post($baseUrl . '/login', [
    'login_id' => $testEmail,
    'password' => 'pass1234'
]);
assertTest('Client Login Blocked after Reset', $resLoginFail->status() === 401 || $resLoginFail->status() === 422 || $resLoginFail->json('success') === false, "Old password should fail");

file_put_contents('e2e_reverification_results.json', json_encode($results, JSON_PRETTY_PRINT));
echo "\nTests completed.\n";

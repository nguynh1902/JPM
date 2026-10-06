<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Domain\IAM\Models\User;

$baseUrl = 'http://localhost:8000/api';
$results = [];

function runTest($name, $method, $url, $data = [], $token = null) {
    global $baseUrl, $results;
    try {
        $req = Http::withHeaders(['Accept' => 'application/json']);
        if ($token) {
            $req = $req->withToken($token);
        }
        $res = $req->$method($baseUrl . $url, $data);
        
        $body = $res->json();
        
        $results[] = [
            'name' => $name,
            'url' => $url,
            'status' => $res->status(),
            'pass' => $res->status() >= 200 && $res->status() < 300,
            'data' => $body
        ];
        return $body;
    } catch (\Exception $e) {
        $results[] = [
            'name' => $name,
            'url' => $url,
            'status' => 'Exception',
            'pass' => false,
            'error' => $e->getMessage()
        ];
        return null;
    }
}

// Ensure an admin user exists
$admin = User::firstOrCreate(
    ['email' => 'admin_e2e@example.com'],
    [
        'name' => 'Admin Test',
        'password' => bcrypt('password123'),
        'phone' => '0999999999'
    ]
);
// Assign admin role if spatie is used.
try {
    if (!$admin->hasRole('admin')) {
        $admin->assignRole('admin');
    }
} catch (\Exception $e) {
    // If roles table empty, maybe just bypass
}

$token = $admin->createToken('admin-test')->plainTextToken;

// 1. Thương Hiệu CRUD
$brandRes = runTest('Create Brand', 'post', '/admin/thuong-hieu', [
    'name' => 'Test Brand',
    // code is auto-generated
], $token);

if (isset($brandRes['data']['id'])) {
    $brandId = $brandRes['data']['id'];
    
    // Check auto-code
    $autoCode = $brandRes['data']['code'] ?? '';
    if (empty($autoCode)) {
        $results[] = ['name' => 'Auto Code Check', 'pass' => false, 'error' => 'Code is empty'];
    } else {
        $results[] = ['name' => 'Auto Code Check', 'pass' => true, 'code' => $autoCode];
    }

    runTest('Update Brand', 'put', '/admin/thuong-hieu/' . $brandId, [
        'name' => 'Test Brand Updated',
        'code' => $autoCode // passing it back so it doesn't fail validation if required
    ], $token);
    
    runTest('Delete Brand', 'delete', '/admin/thuong-hieu/' . $brandId, [], $token);
}

// 2. Fetch Dashboard/Logs (just to check access)
runTest('System Logs', 'get', '/admin/system-logs', [], $token);

file_put_contents('e2e_admin_results.json', json_encode($results, JSON_PRETTY_PRINT));
echo "Admin E2E Test Completed. Results written to e2e_admin_results.json\n";

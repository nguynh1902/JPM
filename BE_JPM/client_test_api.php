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
        
        $results[] = [
            'name' => $name,
            'url' => $url,
            'status' => $res->status(),
            'pass' => $res->successful(),
            'data_structure' => array_keys($res->json() ?? [])
        ];
    } catch (\Exception $e) {
        $results[] = [
            'name' => $name,
            'url' => $url,
            'status' => 'Exception',
            'pass' => false,
            'error' => $e->getMessage()
        ];
    }
}

// 2. AUTHENTICATION
$testEmail = 'e2e_test_' . time() . '@example.com';
runTest('Register', 'post', '/register', [
    'ho' => 'E2E',
    'ten' => 'User',
    'email' => $testEmail,
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'phone' => '09' . rand(10000000, 99999999)
]);

runTest('Login', 'post', '/login', [
    'email' => $testEmail,
    'password' => 'password123'
]);

// Try to grab the token directly to test protected routes
$user = User::where('email', $testEmail)->first();
$token = '';
if ($user) {
    $token = $user->createToken('test')->plainTextToken;
    
    // 3. PROTECTED ROUTES
    runTest('Get Profile', 'get', '/client/profile', [], $token);
    runTest('Get Garage', 'get', '/client/garage', [], $token);
    runTest('Get Orders', 'get', '/client/history/orders', [], $token);
    
    // Cleanup
    $user->delete();
}

file_put_contents('e2e_api_results2.json', json_encode($results, JSON_PRETTY_PRINT));
echo "E2E API Test 2 Completed. Results written to e2e_api_results2.json\n";

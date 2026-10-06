<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

class ClientTestSimulator {
    protected $app;
    protected $kernel;

    public function __construct($app, $kernel) {
        $this->app = $app;
        $this->kernel = $kernel;
    }

    public function handle($method, $uri, $data = [], $token = null) {
        $server = ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'];
        if ($token) {
            $server['HTTP_AUTHORIZATION'] = "Bearer $token";
        }
        $request = Illuminate\Http\Request::create($uri, $method, [], [], [], $server, json_encode($data));
        
        $response = $this->kernel->handle($request);
        return $response;
    }

    public function run() {
        $results = [];

        // 1. PUBLIC CATALOG
        $res = $this->handle('GET', '/api/public/thuong-hieu');
        $results['PublicBrands'] = $res->getStatusCode() === 200 ? 'PASS' : 'FAIL ' . $res->getStatusCode();
        
        $res = $this->handle('GET', '/api/public/danh-muc-san-pham');
        $results['PublicCategories'] = $res->getStatusCode() === 200 ? 'PASS' : 'FAIL ' . $res->getStatusCode();
        
        $res = $this->handle('GET', '/api/public/san-pham');
        $results['PublicProducts'] = $res->getStatusCode() === 200 ? 'PASS' : 'FAIL ' . $res->getStatusCode();

        // 2. AUTHENTICATION & PROFILE
        // Create random user
        $email = 'client_test_' . time() . '@jpm.com';
        $res = $this->handle('POST', '/api/register', [
            'ho' => 'Client',
            'ten' => 'Test',
            'email' => $email,
            'phone' => '09' . rand(10000000, 99999999),
            'password' => '12345678',
            'password_confirmation' => '12345678'
        ]);
        $results['AuthRegister'] = ($res->getStatusCode() === 201 || $res->getStatusCode() === 200) ? 'PASS' : 'FAIL ' . $res->getContent();
        
        $res = $this->handle('POST', '/api/login', [
            'login_id' => $email,
            'password' => '12345678'
        ]);
        $loginData = json_decode($res->getContent(), true);
        $token = $loginData['data']['token'] ?? null;
        $results['AuthLogin'] = $token ? 'PASS' : 'FAIL ' . $res->getContent();

        if ($token) {
            $res = $this->handle('GET', '/api/client/profile', [], $token);
            $results['ClientProfile'] = $res->getStatusCode() === 200 ? 'PASS' : 'FAIL ' . $res->getContent();

            // 3. GARAGE
            $res = $this->handle('POST', '/api/client/garage', [
                'dong_co_model_id' => 1,
                'so_seri' => 'SR-' . time()
            ], $token);
            $results['GarageAdd'] = ($res->getStatusCode() === 201 || $res->getStatusCode() === 200) ? 'PASS' : 'FAIL ' . $res->getContent();

            // 4. CHECKOUT (Quote Request)
            $res = $this->handle('POST', '/api/public/yeu-cau-bao-gia', [
                'khach_hang_ten' => 'Client Test',
                'khach_hang_sdt' => '0901234567',
                'chi_tiet' => [
                    [
                        'id_phu_tung' => 1, 
                        'so_luong' => 2,
                        'ten_phu_tung' => 'Test Part',
                        'ma_phu_tung' => 'PT-TEST'
                    ]
                ]
            ]);
            $results['CheckoutQuote'] = ($res->getStatusCode() === 201 || $res->getStatusCode() === 200) ? 'PASS' : 'FAIL ' . $res->getContent();

            // Logout
            $res = $this->handle('POST', '/api/logout', [], $token);
            $results['AuthLogout'] = $res->getStatusCode() === 200 ? 'PASS' : 'FAIL ' . $res->getStatusCode();
        }

        file_put_contents("results.json", json_encode($results, JSON_PRETTY_PRINT));
    }
}

$simulator = new ClientTestSimulator($app, $kernel);
$simulator->run();

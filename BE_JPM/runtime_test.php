<?php

use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Tests\CreatesApplication;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

class AdminRuntimeTester {
    protected $app;
    
    public function __construct($app) {
        $this->app = $app;
    }
    
    public function get($uri) {
        $request = Illuminate\Http\Request::create($uri, 'GET');
        $request->headers->set('Accept', 'application/json');
        return $this->app->handle($request);
    }
    
    public function post($uri, $data = []) {
        $request = Illuminate\Http\Request::create($uri, 'POST', $data);
        $request->headers->set('Accept', 'application/json');
        return $this->app->handle($request);
    }
    
    public function put($uri, $data = []) {
        // use PUT or POST with _method=PUT
        $data['_method'] = 'PUT';
        $request = Illuminate\Http\Request::create($uri, 'POST', $data);
        $request->headers->set('Accept', 'application/json');
        return $this->app->handle($request);
    }
    
    public function delete($uri) {
        $request = Illuminate\Http\Request::create($uri, 'DELETE');
        $request->headers->set('Accept', 'application/json');
        return $this->app->handle($request);
    }
    
    public function run() {
        $admin = \App\Domain\IAM\Models\User::first();
        $this->app->make('auth')->guard('sanctum')->setUser($admin);
        
        $results = [];
        
        // 1. Thương Hiệu (Brand)
        $res = $this->post('/api/admin/thuong-hieu', ['name' => 'RUNTIME BRAND', 'status' => 1]);
        $json = json_decode($res->getContent(), true);
        if ($res->getStatusCode() !== 201) {
            $results['ThuongHieu'] = ['CREATE' => 'FAIL', 'message' => $res->getContent()];
        } else {
            $id = $json['data']['id'] ?? $json['id'];
            $res2 = $this->put("/api/admin/thuong-hieu/{$id}", ['name' => 'UPDATED BRAND', 'status' => 0]);
            $res3 = $this->delete("/api/admin/thuong-hieu/{$id}");
            $results['ThuongHieu'] = [
                'CREATE' => 'PASS',
                'UPDATE' => $res2->getStatusCode() === 200 ? 'PASS' : 'FAIL',
                'DELETE' => $res3->getStatusCode() === 200 ? 'PASS' : 'FAIL',
            ];
        }
        
        // 2. Loại Động Cơ
        $res = $this->post('/api/admin/loai-dong-co', ['ten_loai_dong_co' => 'RUNTIME TYPE', 'trang_thai' => 1]);
        $json = json_decode($res->getContent(), true);
        if ($res->getStatusCode() !== 201 && $res->getStatusCode() !== 200) {
            $results['LoaiDongCo'] = ['CREATE' => 'FAIL', 'message' => $res->getContent()];
        } else {
            $id = $json['data']['id'] ?? $json['id'];
            $res2 = $this->put("/api/admin/loai-dong-co/{$id}", ['ten_loai_dong_co' => 'UPDATED TYPE', 'trang_thai' => 0]);
            if ($res2->getStatusCode() !== 200) {
                echo "LoaiDongCo UPDATE Error: " . $res2->getContent() . "\n";
            }
            $res3 = $this->delete("/api/admin/loai-dong-co/{$id}");
            $results['LoaiDongCo'] = [
                'CREATE' => 'PASS',
                'UPDATE' => $res2->getStatusCode() === 200 ? 'PASS' : 'FAIL',
                'DELETE' => $res3->getStatusCode() === 200 ? 'PASS' : 'FAIL',
            ];
        }

        // 3. Nhân Viên
        $res = $this->post('/api/admin/nhan-vien', ['name' => 'Test NV', 'email' => 'testnv_'.time().'@jpm.com', 'password' => '12345678', 'role' => 'admin', 'status' => 1, 'phone' => '090' . rand(1000000, 9999999)]);
        $json = json_decode($res->getContent(), true);
        if ($res->getStatusCode() !== 201 && $res->getStatusCode() !== 200) {
            $results['NhanVien'] = ['CREATE' => 'FAIL', 'message' => $res->getContent()];
        } else {
            $id = $json['data']['id'] ?? $json['id'];
            $res2 = $this->put("/api/admin/nhan-vien/{$id}", ['name' => 'UPDATED NV', 'email' => 'testnv_'.time().'@jpm.com', 'role' => 'admin', 'status' => 0, 'phone' => '090' . rand(1000000, 9999999)]);
            $res3 = $this->delete("/api/admin/nhan-vien/{$id}");
            $results['NhanVien'] = [
                'CREATE' => 'PASS',
                'UPDATE' => $res2->getStatusCode() === 200 ? 'PASS' : 'FAIL',
                'DELETE' => $res3->getStatusCode() === 200 ? 'PASS' : 'FAIL',
            ];
        }
        
        echo json_encode($results, JSON_PRETTY_PRINT);
    }
}

$tester = new AdminRuntimeTester($app);
$tester->run();

<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=db_jpm', 'root', '');
// Get an order that is cancelled (status = 3)
$orderId = 3; 

// Let's reset order 3 to status 0
$pdo->exec("UPDATE don_hangs SET trang_thai = 0 WHERE id = $orderId");
$pdo->exec("DELETE FROM the_khos WHERE loai_giao_dich = 'HOAN_NHAP' AND id_tham_chieu = $orderId");

$initialStock = $pdo->query("SELECT so_luong_ton FROM ton_khos WHERE id_san_pham = 3001")->fetchColumn();

// Cancel once (need to simulate DonHangController@update)
// Since it requires Auth, I'll just write a quick script to bootstrap Laravel and call the service
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$order = \App\Domain\Sales\Models\DonHang::find($orderId);
$service = new \App\Domain\Sales\Services\OrderService();

// Cancel 1
$service->cancelOrderInventory($order, 1);
$stockAfterCancel1 = $pdo->query("SELECT so_luong_ton FROM ton_khos WHERE id_san_pham = 3001")->fetchColumn();

// Cancel 2
$service->cancelOrderInventory($order, 1);
$stockAfterCancel2 = $pdo->query("SELECT so_luong_ton FROM ton_khos WHERE id_san_pham = 3001")->fetchColumn();

echo "Initial: $initialStock, After Cancel 1: $stockAfterCancel1, After Cancel 2: $stockAfterCancel2\n";

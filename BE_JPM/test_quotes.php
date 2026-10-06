<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$user = App\Domain\IAM\Models\User::first();
$token = $user->createToken('test')->plainTextToken;
$ch = curl_init("http://127.0.0.1:8000/api/client/history/quotes");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
$response = curl_exec($ch);
curl_close($ch);
echo $response;

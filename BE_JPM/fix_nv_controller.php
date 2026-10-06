<?php
$file = 'C:/xampp/htdocs/JPM/BE_JPM/app/Domain/IAM/Controllers/NhanVienController.php';
$c = file_get_contents($file);

$c = preg_replace('/\'ho_ten\'\s*=>\s*\$request->name,\s*\/\/\s*Do NOT update phone and email to protect identity/', "'ho_ten' => \$request->name,\n                'so_dien_thoai' => \$request->phone,", $c);

$c = preg_replace('/\'name\'\s*=>\s*\$request->name,\s*\'status\'\s*=>\s*\$request->status,/', "'name' => \$request->name,\n                    'email' => \$request->email,\n                    'status' => \$request->status,", $c);

file_put_contents($file, $c);
echo "Done";

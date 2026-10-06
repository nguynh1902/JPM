<?php
$file = 'C:/xampp/htdocs/JPM/BE_JPM/app/Domain/IAM/Requests/NhanVienRequest.php';
$c = file_get_contents($file);

$c = preg_replace('/return \$rules;\s*\}\s*/', '', $c);

file_put_contents($file, $c);
echo "Fixed Request syntax";

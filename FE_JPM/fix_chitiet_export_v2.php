<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/ChiTietTinTuc.vue';
$c = file_get_contents($file);

$c = preg_replace(
    '/return\s*\{\s*article,/',
    "return {\n      getImageUrl,\n      article,",
    $c
);

file_put_contents($file, $c);
echo "Fixed getImageUrl export via Regex\n";

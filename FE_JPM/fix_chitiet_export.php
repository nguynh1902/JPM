<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/ChiTietTinTuc.vue';
$c = file_get_contents($file);

$c = str_replace(
    "return {\n      article,",
    "return {\n      getImageUrl,\n      article,",
    $c
);

file_put_contents($file, $c);
echo "Fixed getImageUrl export\n";

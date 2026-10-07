<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/TinTuc.vue';
$c = file_get_contents($file);

$c = preg_replace("/`http:\/\/localhost:8000\/storage\/\$\{art.thumbnail\}`/", "`http://localhost:8000/\${art.thumbnail.startsWith('/') ? art.thumbnail.substring(1) : art.thumbnail}`", $c);

file_put_contents($file, $c);
echo "Patched Client TinTuc mapping\n";

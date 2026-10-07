<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/ChiTietTinTuc.vue';
$c = file_get_contents($file);

$c = str_replace(
    'return match ? https://www.youtube.com/embed/ + match[1] : null;',
    "return match ? 'https://www.youtube.com/embed/' + match[1] : null;",
    $c
);

file_put_contents($file, $c);
echo "Fixed missing quotes in youtube URL\n";

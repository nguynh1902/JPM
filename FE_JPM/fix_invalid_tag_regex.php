<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/TinTuc.vue';
$c = file_get_contents($file);

$c = preg_replace(
    '/(<img :src="art.img" :alt="art.title" class="w-100 h-100 object-fit-cover transition-transform">\s*<\/div>\s*)<\/a>/',
    '$1</router-link>',
    $c
);

file_put_contents($file, $c);
echo "Fixed regex end tag\n";

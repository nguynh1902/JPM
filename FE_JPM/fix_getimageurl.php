<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/SoDoPhuTung.vue';
$c = file_get_contents($file);

$c = preg_replace(
    '/(const getImageUrl = \(path\) => \{.+?\}\s*;)/s',
    "const getImageUrl = (path) => {\n  if (!path) return '';\n  if (path.startsWith('blob:') || path.startsWith('http')) return path;\n  let cleanPath = path.replace(/^\/?storage\//, '');\n  return 'http://localhost:8000/storage/' + cleanPath;\n};",
    $c
);

file_put_contents($file, $c);
echo "Fixed getImageUrl";

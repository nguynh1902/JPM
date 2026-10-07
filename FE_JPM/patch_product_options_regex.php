<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/Kho/TonKho.vue';
$c = file_get_contents($file);

$c = preg_replace(
    '/computed:\s*\{\s*warehouseOptions\(\)\s*\{/',
    "computed: {\n    productOptions() {\n      return [...(this.sanPhams || []), ...(this.phuTungs || [])];\n    },\n    warehouseOptions() {",
    $c
);

file_put_contents($file, $c);
echo "Added productOptions via regex\n";

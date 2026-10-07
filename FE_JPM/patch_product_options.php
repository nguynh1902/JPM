<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/Kho/TonKho.vue';
$c = file_get_contents($file);

$search = "computed: {
    warehouseOptions() {";

$replace = "computed: {
    productOptions() {
      return [...(this.sanPhams || []), ...(this.phuTungs || [])];
    },
    warehouseOptions() {";

$c = str_replace($search, $replace, $c);

file_put_contents($file, $c);
echo "Added productOptions\n";

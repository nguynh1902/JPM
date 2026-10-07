<?php
$files = [
    'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/SanPham/SanPham.vue',
    'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/PhuTung/PhuTung.vue',
    'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/NoiDung/TinTuc.vue',
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $c = file_get_contents($file);
    
    // Pattern to match the custom file upload UI
    $pattern = '/<div class="d-flex justify-content-between align-items-center mb-2">\s*<label class="form-label fw-medium text-dark mb-0">Thư Viện Ảnh Phụ<\/label>\s*<label for="[^"]+" class="btn btn-sm btn-outline-primary py-0 px-2 mb-0" style="cursor: pointer;?">\s*<i class="bx bx-upload"><\/i> Chọn nhiều ảnh\s*<\/label>\s*<\/div>\s*<input type="file" id="([^"]+)" class="d-none"/u';
    
    $c = preg_replace($pattern, '<label class="form-label fw-medium text-dark">Thư Viện Ảnh Phụ</label>' . "\n" . '                      <input type="file" id="$1" class="form-control mb-2"', $c);
    
    file_put_contents($file, $c);
    echo "Patched $file\n";
}

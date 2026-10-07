<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/SanPham/SanPham.vue';
$c = file_get_contents($file);

$search = '<label class="form-label fw-medium text-dark">Tồn Kho</label>
                    <input type="number" class="form-control fw-bold text-primary mt-1" v-model="formData.so_luong_ton" min="0" placeholder="0">';

$replace = '<label class="form-label fw-medium text-dark">Tồn Kho</label>
                    <input type="number" class="form-control fw-bold text-primary mt-1" v-model="formData.so_luong_ton" readonly placeholder="0" title="Số lượng được tự động tính tổng từ các kho.">
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;"><i class="bx bx-lock-alt"></i> Quản lý tại phân hệ Kho</small>';

// Since encoding issues can cause str_replace to fail on "Tồn Kho", I will use regex
$c = preg_replace(
    '/<label[^>]*>T?n Kho<\/label>\s*<input[^>]*v-model="formData\.so_luong_ton"[^>]*>/',
    '<label class="form-label fw-medium text-dark">Tồn Kho</label>
                    <input type="number" class="form-control fw-bold text-primary mt-1" v-model="formData.so_luong_ton" readonly placeholder="0" title="Số lượng được tự động tính tổng từ các kho.">
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;"><i class="bx bx-lock-alt"></i> Quản lý tại phân hệ Kho</small>',
    $c
);

file_put_contents($file, $c);
echo "Patched SanPham.vue\n";

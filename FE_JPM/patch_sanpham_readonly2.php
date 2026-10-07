<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/SanPham/SanPham.vue';
$c = file_get_contents($file);

$c = preg_replace(
    '/<label class="form-label fw-medium text-dark">T.n Kho<\/label>\s*<input type="number" class="form-control fw-bold text-primary mt-1" v-model="formData.so_luong_ton" min="0" placeholder="0">/u',
    '<label class="form-label fw-medium text-dark">Tồn Kho</label>
                    <input type="number" class="form-control fw-bold text-secondary mt-1 bg-light" v-model="formData.so_luong_ton" readonly placeholder="0" title="Số lượng được tự động tính tổng từ các kho." style="cursor: not-allowed;">
                    <small class="text-danger d-block mt-1 fw-bold" style="font-size: 0.75rem;"><i class="bx bx-lock-alt"></i> Quản lý số lượng tại trang Kho</small>',
    $c
);

file_put_contents($file, $c);
echo "Patched SanPham.vue successfully\n";

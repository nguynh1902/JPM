<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/NhanSu/NhanVien.vue';
$c = file_get_contents($file);

// 1. Fix read-only in email and phone
$c = str_replace('<span v-if="!isEditMode" class="text-danger">*</span></label>', '<span class="text-danger">*</span></label>', $c);
$c = str_replace(":class=\"isEditMode ? 'bg-light text-muted' : ''\"", '', $c);
$c = str_replace(':readonly="isEditMode"', '', $c);
$c = str_replace(':required="!isEditMode"', 'required', $c);

// 2. Fix the grid layout for Password and Role (chia hàng)
// Currently it is:
// <div class="col-md-6 mb-3">
//   <label class="form-label fw-medium text-dark">Mật Khẩu...
// </div>
// <div class="col-md-6 mb-3">
//   <label class="form-label fw-medium text-dark">Vai Trò...
// </div>

// We will change them to <div class="col-md-12 mb-3">
// so they span full width in their row and naturally wrap.

$c = preg_replace('/<div class="col-md-6 mb-3">(\s*<label class="form-label fw-medium text-dark">Mật Khẩu)/', '<div class="col-md-12 mb-3">$1', $c);

$c = preg_replace('/<div class="col-md-6 mb-3">(\s*<label class="form-label fw-medium text-dark">Vai Trò)/', '<div class="col-md-12 mb-3">$1', $c);

file_put_contents($file, $c);
echo "Done Vue patch";

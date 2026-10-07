<?php
$files = [
    'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/SanPham/SanPham.vue' => 'thu_vien_anh',
    'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/PhuTung/PhuTung.vue' => 'thu_vien_anh',
    'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/NoiDung/TinTuc.vue' => 'gallery_upload' // TinTuc uses gallery_upload
];

foreach ($files as $file => $id) {
    if (!file_exists($file)) continue;
    $c = file_get_contents($file);
    
    // The current state is:
    // <label class="form-label fw-medium text-dark">Thư Viện Ảnh Phụ</label>
    // <input type="file" id="ID" class="form-control mb-2" accept="image/*" multiple @change="handleGalleryUpload" />
    
    // We want to replace it with:
    /*
    <div class="d-flex justify-content-between align-items-center mb-2">
      <label class="form-label fw-medium text-dark mb-0">Thư Viện Ảnh Phụ</label>
      <label for="ID" class="btn btn-sm btn-primary py-0 px-2 mb-0" style="cursor: pointer;">
        <i class="bx bx-upload"></i> Chọn nhiều ảnh
      </label>
    </div>
    <input type="file" id="ID" class="d-none" accept="image/*" multiple @change="handleGalleryUpload" />
    */
    
    // Note: The user mentioned "màu button lạ" (weird button color) earlier. 
    // btn-outline-primary turns solid blue on hover/active. If I use btn-primary, it's consistent Admin Teal/Primary. 
    // Let's stick to btn-outline-primary because that was the EXACT previous interface they want back.

    // Regex to find the current label and input
    // We'll use a very flexible regex to match both tags regardless of whitespace.
    
    // In TinTuc, the label might be "Thu Vi?n ?nh Ph?"
    $pattern = '/<label class="form-label fw-medium text-dark">Thu Vi[^\<]+<\/label>\s*<input type="file" id="'.$id.'" class="form-control mb-2" accept="image\/\*" multiple @change="handleGalleryUpload" \/>/s';
    
    // Also try matching standard "Thư Viện Ảnh Phụ" just in case
    $pattern2 = '/<label class="form-label fw-medium text-dark">Thư Viện Ảnh Phụ<\/label>\s*<input type="file" id="'.$id.'" class="form-control mb-2" accept="image\/\*" multiple @change="handleGalleryUpload" \/>/s';
    
    // Also try without self-closing slash for input
    $pattern3 = '/<label class="form-label fw-medium text-dark">Thư Viện Ảnh Phụ<\/label>\s*<input type="file" id="'.$id.'" class="form-control mb-2" accept="image\/\*" multiple @change="handleGalleryUpload">/s';
    
    $replacement = '<div class="d-flex justify-content-between align-items-center mb-2">
                          <label class="form-label fw-medium text-dark mb-0">Thư Viện Ảnh Phụ</label>
                          <label for="'.$id.'" class="btn btn-sm btn-outline-primary py-0 px-2 mb-0" style="cursor: pointer; box-shadow: none;">
                            <i class="bx bx-upload"></i> Chọn nhiều ảnh
                          </label>
                        </div>
                        <input type="file" id="'.$id.'" class="d-none" accept="image/*" multiple @change="handleGalleryUpload" />';

    $matched = false;
    if (preg_match($pattern, $c)) {
        $c = preg_replace($pattern, $replacement, $c);
        $matched = true;
    } elseif (preg_match($pattern2, $c)) {
        $c = preg_replace($pattern2, $replacement, $c);
        $matched = true;
    } elseif (preg_match($pattern3, $c)) {
        $c = preg_replace($pattern3, $replacement, $c);
        $matched = true;
    }
    
    // Fallback for TinTuc where it might be slightly different
    if (!$matched && $id === 'gallery_upload') {
        $p4 = '/<label class="form-label fw-medium text-dark">Thu Vi\?n \?nh Ph\?<\/label>\s*<input type="file" id="gallery_upload" class="form-control mb-2" accept="image\/\*" multiple @change="handleGalleryUpload" \/>/s';
        if (preg_match($p4, $c)) {
            $c = preg_replace($p4, $replacement, $c);
            $matched = true;
        }
    }
    
    if ($matched) {
        file_put_contents($file, $c);
        echo "Restored button in $file\n";
    } else {
        echo "Failed to match in $file\n";
    }
}

<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/SanPham/SanPham.vue';
$c = file_get_contents($file);

$c = preg_replace(
    '/<div class="mt-2">\s*<label class="form-label fw-medium text-dark">Thu Vi[^\<]+<\/label>.*?<\/div>\s*<\/div>\s*<\/div>\s*<div class="row mt-3">/s',
    '<div class="mt-2">
                        <label class="form-label fw-medium text-dark">Thư Viện Ảnh Phụ</label>
                        <input type="file" id="thu_vien_anh" class="form-control mb-2" accept="image/*" multiple @change="handleGalleryUpload" />
                        
                        <!-- Existing Gallery -->
                        <div v-if="formData.thu_vien_anh && formData.thu_vien_anh.length > 0">
                          <div v-for="(img, idx) in formData.thu_vien_anh" :key="\'old_\'+idx" class="d-flex gap-2 mb-2 align-items-center bg-light p-1 rounded border">
                            <img :src="getImageUrl(img)" style="height: 30px; width: 40px; object-fit: contain" />
                            <span class="text-truncate text-secondary" style="font-size: 0.8rem; max-width: 150px;">{{ img }}</span>
                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger ms-auto" @click="formData.thu_vien_anh.splice(idx, 1)" title="Xóa"><i class="bx bx-trash"></i></button>
                          </div>
                        </div>

                        <!-- New Uploads -->
                        <div v-if="galleryFiles && galleryFiles.length > 0">
                          <div v-for="(file, idx) in galleryFiles" :key="\'new_\'+idx" class="d-flex gap-2 mb-2 align-items-center bg-light p-1 rounded border">
                            <img :src="getBlobUrl(file)" style="height: 30px; width: 40px; object-fit: contain" />
                            <span class="text-truncate text-secondary" style="font-size: 0.8rem; max-width: 150px;">{{ file.name }}</span>
                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger ms-auto" style="flex-shrink: 0" @click="galleryFiles.splice(idx, 1)" title="Xóa"><i class="bx bx-trash"></i></button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-3">',
    $c
);

// Inject getBlobUrl
if (strpos($c, 'getBlobUrl(file)') === false) {
    $c = preg_replace('/(getImageUrl\(path\) \{.*?\},)/s', "$1\n    getBlobUrl(file) {\n      if (!file) return '';\n      return URL.createObjectURL(file);\n    },", $c);
}

file_put_contents($file, $c);
echo "Patched SanPham";

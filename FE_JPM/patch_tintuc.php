<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/NoiDung/TinTuc.vue';
$c = file_get_contents($file);

$p1 = strpos($c, '<div class="mb-3">'); // For TinTuc, it's <div class="mb-3">\n <label ...>Thu Viện...
// find index of "Thu Vi" inside that div to be sure
$p1 = strrpos(substr($c, 0, strpos($c, 'Thu Vi?n ?nh Ph?')), '<div class="mb-3">');

$p2 = strpos($c, '<div class="modal-footer border-top pt-3">', $p1);

if ($p1 !== false && $p2 !== false) {
    // we need to step back before </div> </div> </div> which closes modal-body
    // let's just do a preg_replace for the exact HTML fragment
    
    // Actually, I'll just replace the New Uploads block
    $newUploadsPattern = '/<!-- New Uploads -->.*?<\/div>\s*<\/div>\s*<\/div>/s';
    
    $replacement = '<!-- New Uploads -->
                    <div v-if="galleryFiles && galleryFiles.length > 0">
                      <div v-for="(file, idx) in galleryFiles" :key="\'new_\'+idx" class="d-flex gap-2 mb-2 align-items-center bg-light p-1 rounded border">
                        <img :src="getBlobUrl(file)" style="height: 30px; width: 40px; object-fit: contain" />
                        <span class="text-truncate text-secondary" style="font-size: 0.8rem; max-width: 150px;">{{ file.name }}</span>
                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger ms-auto" style="flex-shrink: 0" @click="galleryFiles.splice(idx, 1)" title="Xóa"><i class="bx bx-trash"></i></button>
                      </div>
                    </div>
                  </div>';
    $c = preg_replace($newUploadsPattern, $replacement, $c);
    
    if (strpos($c, 'getBlobUrl(file)') === false) {
        $c = preg_replace('/(getImageUrl\(path\) \{.*?\},)/s', "$1\n    getBlobUrl(file) {\n      if (!file) return '';\n      return URL.createObjectURL(file);\n    },", $c);
    }
    
    file_put_contents($file, $c);
    echo "Patched TinTuc\n";
} else {
    echo "Failed";
}

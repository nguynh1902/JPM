const fs = require('fs');

function applyRemoveButton(file, id, dataField, isUrlMethod, methodCode) {
    if (!fs.existsSync(file)) return;
    let content = fs.readFileSync(file, 'utf8');

    // 1. Add position-relative and button to the preview container
    // Look for <div class="... bg-light ..."> and <img ...>
    
    // Replace class to ensure position-relative is there
    content = content.replace(/(class="[^"]*bg-light[^"]*)(".*?style="height: 180px")/s, (match, p1, p2) => {
        if (!p1.includes('position-relative')) {
            return p1 + ' position-relative' + p2;
        }
        return match;
    });

    const btnHtml = `\n                      <button v-if="${dataField}" type="button" class="btn btn-icon btn-sm bg-white text-danger position-absolute top-0 end-0 m-2 rounded-circle shadow-sm border" @click="removeImage" style="width: 28px; height: 28px; padding: 0;" title="Xóa ảnh">
                        <i class="bx bx-trash"></i>
                      </button>`;
                      
    // Insert button after <img ... >
    content = content.replace(/(<img[^>]*>)/, (match) => {
        if (!content.includes('removeImage"')) {
            return match + btnHtml;
        }
        return match;
    });

    // 2. Add removeImage method
    if (!content.includes('removeImage() {') && !content.includes('removeImage(')) {
        const methodReplacement = `${methodCode}\n      handle`;
        content = content.replace(/\bhandle[A-Za-z0-9_]*Upload\s*\([A-Za-z0-9_]*\)\s*{/, (match) => {
            return methodCode + "\n    " + match;
        });
    }

    fs.writeFileSync(file, content, 'utf8');
    console.log(`Patched ${file}`);
}

applyRemoveButton(
    "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/Marketing/Banner.vue",
    "image",
    "formData.image_url",
    true,
    `removeImage() {
      this.imageFile = null;
      this.formData.image_url = null;
      const fileInput = document.getElementById('image');
      if (fileInput) fileInput.value = '';
    },`
);

// We have 6 files: CauHinhHeThong.vue, KhachHang.vue, CapNhatLinhKien.vue, Banner.vue, PhuTung.vue, HoSo.vue

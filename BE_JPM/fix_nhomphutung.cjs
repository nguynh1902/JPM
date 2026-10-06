const fs = require('fs');
const path = require('path');

const file = path.join(__dirname, '../FE_JPM/src/views/admin/PhuTung/NhomPhuTung.vue');
let content = fs.readFileSync(file, 'utf8');

// Replace the HTML block
const htmlRegex = /<div class="mb-4">\s*<label for="ma_nhom"[\s\S]*?<\/div>/;
content = content.replace(htmlRegex, `<div class="mb-4" v-if="isEditMode">
                <label for="ma_nhom" class="form-label fw-medium text-dark">MÃ NHÓM</label>
                <input type="text" id="ma_nhom" class="form-control bg-light" v-model="formData.ma_nhom" readonly />
              </div>`);

// Replace the validation block
const jsRegex = /if \(\!this\.formData\.ma_nhom \|\| \!this\.formData\.ten_nhom\)/;
content = content.replace(jsRegex, `if (!this.formData.ten_nhom)`);

fs.writeFileSync(file, content, 'utf8');
console.log('Fixed NhomPhuTung.vue');

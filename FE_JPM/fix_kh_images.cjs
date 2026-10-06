const fs = require('fs');

function patchKhachHang() {
    const file = "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/KhachHang/KhachHang.vue";
    let content = fs.readFileSync(file, 'utf8');
    
    if (content.includes('removeAvatar')) return;

    // 1. Add position-relative to the 180px div
    content = content.replace(/class="d-flex justify-content-center align-items-center border rounded bg-light mb-3"(\s*style="height: 180px")/s, 
        'class="d-flex justify-content-center align-items-center border rounded bg-light mb-3 position-relative"$1');

    // 2. Add button after </img>
    const btnHtml = `\n                          <button v-if="avatarPreview || formData.avatar" type="button" class="btn btn-icon btn-sm bg-white text-danger position-absolute top-0 end-0 m-2 rounded-circle shadow-sm border" @click="removeAvatar" style="width: 28px; height: 28px; padding: 0;" title="Xóa ảnh"><i class="bx bx-trash"></i></button>`;
    
    content = content.replace(/(object-fit: contain;\s*"\s*\/>)/s, `$1${btnHtml}`);

    // 3. Add removeAvatar method
    const methodStr = `removeAvatar() {
      this.avatarPreview = null;
      this.formData.avatar = null;
      this.avatarFile = null;
      const fileInput = document.getElementById('avatar');
      if (fileInput) fileInput.value = "";
    },\n    handleAvatarUpload`;
    
    content = content.replace(/handleAvatarUpload/, methodStr);

    fs.writeFileSync(file, content, 'utf8');
    console.log("Patched KhachHang.vue");
}

function patchPhuTung() {
    const file = "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/PhuTung/PhuTung.vue";
    let content = fs.readFileSync(file, 'utf8');

    if (!content.includes('@click="clearMainImage"')) {
        // Main image already has a button? 
        // Wait, earlier I saw PhuTung.vue has clearMainImage, let's see if the HTML has it
        if (!content.includes('bx-trash')) {
           // wait, my grep didn't see it because it was outside context?
           // Actually PhuTung has `clearMainImage` method, but let's check if it's bound in HTML.
        }
    }
    console.log("PhuTung not patched yet (need to check HTML).");
}

patchKhachHang();
patchPhuTung();

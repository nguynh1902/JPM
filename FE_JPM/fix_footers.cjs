const fs = require('fs');

const files = [
    "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/HeThong/CauHinhHeThong.vue",
    "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/HeThong/DanhSachThongBao.vue",
    "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/HeThong/LichSuHoatDong.vue",
    "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/KhachHang/KhachHang.vue",
    "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/Kho/CrossDocking.vue",
    "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/Kho/Kho.vue",
    "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/Kho/TonKho.vue",
    "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/LinhKien/CapNhatLinhKien.vue",
    "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/LinhKien/MaThayThe.vue",
    "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/Marketing/Banner.vue",
    "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/Marketing/Voucher.vue",
    "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/MayThuy/LoaiDongCo.vue",
    "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/NhanSu/NhanVien.vue",
    "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/NhanSu/PhanQuyen.vue",
    "C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/NoiDung/TinTuc.vue"
];

files.forEach(file => {
    if (!fs.existsSync(file)) return;
    let content = fs.readFileSync(file, 'utf8');

    if (content.includes('Tổng cộng:')) {
        console.log(`Skipping ${file}`);
        return;
    }

    // Match v-for="... in arrayName"
    const regex = /v-for=".*?in\s+\(?([a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)?)(?:\s*\|\|.*?)?\)?\"/;
    const match = content.match(regex);

    if (match) {
        const arrayName = match[1];
        
        const footerCode = `      </div>
      <!-- Tổng số mục -->
      <div class="card-footer py-3 border-top">
        <span class="text-muted fw-medium">Tổng cộng: {{ (${arrayName} && ${arrayName}.length) ? ${arrayName}.length : 0 }} mục</span>
      </div>`;

        // We want to insert the footer after the closing </div> of <div class="table-responsive...">
        // Usually it's:
        // </table>
        // </div>
        // (insert here)
        
        content = content.replace(/<\/table>\s*<\/div>/g, (matched, index, original) => {
            // we only want to replace the FIRST occurrence in the template
            return matched + "\n" + footerCode;
        });

        // But we have to make sure we only replace the FIRST one (in case there are multiple tables)
        let replaced = false;
        content = fs.readFileSync(file, 'utf8');
        content = content.replace(/<\/table>\s*<\/div>/, (matched) => {
            replaced = true;
            return matched + "\n" + footerCode;
        });

        if (replaced) {
            fs.writeFileSync(file, content, 'utf8');
            console.log(`Fixed ${file} with array: ${arrayName}`);
        } else {
            console.log(`Failed to find table end in ${file}`);
        }
    } else {
        console.log(`Could not find array in ${file}`);
    }
});

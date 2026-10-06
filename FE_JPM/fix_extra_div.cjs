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

    // Replace the extra </div> that was accidentally added before the footer comment
    const newContent = content.replace(/<\/div>\r?\n(\s*<!-- Tổng số mục -->)/, '$1');

    if (newContent !== content) {
        fs.writeFileSync(file, newContent, 'utf8');
        console.log(`Fixed extra </div> in ${file}`);
    } else {
        console.log(`No extra </div> found in ${file}`);
    }
});

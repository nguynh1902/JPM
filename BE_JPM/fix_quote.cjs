const fs = require('fs');
const path = require('path');

const file = path.join(__dirname, 'app/Domain/Sales/Controllers/YeuCauBaoGiaController.php');
let content = fs.readFileSync(file, 'utf8');

content = content.replace(
    /'ten_phu_tung'       => \$item\['ten_phu_tung'\] \?\? null,/g,
    `'ten_phu_tung'       => $item['ten_phu_tung'] ?? 'Không rõ',`
);

content = content.replace(
    /'ma_phu_tung'        => \$item\['ma_phu_tung'\] \?\? null,/g,
    `'ma_phu_tung'        => $item['ma_phu_tung'] ?? 'N/A',`
);

fs.writeFileSync(file, content, 'utf8');
console.log('Fixed YeuCauBaoGiaController');

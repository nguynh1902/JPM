<?php
$files = [
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

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Check if it already has card-footer
    if (strpos($content, 'Tổng cộng:') !== false) continue;
    
    // Find the array name
    if (preg_match('/v-for=".*?in\s+([a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)?)"/', $content, $matches)) {
        $arrayName = $matches[1];
        
        $footerCode = "
      </div>
      <!-- Tổng số mục -->
      <div class=\"card-footer py-3 border-top\">
        <span class=\"text-muted fw-medium\">Tổng cộng: {{ ($arrayName && $arrayName.length) ? $arrayName.length : 0 }} mục</span>
      </div>";

        // Replace the </div> immediately after </table>
        $content = preg_replace('/<\/table>\s*<\/div>\s*<\/div>/', "</table>\n        </div>$footerCode\n    </div>", $content, 1);
        
        // Let's be safer. Just find </table>, then the next </div>, and replace that </div> with </div> + footer
        $tablePos = strpos($content, '</table>');
        if ($tablePos !== false) {
            $divPos = strpos($content, '</div>', $tablePos);
            if ($divPos !== false) {
                // Check if there is another div immediately after
                $nextDivPos = strpos($content, '</div>', $divPos + 6);
                
                // Let's just use string replacement on a larger block
                $content = preg_replace_callback('/(<\/table>.*?<\/div>)/s', function($m) use ($footerCode) {
                    return $m[1] . $footerCode;
                }, $content, 1);
                
                // Actually wait, some have `<div class="table-responsive">` inside a `<div class="card">`. 
                // Replacing after `</div>` of table-responsive is correct because `card-footer` is a sibling of `table-responsive`.
                
                file_put_contents($file, $content);
                echo "Added footer to $file (Array: $arrayName)\n";
            }
        }
    } else {
        echo "Could not find array in $file\n";
    }
}

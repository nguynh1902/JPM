<?php
$files = [
    'SanPham' => [
        'path' => 'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/SanPham/SanPham.vue',
        'var' => 'thu_vien_anh'
    ],
    'PhuTung' => [
        'path' => 'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/PhuTung/PhuTung.vue',
        'var' => 'thu_vien_anh'
    ],
    'TinTuc' => [
        'path' => 'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/NoiDung/TinTuc.vue',
        'var' => 'gallery'
    ]
];

foreach ($files as $name => $info) {
    $file = $info['path'];
    $var = $info['var'];
    
    if (!file_exists($file)) continue;
    $c = file_get_contents($file);
    
    // Regex to match the entire gallery block from label to the end of New Uploads
    // Since formatting can be wild, let's find the boundaries.
    
    $startToken = "Thu Vi?n ?nh Ph?</label"; // or "Thư Viện Ảnh Phụ"
    // Find the start line index
    $lines = explode("\n", $c);
    $startIndex = -1;
    $endIndex = -1;
    for ($i = 0; $i < count($lines); $i++) {
        if (strpos($lines[$i], 'Thư Viện Ảnh Phụ') !== false || strpos($lines[$i], 'Thu Vi?n ?nh Ph?') !== false) {
            // Found start
            $startIndex = $i;
            break;
        }
    }
    
    if ($startIndex !== -1) {
        // Find end index - usually the end of the col/mb-3 containing it.
        // It's followed by "GIÁ BÁN" in SanPham, "GIÁ BÁN" in PhuTung, and "</div>" for modal-body in TinTuc.
        for ($i = $startIndex; $i < count($lines); $i++) {
            if (strpos($lines[$i], 'Giá Bán') !== false || strpos($lines[$i], 'Gi Bn') !== false || strpos($lines[$i], 'modal-footer') !== false || strpos($lines[$i], 'Trạng thái') !== false || strpos($lines[$i], 'Tr?ng thi') !== false) {
                // Backtrack a few lines to find the closing div of the gallery block
                // Actually, let's just look for the first line that defines the NEXT field.
                $endIndex = $i - 1;
                while(trim($lines[$endIndex]) == '' || trim($lines[$endIndex]) == '</div>' || trim($lines[$endIndex]) == '</div></div>') {
                    $endIndex--; // Step back over closing tags if needed, but wait!
                }
                
                // Let's just find the exact next block's start.
                $endIndex = $i;
                break;
            }
        }
    }
    
    if ($startIndex !== -1 && $endIndex !== -1) {
        // We will replace lines from $startIndex-1 (where the div/label starts) to $endIndex-1
        // Let's just do a simpler preg_replace based on the HTML structure.
    }
}

<?php
$f1 = __DIR__.'/app/Domain/Core/Resources/DonHangResource.php';
$f2 = __DIR__.'/app/Domain/Core/Resources/ThanhToanResource.php';

function replaceMissing($file) {
    if (file_exists($file)) {
        $c = file_get_contents($file);
        $c = str_replace("new PhuongThucThanhToanResource(\$this->whenLoaded('phuongThucThanhToan'))", "\$this->whenLoaded('phuongThucThanhToan')", $c);
        $c = str_replace("new DonViVanChuyenResource(\$this->whenLoaded('donViVanChuyen'))", "\$this->whenLoaded('donViVanChuyen')", $c);
        file_put_contents($file, $c);
        echo "Fixed $file\n";
    }
}
replaceMissing($f1);
replaceMissing($f2);

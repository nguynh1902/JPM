<?php
$models = [
    'C:/xampp/htdocs/JPM/BE_JPM/app/Domain/IAM/Models/KhachHang.php',
    'C:/xampp/htdocs/JPM/BE_JPM/app/Domain/IAM/Models/NhanVien.php',
    'C:/xampp/htdocs/JPM/BE_JPM/app/Domain/PIM/Models/PhuTung.php',
    'C:/xampp/htdocs/JPM/BE_JPM/app/Domain/PIM/Models/SanPham.php',
    'C:/xampp/htdocs/JPM/BE_JPM/app/Domain/PIM/Models/ThuongHieu.php',
    'C:/xampp/htdocs/JPM/BE_JPM/app/Domain/PIM/Models/DongCoModel.php',
    'C:/xampp/htdocs/JPM/BE_JPM/app/Domain/Procurement/Models/Kho.php',
    'C:/xampp/htdocs/JPM/BE_JPM/app/Domain/Procurement/Models/TonKho.php',
    'C:/xampp/htdocs/JPM/BE_JPM/app/Domain/Core/Models/DonHang.php'
];

foreach ($models as $file) {
    if (file_exists($file)) {
        $c = file_get_contents($file);
        if (strpos($c, 'use \App\Domain\Core\Traits\LogsActivity;') === false) {
            $c = preg_replace('/(class\s+[A-Za-z0-9_]+\s+extends\s+Model\r?\n\{)/', "$1\n    use \App\Domain\Core\Traits\LogsActivity;\n", $c);
            file_put_contents($file, $c);
            echo "Patched $file\n";
        }
    }
}

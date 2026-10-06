<?php
$file = 'C:/xampp/htdocs/JPM/BE_JPM/app/Domain/PIM/Models/PhuTung.php';
$content = file_get_contents($file);

// Find the last closing brace
$pos = strrpos($content, '}');
if ($pos !== false) {
    $relation = "
    public function maThayThes()
    {
        return \$this->hasMany(MaThayThe::class, 'phu_tung_cu_id');
    }
";
    $content = substr_replace($content, $relation . "\n}", $pos, 1);
    file_put_contents($file, $content);
    echo "Appended.\n";
}

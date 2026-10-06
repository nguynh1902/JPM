<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Domain\PIM\Models\NhomPhuTung;
use App\Domain\PIM\Models\PhuTung;
use Illuminate\Support\Facades\DB;

DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// 1. Delete unused NhomPhuTung
$usedGroupIds = PhuTung::whereNotNull('nhom_phu_tung_id')->pluck('nhom_phu_tung_id')->unique()->toArray();

$deletedCount = NhomPhuTung::whereNotIn('id', $usedGroupIds)->delete();
echo "Deleted {$deletedCount} unused Nhom Phu Tung.\n";

// 2. Rename remaining to NPTxx
$remainingGroups = NhomPhuTung::orderBy('id', 'asc')->get();
$counter = 1;
foreach ($remainingGroups as $group) {
    $newCode = 'NPT' . str_pad($counter, 2, '0', STR_PAD_LEFT);
    $group->ma_nhom = $newCode;
    $group->save();
    $counter++;
}
echo "Renamed " . count($remainingGroups) . " Nhom Phu Tung to NPTxx format.\n";

DB::statement('SET FOREIGN_KEY_CHECKS=1;');

// Generate the Seeder Array
$seederArray = [];
foreach ($remainingGroups as $group) {
    $seederArray[] = [
        'ma_nhom' => $group->ma_nhom,
        'ten_nhom' => $group->ten_nhom,
        'trang_thai' => $group->trang_thai,
        'created_at' => $group->created_at ? $group->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
        'updated_at' => $group->updated_at ? $group->updated_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s')
    ];
}

$seederContent = "<?php\n\nnamespace Database\Seeders;\n\nuse Illuminate\Database\Seeder;\nuse Illuminate\Support\Facades\DB;\n\nclass NhomPhuTungSeeder extends Seeder\n{\n    public function run()\n    {\n        DB::statement('SET FOREIGN_KEY_CHECKS=0;');\n        DB::table('nhom_phu_tungs')->truncate();\n\n        \$data = " . var_export($seederArray, true) . ";\n\n        DB::table('nhom_phu_tungs')->insert(\$data);\n        DB::statement('SET FOREIGN_KEY_CHECKS=1;');\n    }\n}\n";

file_put_contents(__DIR__ . '/database/seeders/NhomPhuTungSeeder.php', $seederContent);
echo "Updated database/seeders/NhomPhuTungSeeder.php with cleaned data.\n";

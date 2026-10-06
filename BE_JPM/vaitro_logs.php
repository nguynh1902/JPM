<?php
$file = 'C:/xampp/htdocs/JPM/BE_JPM/app/Domain/IAM/Controllers/VaiTroController.php';
$c = file_get_contents($file);

if (strpos($c, 'use App\Domain\IAM\Models\SystemLog;') === false) {
    $c = str_replace('use Illuminate\Support\Facades\DB;', "use Illuminate\Support\Facades\DB;\nuse App\Domain\IAM\Models\SystemLog;", $c);
}

$c = str_replace(
    "\$role->syncPermissions(\$permissions);\n            }\n            \n            DB::commit();",
    "\$role->syncPermissions(\$permissions);\n            }\n            SystemLog::create(['user_id' => auth('sanctum')->id(), 'action' => 'CẬP NHẬT', 'description' => 'Cập nhật Vai Trò (' . \$role->name . ')', 'ip_address' => request()->ip()]);\n            DB::commit();",
    $c
);

$c = str_replace(
    "\$role->syncPermissions(\$permissions);\n            }\n            \n            DB::commit();", // For Create (wait, the create one might be different)
    $c // Placeholder, let's use preg_replace for both
);

file_put_contents($file, $c);
echo "Added logs to VaiTroController";

<?php
$file = 'C:/xampp/htdocs/JPM/BE_JPM/app/Domain/IAM/Controllers/VaiTroController.php';
$c = file_get_contents($file);

// Replace create
$c = str_replace(
    "\$role = Role::create(['name' => \$request->name, 'description' => \$request->description, 'status' => \$request->status ?? 1]);", 
    "\$role = Role::create(['name' => \$request->name, 'description' => \$request->description, 'status' => \$request->status ?? 1, 'guard_name' => 'web']);", 
    $c
);

// We also need to check update just in case, but update doesn't change guard_name so it's fine.
// Wait, is there any other place? Like Permission creation? (Usually users only create roles)

file_put_contents($file, $c);
echo "Fixed guard_name in VaiTroController";

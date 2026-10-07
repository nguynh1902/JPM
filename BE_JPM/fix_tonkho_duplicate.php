<?php
$file = 'C:/xampp/htdocs/JPM/BE_JPM/app/Domain/Procurement/Controllers/TonKhoController.php';
$c = file_get_contents($file);

$search = "// Only update what we can safely update
            \$tonKho->id_kho = \$request->id_kho ?? \$tonKho->id_kho;";

$replace = "// If changing warehouse, check for duplicates
            if (\$request->id_kho && \$request->id_kho != \$tonKho->id_kho) {
                \$exists = TonKho::where('id_kho', \$request->id_kho)
                                ->where('id_san_pham', \$tonKho->id_san_pham)
                                ->where('loai_san_pham', \$tonKho->loai_san_pham)
                                ->first();
                if (\$exists) {
                    return response()->json(['success' => false, 'message' => 'Sản phẩm này đã có trong kho đích, không thể chuyển kho.'], 400);
                }
            }
            // Only update what we can safely update
            \$tonKho->id_kho = \$request->id_kho ?? \$tonKho->id_kho;";

$c = str_replace($search, $replace, $c);

file_put_contents($file, $c);
echo "Fixed backend TonKho duplicate check\n";

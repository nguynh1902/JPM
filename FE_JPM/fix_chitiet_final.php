<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/ChiTietTinTuc.vue';
$c = file_get_contents($file);

// 1. Fix the double-unwrap bug using ~ as delimiter
$c = preg_replace(
    "~queryFn: \(\) => axiosClient\.get\(`\/public\/tin-tuc\/\$\\{articleId\.value\\}`\)\.then\(r => r\.data\)~",
    "queryFn: () => axiosClient.get(`/public/tin-tuc/\${articleId.value}`)",
    $c
);

// 2. Export getImageUrl
$c = preg_replace(
    "/return \{\s*article,/",
    "return {\n      getImageUrl,\n      article,",
    $c
);

file_put_contents($file, $c);
echo "Fixed bugs with correct regex\n";

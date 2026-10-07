<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/ChiTietTinTuc.vue';
$c = file_get_contents($file);

// Fix the queryFn to just return the response directly since axiosClient already unwraps response.data
$c = preg_replace(
    "/queryFn: \(\) => axiosClient\.get\(`\/public\/tin-tuc\/\#\{articleId\.value\}`\)\.then\(r => r\.data\),/",
    "queryFn: () => axiosClient.get(`/public/tin-tuc/\${articleId.value}`),",
    $c
);
// In case the above regex doesn't match due to template literal escaping
$c = str_replace(
    "queryFn: () => axiosClient.get(`/public/tin-tuc/\${articleId.value}`).then(r => r.data),",
    "queryFn: () => axiosClient.get(`/public/tin-tuc/\${articleId.value}`),",
    $c
);

file_put_contents($file, $c);
echo "Fixed queryFn unwrap bug\n";

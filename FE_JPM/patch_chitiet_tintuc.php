<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/ChiTietTinTuc.vue';
$c = file_get_contents($file);

// Add getImageUrl to setup if missing
if (strpos($c, 'const getImageUrl') === false) {
    $setup_pattern = '/setup\(\) \{/s';
    $setup_replacement = "setup() {\n      const getImageUrl = (path) => {\n        if (!path) return '';\n        if (path.startsWith('http')) return path;\n        return `http://localhost:8000/\${path.startsWith('/') ? path.substring(1) : path}`;\n      };";
    $c = preg_replace($setup_pattern, $setup_replacement, $c);
}

// Return getImageUrl in setup
$c = preg_replace('/return \{\s*articleId,/', "return {\n        getImageUrl,\n        articleId,", $c);

// Fix template to use getImageUrl
$c = preg_replace('/:src="article\.thumbnail \|\| fallback"/', ':src="getImageUrl(article.thumbnail) || fallback"', $c);
$c = preg_replace('/:src="rel\.thumbnail \|\| fallback"/', ':src="getImageUrl(rel.thumbnail) || fallback"', $c);

file_put_contents($file, $c);
echo "Patched ChiTietTinTuc\n";

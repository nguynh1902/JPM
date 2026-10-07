<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/TinTuc.vue';
$c = file_get_contents($file);

$c = str_replace(
    '<a href="#" class="d-flex gap-4 align-items-start text-decoration-none group-hover" v-for="(art, index) in sideArticles" :key="index">',
    '<router-link :to="`/tin-tuc/${art.id}`" class="d-flex gap-4 align-items-start text-decoration-none group-hover" v-for="(art, index) in sideArticles" :key="index">',
    $c
);

$c = str_replace(
    '</a>
            </div>
          </div>',
    '</router-link>
            </div>
          </div>',
    $c
);

file_put_contents($file, $c);
echo "Patched sideArticles router-link\n";

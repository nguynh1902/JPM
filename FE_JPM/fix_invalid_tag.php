<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/client/guest/TinTuc.vue';
$c = file_get_contents($file);

$c = str_replace(
    '</div>
                </a>
              </div>',
    '</div>
                </router-link>
              </div>',
    $c
);

file_put_contents($file, $c);
echo "Fixed invalid end tag\n";

<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/Kho/TonKho.vue';
$c = file_get_contents($file);

$search1 = '<template #option="option">
          <span class="fw-bold">{{ option.code }}</span>
        </template>
      </v-select>';

$replace1 = '<template #option="option">
          <span class="fw-bold">{{ option.code }}</span>
        </template>
        <template #noResults>
          <span class="text-success"><i class="bx bx-check-circle"></i> Đã chọn mã này</span>
        </template>
        <template #noOptions>
          <span class="text-muted">Không có dữ liệu</span>
        </template>
      </v-select>';

$search2 = '<template #option="option">
          <span class="text-primary">{{ option.name }}</span>
        </template>
      </v-select>';

$replace2 = '<template #option="option">
          <span class="text-primary">{{ option.name }}</span>
        </template>
        <template #noResults>
          <span class="text-success"><i class="bx bx-check-circle"></i> Đã chọn tên này</span>
        </template>
        <template #noOptions>
          <span class="text-muted">Không có dữ liệu</span>
        </template>
      </v-select>';

$c = str_replace($search1, $replace1, $c);
$c = str_replace($search2, $replace2, $c);

file_put_contents($file, $c);
echo "Patched TonKho multiselect UI\n";

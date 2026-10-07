<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/Kho/TonKho.vue';
$c = file_get_contents($file);

// 1. Fix payload format in saveData
$search_payload = "const payload = {
        id: this.formData.id,
        kho_id: this.formData.warehouse_id,
        ma_san_pham: this.formData.ma_san_pham,
        ten_san_pham: this.formData.ten_san_pham,
        so_luong_ton: this.formData.so_luong_ton,
        so_luong_cho_giao: this.formData.so_luong_cho_giao
      };";

$replace_payload = "let id_san_pham = null;
      let loai_san_pham = null;
      
      if (!this.isEditMode && this.formData.product_key) {
        // Parse from product_key (e.g. 'san_pham_1' or 'phu_tung_5')
        const parts = this.formData.product_key.split('_');
        id_san_pham = parts.pop();
        loai_san_pham = parts.join('_');
      } else {
        id_san_pham = this.formData.id_san_pham;
        loai_san_pham = this.formData.loai_san_pham;
      }

      const payload = {
        id: this.formData.id,
        id_kho: this.formData.warehouse_id,
        id_san_pham: id_san_pham,
        loai_san_pham: loai_san_pham,
        so_luong_ton: this.formData.so_luong_ton
      };";
$c = str_replace($search_payload, $replace_payload, $c);

// 2. Fix the validation error checking
$search_validation = "if (!this.formData.warehouse_id || !this.formData.ma_san_pham || !this.formData.ten_san_pham) {";
$replace_validation = "if (!this.formData.warehouse_id || (!this.isEditMode && !this.formData.product_key)) {";
$c = str_replace($search_validation, $replace_validation, $c);

// 3. Fix initial form data to include product_key
$search_initial = "warehouse_id: null,
        ma_san_pham: \"\",
        ten_san_pham: \"\",";
$replace_initial = "warehouse_id: null,
        product_key: null,
        ma_san_pham: \"\",
        ten_san_pham: \"\",";
$c = str_replace($search_initial, $replace_initial, $c);

// 4. Implement optimistic UI (onMutate) in saveMutation
$search_mutation = "const saveMutation = useMutation({
      mutationFn: async (payload) => {
        return payload.id 
          ? axiosClient.put('/admin/ton-kho/' + payload.id, payload)
          : axiosClient.post('/admin/ton-kho', payload);
      },
      onError: (err, vars, context) => {
      },";

$replace_mutation = "const saveMutation = useMutation({
      mutationFn: async (payload) => {
        return payload.id 
          ? axiosClient.put('/admin/ton-kho/' + payload.id, payload)
          : axiosClient.post('/admin/ton-kho', payload);
      },
      onMutate: async (payload) => {
        await queryClient.cancelQueries({ queryKey: ['ton-kho'] });
        const previous = queryClient.getQueryData(['ton-kho']);
        
        queryClient.setQueryData(['ton-kho'], (old) => {
          if (!old) return old;
          let oldData = Array.isArray(old) ? old : (old.data?.data || old.data || []);
          if (payload.id) {
            return oldData.map(item => {
              if (item.id === payload.id) {
                return { ...item, so_luong_ton: payload.so_luong_ton, warehouse_id: payload.id_kho };
              }
              return item;
            });
          }
          return oldData;
        });
        return { previous };
      },
      onError: (err, payload, context) => {
        if (context?.previous) {
          queryClient.setQueryData(['ton-kho'], context.previous);
        }
      },";
$c = str_replace($search_mutation, $replace_mutation, $c);

file_put_contents($file, $c);
echo "Patched TonKho UI\n";

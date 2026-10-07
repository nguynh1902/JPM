<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/NoiDung/Faq.vue';
$c = file_get_contents($file);

$pattern = '/const deleteMutation = useMutation\(\{.*?\}\);\s*const saveMutation = useMutation\(\{.*?\}\);\s*return \{ faqs/s';

$replacement = 'const deleteMutation = useMutation({
      mutationFn: async (id) => axiosClient.delete(`/admin/faq/${id}`),
      onMutate: async (id) => {
        await queryClient.cancelQueries({ queryKey: [\'faq\'] });
        const previousData = queryClient.getQueryData([\'faq\']);
        queryClient.setQueryData([\'faq\'], (old) => {
          const list = Array.isArray(old) ? old : (old?.data || []);
          return list.filter(item => item.id !== id);
        });
        return { previousData };
      },
      onError: (err, id, context) => {
        if (context?.previousData) {
          queryClient.setQueryData([\'faq\'], context.previousData);
        }
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: [\'faq\'] });
      }
    });

    const saveMutation = useMutation({
      mutationFn: async (payload) => {
        return payload.id 
          ? axiosClient.put(`/admin/faq/${payload.id}`, payload)
          : axiosClient.post(\'/admin/faq\', payload);
      },
      onMutate: async (newRecord) => {
        await queryClient.cancelQueries({ queryKey: [\'faq\'] });
        const previousData = queryClient.getQueryData([\'faq\']);
        queryClient.setQueryData([\'faq\'], (old) => {
          const list = Array.isArray(old) ? old : (old?.data || []);
          if (newRecord.id) {
            return list.map(item => item.id === newRecord.id ? { ...item, ...newRecord } : item);
          }
          return [{ ...newRecord, id: Date.now() }, ...list]; // Prepend for new
        });
        return { previousData };
      },
      onError: (err, vars, context) => {
        if (context?.previousData) {
          queryClient.setQueryData([\'faq\'], context.previousData);
        }
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: [\'faq\'] });
      }
    });

    return { faqs';

$c = preg_replace($pattern, $replacement, $c);

file_put_contents($file, $c);
echo "Patched Faq.vue\n";

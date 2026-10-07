<?php
$file = 'C:/xampp/htdocs/JPM/FE_JPM/src/views/admin/NoiDung/TinTuc.vue';
$c = file_get_contents($file);

$pattern = '/const deleteMutation = useMutation\(\{.*?\}\);\s*const saveMutation = useMutation\(\{.*?\}\);\s*return \{ articles/s';

$replacement = 'const deleteMutation = useMutation({
      mutationFn: async (id) => axiosClient.delete(`/admin/tin-tuc/${id}`),
      onMutate: async (id) => {
        await queryClient.cancelQueries({ queryKey: ["tin-tuc"] });
        const previousData = queryClient.getQueryData(["tin-tuc"]);
        queryClient.setQueryData(["tin-tuc"], (old) => {
          const list = Array.isArray(old) ? old : (old?.data || []);
          return list.filter(item => item.id !== id);
        });
        return { previousData };
      },
      onError: (err, id, context) => {
        if(context?.previousData) {
          queryClient.setQueryData(["tin-tuc"], context.previousData);
        }
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ["tin-tuc"] });
      }
    });

    const saveMutation = useMutation({
      mutationFn: async ({ payload, isEdit, id }) => {
        if (isEdit) {
          payload.append("_method", "PUT");
          return axiosClient.post(`/admin/tin-tuc/${id}`, payload, { headers: { "Content-Type": "multipart/form-data" } });
        } else {
          return axiosClient.post("/admin/tin-tuc", payload, { headers: { "Content-Type": "multipart/form-data" } });
        }
      },
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: ["tin-tuc"] });
      }
    });

    return { articles';

$c = preg_replace($pattern, $replacement, $c);

file_put_contents($file, $c);
echo "Patched\n";

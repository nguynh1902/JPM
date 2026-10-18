<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Cửa Hàng /</span> Danh Mục Sản Phẩm
    </h4>

    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="card">
          <div class="card-header border-bottom">
            <h5 class="mb-0 fw-medium text-dark">{{ isEditMode ? "Cập Nhật Danh Mục" : "Thêm Mới Danh Mục" }}</h5>
          </div>
          <div class="card-body pt-4">
            <form @submit.prevent="saveData">
              <div class="mb-4">
                <label class="form-label fw-medium text-dark">Danh Mục Cha (Tùy Chọn)</label>
                <v-select v-model="formData.parent_id" :options="parentOptions" placeholder="-- Không có (cấp cao nhất) --" :searchable="true" :can-clear="true" />
              </div>
              <div class="mb-4">
                <label for="name" class="form-label fw-medium text-dark">Tên Danh Mục <span class="text-danger">*</span></label>
                <input type="text" id="name" class="form-control" placeholder="VD: Dầu Nhớt, Phụ Kiện..." v-model="formData.ten_danh_muc" @input="generateSlug" required />
              </div>
              <div class="mb-4">
                <label for="slug" class="form-label fw-medium text-dark">Đường Dẫn (Slug) <span class="text-danger">*</span></label>
                <input type="text" id="slug" class="form-control" placeholder="dau-nhot" v-model="formData.slug" required />
              </div>
              <div class="mb-4">
                <label class="form-label fw-medium text-dark">Trạng Thái <span class="text-danger">*</span></label>
                <v-select v-model="formData.trang_thai" :options="[{value: 1, label: 'Hoạt Động'}, {value: 0, label: 'Tạm Khóa'}]" :can-clear="false" />
              </div>
              <div class="mt-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary px-4" @click="resetForm">Hủy</button>
                <button type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="isSubmitting">
                  <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                  {{ isEditMode ? "Cập Nhật" : "Thêm Mới" }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-md-8 mb-4">
        <div class="card">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-medium text-dark">Danh Sách Danh Mục</h5>
            <div style="width: 250px;">
              <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm mã, tên..." v-model="searchKeyword">
            </div>
          </div>
          <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 230px); min-height: 400px; overflow-y: auto;">
            <table class="table table-hover mb-0">
              <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
                <tr>
                  <th class="text-center text-dark fw-bold" style="width: 5%">STT</th>
                  <th class="text-dark fw-bold" style="width: 20%">Mã Danh Mục</th>
                  <th class="text-dark fw-bold" style="width: 45%">Tên Danh Mục</th>
                  <th class="text-center text-dark fw-bold" style="width: 15%">Trạng Thái</th>
                  <th class="text-center text-dark fw-bold" style="width: 15%">Hành Động</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                <tr v-if="isPending">
                  <td colspan="5" class="text-center py-4 text-muted">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                  </td>
                </tr>
                <tr v-else-if="filteredCategories.length === 0">
                  <td colspan="5" class="text-center py-5 text-muted">
                    <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                    Không có dữ liệu
                  </td>
                </tr>
                <tr v-for="(item, index) in filteredCategories" :key="item.id" v-else>
                  <td class="text-center"><strong>{{ index + 1 }}</strong></td>
                  <td><span class="fw-bold text-primary">{{ item.ma_danh_muc || "N/A" }}</span></td>
                  <td><span class="fw-bold text-dark">{{ item.display_name }}</span></td>
                  <td class="text-center">
                    <span v-if="item.trang_thai === 1 || item.trang_thai === true" class="badge badge-custom bg-pastel-success">HOẠT ĐỘNG</span>
                    <span v-else class="badge badge-custom bg-pastel-danger">TẠM KHÓA</span>
                  </td>
                  <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                      <button type="button" class="action-btn text-primary me-1" @click="openEditModal(item)" title="Sửa"><i class="bx bx-edit-alt"></i></button>
                      <button type="button" class="action-btn text-danger" @click="confirmDelete(item)" title="Xóa"><i class="bx bx-trash"></i></button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        <!-- Tổng số mục -->
        <div class="card-footer py-3 border-top">
          <span class="text-muted fw-medium">Tổng cộng: {{ filteredCategories.length }} mục</span>
        </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from "@vueform/multiselect";
import axiosClient from "../../../api/axiosClient";
import Swal from "sweetalert2";
import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";

export default {
  name: "DanhMucSanPham",
  components: { vSelect },
  setup() {
    const queryClient = useQueryClient();
    const { data: categories, isFetching, isPending } = useQuery({
      queryKey: ["categories"],
      queryFn: () => axiosClient.get("/admin/danh-muc-san-pham").then(res => res.data)
    });

    const deleteMutation = useMutation({
      mutationFn: async (id) => axiosClient.delete(`/admin/danh-muc-san-pham/${id}`),
      onError: (err, id, context) => {
        if (context?.previousData) {
        }
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ["categories"] });
      }
    });

    const saveMutation = useMutation({
      mutationFn: async (payload) => {
        return payload.id 
          ? axiosClient.put(`/admin/danh-muc-san-pham/${payload.id}`, payload)
          : axiosClient.post('/admin/danh-muc-san-pham', payload);
      },
      onError: (err, newRecord, context) => {
        if (context?.previousData) {
        }
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ["categories"] });
      }
    });

    return { categories, isFetching, isPending, queryClient, saveMutation, deleteMutation };
  },
  data() {
    return {
      searchKeyword: "",
      isEditMode: false,
      isSubmitting: false,
      formData: { id: null, parent_id: null, ma_danh_muc: "", ten_danh_muc: "", slug: "", trang_thai: 1 }
    };
  },
  computed: {
    treeCategories() {
      const all = [...(this.categories || [])].sort((a, b) => b.id - a.id);
      const buildTree = (parentId, prefix) => {
        if (prefix === undefined) prefix = "";
        let result = [];
        const children = all.filter(c => c.parent_id === parentId);
        for (const child of children) {
          result.push(Object.assign({}, child, { display_name: prefix + child.ten_danh_muc }));
          result = result.concat(buildTree(child.id, prefix + "— "));
        }
        return result;
      };
      return buildTree(null);
    },
    filteredCategories() {
      if (!this.searchKeyword) return this.treeCategories;
      const q = this.searchKeyword.toLowerCase().trim();
      return this.treeCategories.filter(c =>
        String(c.ma_danh_muc || '').toLowerCase().includes(q) ||
        String(c.ten_danh_muc || '').toLowerCase().includes(q)
      );
    },
    parentOptions() {
      return this.treeCategories
        .filter(c => c.id !== this.formData.id)
        .map(c => ({ value: c.id, label: c.display_name }));
    }
  },
  methods: {
    generateSlug() {
      if (this.formData.ten_danh_muc && !this.isEditMode) {
        this.formData.slug = this.formData.ten_danh_muc
          .toLowerCase()
          .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
          .replace(/[^a-z0-9\s-]/g, "")
          .replace(/\s+/g, "-")
          .replace(/-+/g, "-");
      }
    },
    resetForm() {
      this.isEditMode = false;
      this.formData = { id: null, parent_id: null, ma_danh_muc: "", ten_danh_muc: "", slug: "", trang_thai: 1 };
    },
    openEditModal(item) {
      this.isEditMode = true;
      this.formData = Object.assign({}, item);
    },
    async saveData() {
      if (!this.formData.ten_danh_muc || !this.formData.slug) {
        this.$toast && this.$toast.error("Vui lòng nhập đầy đủ thông tin bắt buộc!");
        return;
      }
      this.isSubmitting = true;
      try {
        await this.saveMutation.mutateAsync(this.formData);
        this.$toast && this.$toast.success(this.isEditMode ? "Cập nhật thành công!" : "Đã thêm danh mục mới!");
        this.resetForm();
      } catch (error) {
        const msg = (error.response && error.response.data && error.response.data.message) || "Có lỗi xảy ra";
        this.$toast && this.$toast.error(msg);
      } finally {
        this.isSubmitting = false;
      }
    },
    async confirmDelete(item) {
      const result = await Swal.fire({
        title: "Xác nhận xóa",
        html: "Bạn có chắc chắn muốn xóa danh mục <b>\"" + item.ten_danh_muc + "\"</b>?<br><br><small class=\"text-danger\">Lưu ý: Không thể xóa nếu có danh mục con hoặc sản phẩm.</small>",
        icon: "warning",
        iconColor: "#ff3e1d",
        showCancelButton: true,
        confirmButtonText: "Đồng ý xóa",
        cancelButtonText: "Hủy",
        reverseButtons: true,
        animation: false,
        customClass: { popup: "swal-shift-up", confirmButton: "btn btn-danger px-4 shadow-sm", cancelButton: "btn btn-outline-secondary px-4 me-3" },
        buttonsStyling: false
      });
      if (result.isConfirmed) {
        try {
          await this.deleteMutation.mutateAsync(item.id);
          this.$toast && this.$toast.success("Đã xóa danh mục!");
        } catch (error) {
          const msg = (error.response && error.response.data && error.response.data.message) || "Có lỗi xảy ra khi xóa";
          this.$toast && this.$toast.error(msg);
        }
      }
    }
  }
};
</script>

<style scoped>
.action-btn {
  background: transparent;
  border: none;
  font-size: 1.15rem;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.15s;
}
.action-btn:hover {
  background: rgba(0,0,0,0.07);
}
</style>







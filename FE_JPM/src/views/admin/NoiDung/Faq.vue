<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Nội Dung /</span> Quản Lý FAQ
    </h4>

    <!-- Card danh sách -->
    <div class="card">
      <div class="card-header border-bottom">
        <h5 class="mb-0 fw-bold text-dark">Danh Sách Câu Hỏi</h5>
      </div>
      
            <!-- Bộ lọc (Filters) -->
      <div class="card-body pt-3 pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="flex-grow-1">
            <input 
              type="text" 
              class="form-control" 
              placeholder="Tìm kiếm câu hỏi..." 
              v-model="filters.search"
            />
          </div>
          <div style="width: 25%">
            <v-select
              v-model="filters.category"
              :options="filterCategoryOptions"
              placeholder="Chuyên mục"
              :can-clear="false"
            />
          </div>
          <div style="width: 20%">
            <v-select
              v-model="filters.status"
              :options="[
                {value: '', label: 'Tất cả trạng thái'},
                {value: 'ACTIVE', label: 'Hoạt động'},
                {value: 'INACTIVE', label: 'Tạm khóa'}
              ]"
              placeholder="Trạng thái"
              :can-clear="false"
            />
          </div>

          <button class="btn btn-secondary text-nowrap" @click="resetFilters">
            Đặt lại
          </button>
          <button class="btn btn-primary text-nowrap" @click="openAddModal" data-bs-toggle="modal" data-bs-target="#modalFAQ">
            <i class="bx bx-plus me-1"></i> Thêm Mới
          </button>
        </div>
      </div>

      <div class="table-responsive text-nowrap custom-scroll" style="max-height: calc(100vh - 260px); min-height: 400px; overflow-y: auto;">
        <table class="table table-hover mb-0">
          <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
            <tr>
              <th class="text-center" style="width: 5%">STT</th>
              <th style="width: 50%">Câu Hỏi & Trả Lời</th>
              <th class="text-center" style="width: 15%">Chuyên Mục</th>
              <th class="text-center" style="width: 15%">Trạng Thái</th>
              <th class="text-center" style="width: 15%">Hành Động</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr v-if="filteredFaqs.length === 0">
              <td colspan="5" class="text-center py-5 text-muted">
                <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                Không có dữ liệu
              </td>
            </tr>
            <tr v-for="(item, index) in filteredFaqs" :key="item.id">
              <td class="text-center"><strong>{{ index + 1 }}</strong></td>
              <td>
                <div class="d-flex flex-column" style="white-space: normal;">
                  <span class="fw-bold text-dark mb-1">{{ item.question }}</span>
                  <span class="text-muted small text-wrap" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ item.answer }}
                  </span>
                </div>
              </td>
              <td class="text-center">
                <span class="fw-medium text-primary">{{ item.category }}</span>
              </td>
              <td class="text-center">
                                <span v-if="item.status === 'ACTIVE'" class="badge badge-custom bg-pastel-success">HOẠT ĐỘNG</span>
                <span v-else class="badge badge-custom bg-pastel-danger">TẠM KHÓA</span>
              </td>
              <td class="text-center">
                <div class="d-flex align-items-center justify-content-center">
                  <button type="button" class="action-btn text-primary me-2" @click="openEditModal(item)" data-bs-toggle="modal" data-bs-target="#modalFAQ" title="Sửa">
                    <i class="bx bx-edit-alt"></i>
                  </button>
                  <button type="button" class="action-btn text-danger" @click="confirmDelete(item)" title="Xóa">
                    <i class="bx bx-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Tổng cộng -->
      <div class="card-footer py-3 border-top">
        <span class="text-muted fw-medium">Tổng cộng: {{ filteredFaqs.length }} mục</span>
      </div>
    </div>

        <!-- Modal Form -->
    <div class="modal fade" id="modalFAQ" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-bottom pb-3">
            <h4 class="modal-title fw-bold text-dark mb-0 fs-4">
              {{ isEditMode ? 'Cập Nhật Câu Hỏi' : 'Thêm Câu Hỏi Mới' }}
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="resetForm"></button>
          </div>
          <form @submit.prevent="saveData">
                        <div class="modal-body pb-2 pt-4">
              <div class="row">
                <!-- Cột trái: Thông tin ngắn -->
                <div class="col-md-5 pe-md-4">
                  <div class="mb-3">
                    <label class="form-label fw-medium text-dark">Câu Hỏi (Question) <span class="text-danger">*</span></label>
                    <input 
                      type="text" 
                      class="form-control" 
                      v-model="formData.question" 
                      placeholder="Nhập nội dung câu hỏi..." 
                      required
                    >
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label fw-medium text-dark">Chuyên Mục <span class="text-danger">*</span></label>
                    <v-select
                      v-model="formData.category"
                      :options="faqCategories"
                      placeholder="Chọn chuyên mục..."
                      required
                    />
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-medium text-dark">Trạng Thái <span class="text-danger">*</span></label>
                    <v-select
                      v-model="formData.status"
                      :options="[
                        {value: 'ACTIVE', label: 'Hoạt động'},
                        {value: 'INACTIVE', label: 'Tạm khóa'}
                      ]"
                      :can-clear="false"
                      required
                    />
                  </div>
                </div>
                
                <!-- Cột phải: Câu Trả Lời -->
                <div class="col-md-7 border-start ps-md-4 d-flex flex-column">
                  <div class="mb-3 flex-grow-1 d-flex flex-column">
                    <label class="form-label fw-medium text-dark">Câu Trả Lời (Answer) <span class="text-danger">*</span></label>
                    <textarea 
                      class="form-control flex-grow-1" 
                      v-model="formData.answer" 
                      placeholder="Nhập nội dung trả lời..." 
                      style="resize: none; min-height: 205px;"
                      required
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer border-top pt-3">
              <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" @click="resetForm">Hủy Bỏ</button>
              <button type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="isSubmitting">
                <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                {{ isEditMode ? 'Cập Nhật Câu Hỏi' : 'Lưu & Đăng Bài' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import vSelect from '@vueform/multiselect';
import axiosClient from '../../../api/axiosClient';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import Swal from 'sweetalert2';

export default {
  name: "Faq",
  components: {
    vSelect
  },
  setup() {
    const queryClient = useQueryClient();
    
    const { data: rawCategories, isFetching: isFetchingCategories } = useQuery({
      queryKey: ['chuyen-muc'],
      queryFn: async () => {
        const res = await axiosClient.get('/admin/chuyen-muc');
        return res.data?.data || res.data || [];
      }
    });

    const { data: faqs, isFetching: isFetchingFaqs } = useQuery({
      queryKey: ['faq'],
      queryFn: () => axiosClient.get('/admin/faq').then(res => res.data || [])
    });

    const deleteMutation = useMutation({
      mutationFn: async (id) => axiosClient.delete(`/admin/faq/${id}`),
      onMutate: async (id) => {
        await queryClient.cancelQueries({ queryKey: ['faq'] });
        const previousData = queryClient.getQueryData(['faq']);
        queryClient.setQueryData(['faq'], (old) => {
          const list = Array.isArray(old) ? old : (old?.data || []);
          return list.filter(item => item.id !== id);
        });
        return { previousData };
      },
      onError: (err, id, context) => {
        if (context?.previousData) {
          queryClient.setQueryData(['faq'], context.previousData);
        }
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ['faq'] });
      }
    });

    const saveMutation = useMutation({
      mutationFn: async (payload) => {
        return payload.id 
          ? axiosClient.put(`/admin/faq/${payload.id}`, payload)
          : axiosClient.post('/admin/faq', payload);
      },
      onMutate: async (newRecord) => {
        await queryClient.cancelQueries({ queryKey: ['faq'] });
        const previousData = queryClient.getQueryData(['faq']);
        queryClient.setQueryData(['faq'], (old) => {
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
          queryClient.setQueryData(['faq'], context.previousData);
        }
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ['faq'] });
      }
    });

    return { faqs, isFetchingFaqs, isFetchingCategories, rawCategories, queryClient, deleteMutation, saveMutation };
  },
  data() {
    return {
      filters: {
        search: '',
        category: '',
        status: ''
      },
      isEditMode: false,
      isSubmitting: false,
      formData: this.getInitialFormData()
    };
  },
  computed: {
    isFetching() {
      return this.isFetchingFaqs || this.isFetchingCategories;
    },
    filterCategoryOptions() {
      return [
        { value: '', label: 'Tất cả chuyên mục' },
        ...this.faqCategories
      ];
    },
    faqCategories() {
      const all = this.rawCategories || [];
      return all.filter(c => c.type === 'FAQ').map(c => ({ value: c.name, label: c.name }));
    },
    filteredFaqs() {
      let result = this.faqs || [];
      if (this.filters.search) {
        const lowerQuery = this.filters.search.toLowerCase().trim();
        result = result.filter(item => 
          String(item.question || '').toLowerCase().includes(lowerQuery) || 
          String(item.answer || '').toLowerCase().includes(lowerQuery)
        );
      }
      if (this.filters.category) {
        result = result.filter(item => item.category === this.filters.category);
      }
      if (this.filters.status) {
        result = result.filter(item => item.status === this.filters.status);
      }
      return result;
    }
  },
  methods: {
    resetFilters() {
      this.filters = { search: '', category: '', status: '' };
    },

    getInitialFormData() {
      return {
        id: null,
        question: '',
        answer: '',
        category: '',
        status: 'ACTIVE'
      };
    },
    openAddModal() {
      this.isEditMode = false;
      this.formData = this.getInitialFormData();
    },
    openEditModal(item) {
      this.isEditMode = true;
      this.formData = { ...item };
    },
    resetForm() {
      this.isEditMode = false;
      this.formData = this.getInitialFormData();
    },
    async saveData() {
      this.isSubmitting = true;
      this.saveMutation.mutate(this.formData, {
        onSuccess: () => {
          this.$toast?.success(this.isEditMode ? 'Cập nhật thành công!' : 'Thêm mới thành công!');
          this.resetForm();
          const closeBtn = document.querySelector("#modalFAQ .btn-close");
          if (closeBtn) closeBtn.click();
        },
        onError: () => {
          this.$toast?.error('Lỗi khi lưu FAQ!');
        },
        onSettled: () => {
          this.isSubmitting = false;
        }
      });
    },
    async confirmDelete(item) {
      const result = await Swal.fire({
        title: 'Xóa FAQ này?',
        html: `Dữ liệu sau khi xóa sẽ không thể khôi phục!<br><br><small class="text-danger">Câu hỏi: ${item.question}</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Đồng ý xóa',
        cancelButtonText: 'Hủy',
        reverseButtons: true,
        customClass: { popup: "swal-shift-up", confirmButton: "btn btn-danger px-4 shadow-sm", cancelButton: "btn btn-outline-secondary px-4 me-3" },
        buttonsStyling: false
      });
      if (result.isConfirmed) {
        this.deleteMutation.mutate(item.id, {
          onSuccess: () => {
            this.$toast?.success('Đã xóa thành công!');
          },
          onError: () => {
            this.$toast?.error('Lỗi khi xóa!');
          }
        });
      }
    }
  }
};
</script>























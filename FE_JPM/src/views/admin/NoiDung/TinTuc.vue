<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Nội Dung /</span> Quản Lý Tin Tức
    </h4>

    <div class="card">
      <div class="card-header border-bottom">
        <h5 class="mb-0 fw-bold text-dark">Danh Sách Bài Viết</h5>
      </div>

      <!-- Bộ lọc (Filters) -->
      <div class="card-body pt-3 pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="flex-grow-1">
            <input 
              type="text" 
              class="form-control" 
              placeholder="Tìm kiếm tiêu đề bài viết..." 
              v-model="filters.title"
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
                {value: 'PUBLISHED', label: 'Hoạt động'},
                {value: 'DRAFT', label: 'Tạm khóa'}
              ]"
              placeholder="Trạng thái"
              :can-clear="false"
            />
          </div>

          <button class="btn btn-secondary text-nowrap" @click="resetFilters">
            Đặt lại
          </button>
          <button class="btn btn-primary text-nowrap" @click="openAddModal" data-bs-toggle="modal" data-bs-target="#modalNews">
            <i class="bx bx-plus me-1"></i> Thêm Mới
          </button>
        </div>
      </div>
      <!-- Bảng dữ liệu -->
      <div class="table-responsive text-nowrap custom-scroll" style="height: calc(100vh - 230px); min-height: 400px; overflow-y: auto;">
        <table class="table table-hover">
          <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
            <tr>
              <th class="text-center" style="width: 5%">STT</th>
              <th class="text-center" style="width: 10%">Ảnh</th>
              <th style="width: 35%">Tiêu Đề & Chuyên Mục</th>
              <th class="text-center" style="width: 15%">Ngày Đăng</th>
              <th class="text-center" style="width: 10%">Lượt Xem</th>
              <th class="text-center" style="width: 15%">Trạng Thái</th>
              <th class="text-center" style="width: 10%">Hành Động</th>
            </tr>
          </thead>
                    <tbody class="table-border-bottom-0">
            <tr v-if="isPending">
              <td colspan="7" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="filteredArticles.length === 0">
              <td colspan="7" class="text-center py-5 text-muted">
                <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                Không có dữ liệu
              </td>
            </tr>
            <tr v-for="(item, index) in filteredArticles" :key="item.id">
              <td class="text-center align-middle"><strong>{{ index + 1 }}</strong></td>
              <td class="text-center align-middle">
                <div class="d-inline-flex justify-content-center align-items-center border rounded bg-white" style="width: 60px; height: 60px;">
                  <img v-if="item.thumbnail" :src="getImageUrl(item.thumbnail)" alt="Thumbnail" style="max-height: 40px; max-width: 50px; object-fit: contain;">
                  <i v-else class="bx bx-image text-muted fs-3"></i>
                </div>
              </td>
              
              <td class="align-middle">
                <span class="fw-bold text-dark fs-6 d-block text-wrap" style="line-height: 1.4;">
                  {{ item.title }}
                </span>
                <span class="badge badge-custom mt-1" :class="getCategoryBadge(item.category)">{{ item.category }}</span>
              </td>
              <td class="text-center align-middle">
                <span class="fw-medium text-dark d-block" style="font-size: 0.95rem;">{{ formatDate(item.created_at) }}</span>
                <span class="text-primary d-block" style="font-size: 0.8rem;">Bởi: {{ item.author || 'Admin' }}</span>
              </td>
              <td class="text-center align-middle"><span class="fw-medium text-secondary">{{ item.views }}</span></td>
              <td class="text-center align-middle">
                <span v-if="item.status === 'PUBLISHED'" class="badge badge-custom bg-pastel-success">HOẠT ĐỘNG</span>
                <span v-else class="badge badge-custom bg-pastel-danger">TẠM KHÓA</span>
              </td>
              <td class="text-center align-middle">
                <div class="d-flex align-items-center justify-content-center">
                  <button 
                    type="button" 
                    class="action-btn text-primary me-1" 
                    @click="openEditModal(item)"
                    data-bs-toggle="modal"
                    data-bs-target="#modalNews"
                    title="Chỉnh sửa"
                  >
                    <i class="bx bx-edit-alt"></i>
                  </button>
                  <button 
                    type="button" 
                    class="action-btn text-danger" 
                    @click="confirmDelete(item)"
                    title="Xóa"
                  >
                    <i class="bx bx-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
            <!-- Tổng số mục -->
      <div class="card-footer py-3 border-top">
        <span class="text-muted fw-medium">Tổng cộng: {{ (filteredArticles && filteredArticles.length) ? filteredArticles.length : 0 }} mục</span>
      </div>
    </div>

    <!-- Modal Form (Layout 2) -->
        <!-- Modal Form (Layout 2 - Phụ Tùng Style) -->
    <div class="modal fade" id="modalNews" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <form @submit.prevent="saveData" class="modal-content">
          <div class="modal-header border-bottom pb-3">
            <h5 class="modal-title fw-bold text-dark fs-4">
              {{ isEditMode ? 'Chỉnh Sửa Bài Viết' : 'Thêm Bài Viết Mới' }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="resetForm"></button>
          </div>
          <div class="modal-body pb-2 pt-4">
            <div class="row">
              <!-- Cột trái: Thông tin cơ bản & Mô tả -->
              <div class="col-md-7 pe-md-4">
                <div class="mb-3">
                  <label class="form-label fw-medium text-dark">Tiêu Đề Bài Viết <span class="text-danger">*</span></label>
                  <input type="text" class="form-control form-control-lg" v-model="formData.title" placeholder="Nhập tiêu đề..." required />
                </div>
                
                <div class="mb-3">
                  <label class="form-label fw-medium text-dark">Chuyên Mục <span class="text-danger">*</span></label>
                  <v-select
                    v-model="formData.category"
                    :options="newsCategories"
                    placeholder="Chọn chuyên mục..."
                    required
                  />
                </div>

                <div class="mb-3">
                  <label class="form-label fw-medium text-dark">Trích Dẫn Ngắn</label>
                  <textarea class="form-control" v-model="formData.summary" rows="3" placeholder="Đoạn văn ngắn tóm tắt nội dung..."></textarea>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-medium text-dark">Nội Dung Chi Tiết <span class="text-danger">*</span></label>
                                    <div class="border rounded p-2 bg-light mb-1 d-flex align-items-center">
                    <div>
                      <i class="bx bx-bold fs-4 me-2 cursor-pointer"></i>
                      <i class="bx bx-italic fs-4 me-2 cursor-pointer"></i>
                      <i class="bx bx-underline fs-4 me-2 cursor-pointer"></i>
                      <i class="bx bx-link fs-4 me-2 cursor-pointer"></i>
                      <i class="bx bx-image-add fs-4 cursor-pointer"></i>
                    </div>
                    <div class="ms-auto">
                      <input type="file" accept=".docx" class="d-none" ref="wordInput" @change="handleWordUpload" />
                      <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" @click="$refs.wordInput.click()">
                        <i class="bx bx-file-blank me-1"></i> Nhập từ file Word (.docx)
                      </button>
                    </div>
                  </div>
                  <textarea class="form-control content-textarea" v-model="formData.content" rows="20" placeholder="Soạn thảo nội dung chi tiết bài viết ở đây..." required></textarea>
                </div>
              </div>

              <!-- Cột phải: Hình ảnh & Trạng thái -->
              <div class="col-md-5 border-start ps-md-4">
                <div class="mb-3">
                  <label class="form-label fw-medium text-dark">Trạng Thái <span class="text-danger">*</span></label>
                  <v-select
                    v-model="formData.status"
                    :options="[
                      {value: 'DRAFT', label: 'Tạm khóa'},
                      {value: 'PUBLISHED', label: 'Hoạt động'}
                    ]"
                    placeholder="Chọn trạng thái..."
                    required
                    :can-clear="false"
                  />
                </div>

                <div class="mb-3">
                  <label class="form-label fw-medium text-dark">Đường dẫn Video (YouTube URL)</label>
                  <div class="input-group">
                    <span class="input-group-text bg-light text-danger"><i class="bx bxl-youtube"></i></span>
                    <input type="url" class="form-control" v-model="formData.youtube_url" placeholder="VD: https://www.youtube.com/watch?v=..." />
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-medium text-dark">Ảnh Đại Diện</label>
                  <input
                    type="file"
                    class="form-control mb-2"
                    accept="image/png, image/jpeg, image/svg+xml"
                    @change="handleThumbnailUpload"
                  />
                  <div class="d-flex justify-content-center align-items-center border rounded bg-light mt-2 position-relative" style="height: 180px;">
                    <img v-if="formData.thumbnail" :src="getImageUrl(formData.thumbnail)" style="max-height: 160px; max-width: 100%; object-fit: contain;">
                    <button v-if="formData.thumbnail" type="button" class="btn btn-icon btn-sm bg-white text-danger position-absolute top-0 end-0 m-2 rounded-circle shadow-sm border" @click="removeThumbnail" style="width: 28px; height: 28px; padding: 0;" title="Xóa ảnh">
                      <i class="bx bx-trash"></i>
                    </button>
                    <span class="text-muted" v-else><i class='bx bx-image fs-1'></i></span>
                  </div>
                </div>
                <div class="mb-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                          <label class="form-label fw-medium text-dark mb-0">Thư Viện Ảnh Phụ</label>
                          <label for="gallery_upload" class="btn btn-sm btn-primary py-1 px-2 mb-0 d-inline-flex align-items-center gap-1" style="cursor: pointer; box-shadow: none;">
                            <i class="bx bx-upload"></i> Chọn nhiều ảnh
                          </label>
                        </div>
                        <input type="file" id="gallery_upload" class="d-none" accept="image/*" multiple @change="handleGalleryUpload" />
                  
                  <!-- Existing Gallery -->
                  <div v-if="formData.gallery && formData.gallery.length > 0" class="mb-2">
                    <div v-for="(img, idx) in formData.gallery" :key="'old_'+idx" class="d-flex gap-2 mb-2 align-items-center">
                      <div class="d-flex align-items-center justify-content-center border rounded bg-white flex-shrink-0" style="width: 44px; height: 44px; overflow: hidden;">
                        <img :src="getImageUrl(img)" style="width: 100%; height: 100%; object-fit: cover;" />
                      </div>
                      <a :href="getImageUrl(img)" target="_blank" class="text-primary text-decoration-none text-truncate flex-grow-1 fw-medium" style="font-size: 0.85rem;" title="Xem ảnh">{{ img }}</a>
                      <button type="button" class="btn btn-sm btn-icon btn-outline-danger ms-auto" @click="formData.gallery.splice(idx, 1)" title="Xóa"><i class="bx bx-trash"></i></button>
                    </div>
                  </div>

                  <!-- New Uploads -->
                  <div v-if="galleryFiles && galleryFiles.length > 0" class="mb-2">
                    <div v-for="(file, idx) in galleryFiles" :key="'new_'+idx" class="d-flex gap-2 mb-2 align-items-center">
                      <div class="d-flex align-items-center justify-content-center border rounded bg-white flex-shrink-0" style="width: 44px; height: 44px; overflow: hidden;">
                        <img :src="getBlobUrl(file)" style="width: 100%; height: 100%; object-fit: cover;" />
                      </div>
                      <a :href="getBlobUrl(file)" target="_blank" class="text-primary text-decoration-none text-truncate flex-grow-1 fw-medium" style="font-size: 0.85rem;" title="Xem ảnh">{{ file.name }}</a>
                      <button type="button" class="btn btn-sm btn-icon btn-outline-danger ms-auto" style="flex-shrink: 0" @click="galleryFiles.splice(idx, 1)" title="Xóa"><i class="bx bx-trash"></i></button>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
          <div class="modal-footer border-top pt-3">
            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" @click="resetForm">Hủy Bỏ</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="isSubmitting">
              <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
              {{ isEditMode ? 'Cập Nhật Bài Viết' : 'Lưu & Đăng Bài' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import mammoth from 'mammoth';
import vSelect from '@vueform/multiselect';
import axiosClient from '../../../api/axiosClient';
import Swal from 'sweetalert2';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';

export default {
  name: "TinTuc",
  components: {
    vSelect
  },
  setup() {
    const queryClient = useQueryClient();

        const fetchCategories = async () => {
      const res = await axiosClient.get('/admin/chuyen-muc');
      return res.data?.data || res.data || [];
    };

    const fetchArticles = async () => {
      const res = await axiosClient.get('/admin/tin-tuc');
      return res.data || [];
    };

            const { data: articles, isFetching: isFetchingArticles , isPending: isPendingarticles } = useQuery({
      queryKey: ['tin-tuc'],
      queryFn: fetchArticles,
      staleTime: 1000 * 60 * 5
    });

    const { data: rawCategories, isFetching: isFetchingCategories , isPending: isPendingrawCategories } = useQuery({
      queryKey: ['chuyen-muc'],
      queryFn: fetchCategories,
      staleTime: 1000 * 60 * 5
    });

    const deleteMutation = useMutation({
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

    return { articles, isFetchingArticles, isFetchingCategories, rawCategories, queryClient, deleteMutation, saveMutation };
  },
      data() {
      return {
        filters: {
          title: '',
          category: '',
          status: ''
        },
        isEditMode: false,
        isSubmitting: false,
        galleryFiles: [],
        thumbnailFile: null,
        formData: this.getInitialFormData()
      };
    },
    computed: {
    isFetching() {
      return this.isFetchingArticles || this.isFetchingCategories;
    },
    newsCategories() {
      const all = this.rawCategories || [];
      return all.filter(c => c.type === 'NEWS').map(c => ({ value: c.name, label: c.name }));
    },
    filterCategoryOptions() {
      return [
        { value: '', label: 'Tất cả chuyên mục' },
        ...this.newsCategories
      ];
    },
    filteredArticles() {
      return (this.articles || []).filter(item => {
        let match = true;
        if (this.filters.title && !item.title.toLowerCase().includes(this.filters.title.toLowerCase())) match = false;
        if (this.filters.category && item.category !== this.filters.category) match = false;
        if (this.filters.status && item.status !== this.filters.status) match = false;
        return match;
      });
    }
  },
    methods: {
    async handleWordUpload(event) {
      const file = event.target.files[0];
      if (!file) return;
      this.isSubmitting = true;
      const reader = new FileReader();
      reader.onload = async (e) => {
        try {
          const arrayBuffer = e.target.result;
          const result = await mammoth.convertToHtml({ arrayBuffer: arrayBuffer });
          this.formData.content = result.value;
          this.$toast?.success('Đã nhập thành công nội dung từ file Word!');
        } catch (error) {
          console.error(error);
          this.$toast?.error('Lỗi khi đọc file Word! Vui lòng kiểm tra lại file.');
        } finally {
          this.isSubmitting = false;
          event.target.value = '';
        }
      };
      reader.readAsArrayBuffer(file);
    },
        handleGalleryUpload(event) {
      const files = Array.from(event.target.files);
      if (files && files.length > 0) {
        this.galleryFiles = [...this.galleryFiles, ...files];
      }
      event.target.value = "";
    },
    getBlobUrl(file) {
      if (!file) return "";
      return URL.createObjectURL(file);
    },
    resetFilters() {
      this.filters = {
        title: '',
        category: '',
        status: ''
      };
    },
        getInitialFormData() {
      return {
        id: null,
        title: '',
        category: '',
        thumbnail: '',
        youtube_url: '',
        summary: '',
        content: '',
        status: 'PUBLISHED',
        author: 'Admin',
        created_at: new Date().toISOString(),
        views: 0,
        gallery: []
      };
    },
            getCategoryBadge(category) {
      return 'bg-label-primary';
    },
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    openAddModal() {
      this.isEditMode = false;
      this.formData = this.getInitialFormData();
    },
    openEditModal(item) {
      this.isEditMode = true;
      this.formData = { ...item };
      this.formData.gallery = item.gallery ? (Array.isArray(item.gallery) ? [...item.gallery] : JSON.parse(item.gallery)) : [];
      this.galleryFiles = [];
      const gu = document.getElementById('gallery_upload');
      if (gu) gu.value = '';
    },
    resetForm() {
      this.isEditMode = false;
      this.formData = this.getInitialFormData();
    },
    getImageUrl(path) {
      if (!path) return '';
      if (path.startsWith('blob:') || path.startsWith('http')) return path;
      return `http://localhost:8000/${path.replace(/^\/?storage\//, 'storage/')}`;
    },
    handleThumbnailUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.thumbnailFile = file;
        this.formData.thumbnail = URL.createObjectURL(file);
      }
    },
    removeThumbnail() {
      this.thumbnailFile = null;
      this.formData.thumbnail = '';
    },
        async saveData() {
      this.isSubmitting = true;
      try {
        const payload = new FormData();
      Object.keys(this.formData).forEach(key => {
        if (key !== 'gallery' && (key !== 'thumbnail' || !this.thumbnailFile)) {
          payload.append(key, this.formData[key] || '');
        }
      });
      if (this.thumbnailFile) {
        payload.append('thumbnail', this.thumbnailFile);
      }

              await this.saveMutation.mutateAsync({ payload, isEdit: this.isEditMode, id: this.formData.id, optimisticRecord: { ...this.formData, gallery: [...(this.formData.gallery || []), ...this.galleryFiles.map(f => URL.createObjectURL(f))] } });
        this.$toast?.success(this.isEditMode ? 'Cập nhật thành công!' : 'Thêm mới thành công!');
        this.resetForm();
                const closeBtn = document.querySelector("#modalNews .btn-close");
        if (closeBtn) closeBtn.click();
      } catch (error) {
        this.$toast?.error('Lỗi khi lưu bài viết!');
      } finally {
        this.isSubmitting = false;
      }
    },
    async confirmDelete(item) {
      const result = await Swal.fire({
        title: "Xác nhận xóa",
        html: "Bạn có chắc chắn muốn xóa bài viết <b>\"" + item.title + "\"</b>?<br><br><small class=\"text-danger\">Lưu ý: Hành động này không thể hoàn tác.</small>",
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
        this.deleteMutation.mutate(item.id, {
          onSuccess: () => {
            this.$toast?.success('Đã xóa bài viết!');
          },
          onError: () => {
            this.$toast?.error('Xóa thất bại!');
          }
        });
      }
    }
  }
};
</script>

<style scoped>
.content-textarea {
  min-height: 250px !important;
  height: 250px !important;
}
.text-wrap {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
.cursor-pointer {
  cursor: pointer;
}
</style>



































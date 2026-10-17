<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Cửa Hàng /</span> Danh Sách Sản Phẩm
    </h4>

    <div class="card">
      <div class="card-header border-bottom">
        <h5 class="mb-0 fw-bold text-dark">Kho Sản Phẩm</h5>
      </div>

      <div class="card-body pt-3 pb-3">
        <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center">
          <div class="flex-grow-1">
              <input type="text" class="form-control" placeholder="Tìm Mã SP hoặc Tên..." v-model="filters.search">
          </div>
          <div style="width: 220px;">
            <v-select
              v-model="filters.danh_muc_san_pham_id"
              :options="[{value: null, label: 'Tất cả Danh Mục'}, ...danhMucListOptions]"
              placeholder="Danh Mục"
              :searchable="true"
              :can-clear="false"
            />
          </div>
          <button class="btn btn-secondary text-nowrap" @click="resetFilters">
            Đặt lại
          </button>
          <button v-if="currentUserRole === 'ADMIN'" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#modalSanPham" @click="openAddModal">
            <i class="bx bx-plus me-1"></i> Thêm Mới
          </button>
        </div>
      </div>

      <div class="table-responsive text-nowrap custom-scroll" style="max-height: calc(100vh - 260px); min-height: 400px; overflow-y: auto;">
        <table class="table table-hover mb-0">
          <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
            <tr>
              <th class="text-center" style="width: 5%">STT</th>
              <th class="text-center" style="width: 5%">Hình Ảnh</th>
              <th style="width: 15%">Mã Sản Phẩm</th>
              <th style="width: 25%">Tên Sản Phẩm</th>
              <th style="width: 20%">Danh Mục</th>
              <th class="text-end" style="width: 10%">Giá Bán</th>
              <th class="text-center" style="width: 10%">Trạng thái</th>
              <th class="text-center" style="width: 10%" v-if="currentUserRole === 'ADMIN'">Hành động</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr v-if="isPending">
              <td :colspan="currentUserRole === 'ADMIN' ? 8 : 7" class="text-center py-4 text-muted">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="filteredSanPhams.length === 0">
              <td :colspan="currentUserRole === 'ADMIN' ? 8 : 7" class="text-center py-5 text-muted">
                <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                Không có dữ liệu
              </td>
            </tr>
            <tr v-for="(item, index) in filteredSanPhams" :key="item.id" v-else>
              <td class="text-center align-middle"><strong>{{ index + 1 }}</strong></td>
              <td class="text-center align-middle">
                <div class="d-inline-flex justify-content-center align-items-center border rounded" style="width: 60px; height: 60px; background-color: #fff;">
                  <img v-if="item.hinh_anh_chinh" :src="getImageUrl(item.hinh_anh_chinh)" alt="Image" style="max-height: 40px; max-width: 50px; object-fit: contain;" @error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='inline-block'">
                  <i class="bx bx-image text-muted fs-3" :style="{ display: item.hinh_anh_chinh ? 'none' : 'inline-block' }"></i>
                </div>
              </td>
              <td class="align-middle">
                <span class="fw-bold text-primary">{{ item.ma_san_pham }}</span>
              </td>
              <td class="align-middle text-wrap" style="min-width: 200px;">
                <span class="fw-bold text-dark d-block">{{ item.ten_san_pham }}</span>
              </td>
              <td class="align-middle">
                <span class="fw-semibold text-secondary">{{ getDanhMucName(item.danh_muc_san_pham_id) }}</span>
              </td>
              <td class="text-end align-middle">
                <span v-if="item.is_gia_lien_he" class="fw-bold text-danger">Liên hệ</span>
                <span v-else class="fw-bold text-danger">{{ formatCurrency(item.gia_ban) }}</span>
              </td>
              <td class="text-center align-middle">
                <span v-if="item.trang_thai === 1" class="badge badge-custom bg-pastel-success">HOẠT ĐỘNG</span>
                <span v-else class="badge badge-custom bg-pastel-danger">TẠM KHÓA</span>
              </td>
              <td class="text-center align-middle" v-if="currentUserRole === 'ADMIN'">
                <div class="d-flex align-items-center justify-content-center">
                  <button type="button" class="action-btn text-primary me-1" @click="openEditModal(item)" data-bs-toggle="modal" data-bs-target="#modalSanPham">
                    <i class="bx bx-edit-alt"></i>
                  </button>
                  <button type="button" class="action-btn text-danger" @click="confirmDelete(item)">
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
          <span class="text-muted fw-medium">Tổng cộng: {{ filteredSanPhams.length }} mục</span>
        </div>
    </div>
  </div>

  <!-- Modal Form Thêm/Sửa -->
  <div class="modal fade" id="modalSanPham" tabindex="-1" aria-hidden="true" ref="modalSanPham">
    <div class="modal-dialog modal-xl mt-5" role="document">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header border-bottom pb-3">
          <h4 class="modal-title fw-bold text-dark mb-0 fs-4" id="modalSanPhamTitle">
            {{ isEditMode ? 'Cập Nhật Sản Phẩm' : 'Thêm Mới Sản Phẩm' }}
          </h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btnCloseModal" @click="resetForm"></button>
        </div>
        <form @submit.prevent="saveData">
          <div class="modal-body pb-2 pt-4">
            <div class="row">
              <!-- Cột trái -->
              <div class="col-md-7 pe-md-4">
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <label class="form-label fw-medium text-dark">Mã SP (Part #) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control text-uppercase fw-bold" v-model="formData.ma_san_pham" required placeholder="VD: 50130">
                  </div>
                  <div class="col-md-8 mb-3">
                    <label class="form-label fw-medium text-dark">Tên Sản Phẩm <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="formData.ten_san_pham" @input="generateSlug" required placeholder="Tên Sản Phẩm...">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 mb-3">
                    <label class="form-label fw-medium text-dark">Đường Dẫn (Slug) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" v-model="formData.slug" required placeholder="duong-dan-san-pham">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 mb-3">
                    <label class="form-label fw-medium text-dark">Danh Mục <span class="text-danger">*</span></label>
                    <v-select v-model="formData.danh_muc_san_pham_id" :options="danhMucListOptions" placeholder="Chọn..." searchable required />
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 mb-3">
                    <label class="form-label fw-medium text-dark">Mô tả chi tiết</label>
                    <textarea class="form-control" rows="5" v-model="formData.mo_ta_chi_tiet" placeholder="Nhập mô tả chi tiết sản phẩm..."></textarea>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 mb-3">
                    <label class="form-label fw-medium text-dark">Thông số kỹ thuật</label>
                    <textarea class="form-control" rows="5" v-model="formData.thong_so_ky_thuat" placeholder="Nhập thông số kỹ thuật sản phẩm..."></textarea>
                  </div>
                </div>
                <div class="row mt-2">
                  <div class="col-md-12 mb-3">
                    <label class="form-label fw-medium text-dark">Video Youtube URL (Tùy chọn)</label>
                    <input type="text" class="form-control" v-model="formData.video_url" placeholder="VD: https://www.youtube.com/embed/...">
                  </div>
                </div>
              </div>
              <!-- Cột phải -->
              <div class="col-md-5 border-start ps-md-4">
                <div class="row">
                  <div class="col-md-12 mb-3">
                    <label class="form-label fw-medium text-dark">Hình Ảnh Chính</label>
                    <input type="file" id="hinh_anh_chinh" class="form-control mb-2" accept="image/*" @change="handleMainImageUpload">
                    <div class="d-flex justify-content-center align-items-center border rounded bg-light mb-3 position-relative" style="height: 180px;">
                      <img v-if="mainImagePreview || formData.hinh_anh_chinh" :src="mainImagePreview || getImageUrl(formData.hinh_anh_chinh)" style="max-height: 160px; max-width: 100%; object-fit: contain;" @error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='inline-block'">
                      <button v-if="mainImagePreview || formData.hinh_anh_chinh" type="button" class="btn btn-icon btn-sm bg-white text-danger position-absolute top-0 end-0 m-2 rounded-circle shadow-sm border" @click="removeMainImage" style="width: 28px; height: 28px; padding: 0;" title="Xóa ảnh chính"><i class="bx bx-trash"></i></button>
                      <span class="text-muted" :style="{ display: (mainImagePreview || formData.hinh_anh_chinh) ? 'none' : 'inline-block' }"><i class='bx bx-image fs-1'></i></span>
                    </div>
                    <div class="mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <label class="form-label fw-medium text-dark mb-0">Thư Viện Ảnh Phụ</label>
                          <label for="thu_vien_anh" class="btn btn-sm btn-primary py-1 px-2 mb-0 d-inline-flex align-items-center gap-1" style="cursor: pointer; box-shadow: none;">
                            <i class="bx bx-upload"></i> Chọn nhiều ảnh
                          </label>
                        </div>
                        <input type="file" id="thu_vien_anh" class="d-none" accept="image/*" multiple @change="handleGalleryUpload" />
                        
                        <!-- Existing Gallery -->
                        <div v-if="formData.thu_vien_anh && formData.thu_vien_anh.length > 0" class="mb-2">
                          <div v-for="(img, idx) in formData.thu_vien_anh" :key="'old_'+idx" class="d-flex gap-2 mb-2 align-items-center">
                            <div class="d-flex align-items-center justify-content-center border rounded bg-white flex-shrink-0" style="width: 44px; height: 44px; overflow: hidden;">
                              <img :src="getImageUrl(img)" style="width: 100%; height: 100%; object-fit: cover;" />
                            </div>
                            <a :href="getImageUrl(img)" target="_blank" class="text-primary text-decoration-none text-truncate flex-grow-1 fw-medium" style="font-size: 0.85rem;" title="Xem ảnh">{{ img }}</a>
                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger ms-auto" @click="formData.thu_vien_anh.splice(idx, 1)" title="Xóa"><i class="bx bx-trash"></i></button>
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
                  <div class="row mt-3">
                  <div class="col-md-12 mb-3">
                      <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-medium text-dark mb-0">Giá Bán (VNĐ) <span v-if="!formData.is_gia_lien_he" class="text-danger">*</span></label>
                        <div class="form-check mb-0 d-flex align-items-center gap-1">
                          <input class="form-check-input m-0" type="checkbox" id="is_gia_lien_he" v-model="formData.is_gia_lien_he" :true-value="true" :false-value="false" @change="onGiaLienHeChange">
                          <label class="form-check-label text-muted text-nowrap" for="is_gia_lien_he" style="font-size: 0.8rem; cursor: pointer;">Liên hệ</label>
                        </div>
                      </div>
                      <input type="number" class="form-control" v-model="formData.gia_ban" min="0" placeholder="Nhập giá bán..." :disabled="formData.is_gia_lien_he === true">
                    </div>
                </div>
                <div class="row">
                  <div class="col-md-12 mb-3">
                    <label class="form-label fw-medium text-dark">Trạng Thái <span class="text-danger">*</span></label>
                    <v-select v-model="formData.trang_thai" :options="[{value: 1, label: 'Hoạt Động'}, {value: 0, label: 'Tạm Khóa'}]" :can-clear="false" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer border-top py-3 mt-2 bg-light">
            <button type="button" class="btn btn-outline-secondary px-4 me-3" data-bs-dismiss="modal" @click="resetForm">Hủy</button>
            <button type="submit" class="btn btn-primary px-5 shadow-sm" :disabled="isSubmitting">
              <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
              {{ isEditMode ? 'Lưu Thay Đổi' : 'Thêm Sản Phẩm' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script>
import vSelect from '@vueform/multiselect';
import axiosClient from '../../../api/axiosClient';
import Swal from 'sweetalert2';
import { computed } from 'vue';
  import { useAuthStore } from '../../../stores/authStore';
  import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import { createToaster } from "@meforma/vue-toaster";

export default {
  name: "SanPham",
  components: {
    vSelect
  },
  setup() {
    const queryClient = useQueryClient();
      const authStore = useAuthStore();
    const toaster = createToaster({ position: "top-right", duration: 3000 });
    
    const { data: sanPhams, isPending: isPendingSanPhams, isFetching: isFetchingSanPhams } = useQuery({
      queryKey: ['san-pham'],
      queryFn: () => axiosClient.get('/admin/san-pham').then(res => res.data)
    });

    const { data: categories, isPending: isPendingCategories, isFetching: isFetchingCategories } = useQuery({
      queryKey: ['categories'],
      queryFn: () => axiosClient.get('/admin/danh-muc-san-pham').then(res => res.data)
    });

    const deleteMutation = useMutation({
      mutationFn: async (id) => await axiosClient.delete(`/admin/san-pham/${id}`),
      onError: (err, id, context) => {
        toaster.error(err.response?.data?.message || "Lỗi khi xóa sản phẩm");
      },
      onSuccess: () => toaster.success("Đã xóa sản phẩm!"),
      onSettled: () => queryClient.invalidateQueries({ queryKey: ['san-pham'] })
    });

    const saveMutation = useMutation({
      mutationFn: async ({ id, payload }) => {
        if (id) {
          payload.append('_method', 'PUT');
          return await axiosClient.post(`/admin/san-pham/${id}`, payload, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });
        }
        return await axiosClient.post('/admin/san-pham', payload, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
      },
      onMutate: async (vars) => { await queryClient.cancelQueries({ queryKey: ['san-pham'] }); const previousData = queryClient.getQueryData(['san-pham']); queryClient.setQueryData(['san-pham'], (old) => { const list = Array.isArray(old) ? old : (old?.data || []); const updatedData = vars.data || {}; if (vars.id) { return { data: list.map(item => item.id === vars.id ? { ...item, ...updatedData } : item) }; } return { data: [...list, { ...updatedData, id: Date.now() }] }; }); return { previousData }; },
      onError: (err, vars, context) => {
        toaster.error(err.response?.data?.message || "Lỗi khi lưu sản phẩm");
      },
      onSuccess: (data, vars) => {
        toaster.success(vars.id ? "Cập nhật sản phẩm thành công!" : "Đã thêm sản phẩm mới!");
      },
      onSettled: () => queryClient.invalidateQueries({ queryKey: ['san-pham'] })
    });

    return { 
      queryClient, sanPhams, isPendingSanPhams, isFetchingSanPhams, 
      categories, isPendingCategories, isFetchingCategories,
      deleteMutation, saveMutation, toaster
    };
  },
  data() {
    return {
      currentUserRole: JSON.parse(localStorage.getItem('auth_user') || '{}').roles?.[0]?.toUpperCase() || 'ADMIN',
      activeTab: 'MARINE',
      filters: {
        search: "",
        danh_muc_san_pham_id: null
      },
      isSubmitting: false,
      isEditMode: false,
      mainImagePreview: null,
      mainImageFile: null,
      galleryFiles: [],
      formData: {
        id: null,
        danh_muc_san_pham_id: null,
        ma_san_pham: "",
        ten_san_pham: "",
        slug: "",
        mo_ta_chi_tiet: "",
        thong_so_ky_thuat: "",
        gia_ban: null,
        is_gia_lien_he: false,
        so_luong_ton: 0,
        hinh_anh_chinh: "",
        thu_vien_anh: [],
        video_url: "",
        
        trang_thai: 1
      }
    };
  },
  computed: {
      isPending() {
        return this.isPendingCategories || this.isPendingSanPhams;
      },
    isFetching() {
      return this.isFetchingCategories || this.isFetchingSanPhams;
    },
    danhMucListOptions() {
      const all = this.categories || [];
      const buildTree = (parentId, prefix = '') => {
        let result = [];
        const children = all.filter(c => c.parent_id === parentId);
        for (const child of children) {
          result.push({ ...child, display_name: prefix + child.ten_danh_muc });
          result = result.concat(buildTree(child.id, prefix + '— '));
        }
        return result;
      };
      const tree = buildTree(null);
      
      return tree
        .filter(dm => dm.trang_thai === 1 || dm.id === this.formData.danh_muc_san_pham_id || dm.id === this.filterCategoryId)
        .map(dm => ({ value: dm.id, label: dm.display_name }));
    },
    filteredSanPhams() {
      return (this.sanPhams || []).filter(item => {
        let match = true;

        if (this.filters.search) {
          const s = this.filters.search.toLowerCase();
          match = match && (
            (item.ten_san_pham && item.ten_san_pham.toLowerCase().includes(s)) || 
            (item.ma_san_pham && item.ma_san_pham.toLowerCase().includes(s))
          );
        }
        if (this.filters.danh_muc_san_pham_id) {
          match = match && item.danh_muc_san_pham_id === this.filters.danh_muc_san_pham_id;
        }
        return match;
      });
    }
  },
  methods: {
    switchTab(tab) {
      this.activeTab = tab;
      this.resetFilters();
      this.resetForm();
    },
    handleMainImageUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.mainImageFile = file;
        this.mainImagePreview = URL.createObjectURL(file);
      } else {
        this.mainImageFile = null;
        this.mainImagePreview = null;
      }
    },
    handleGalleryUpload(event) {
      const files = event.target.files;
      if (files && files.length > 0) {
        for (let i = 0; i < files.length; i++) {
          this.galleryFiles.push(files[i]);
        }
      }
      event.target.value = '';
    },
    generateSlug() {
      if (this.formData.ten_san_pham && !this.isEditMode) {
        this.formData.slug = this.formData.ten_san_pham
          .toLowerCase()
          .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
          .replace(/[^a-z0-9\s-]/g, "")
          .replace(/\s+/g, "-")
          .replace(/-+/g, "-");
      }
    },
    formatCurrency(value) {
      if (!value) return "0 đ";
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
    },
    getImageUrl(path) {
      if (!path) return '';
      if (path.startsWith('blob:') || path.startsWith('http')) return path;
      return `http://localhost:8000${path}`;
    },
    getBlobUrl(file) {
      if (!file) return '';
      return URL.createObjectURL(file);
    },
    onGiaLienHeChange() {
      if (this.formData.is_gia_lien_he) {
        this.formData.gia_ban = 0;
      } else {
        this.formData.gia_ban = null;
      }
    },
    resetFilters() {
      this.filters = {
        search: "",
        danh_muc_san_pham_id: null
      };
    },
    getDanhMucName(id) {
      const dm = (this.categories || []).find(d => d.id === id);
      return dm ? dm.ten_danh_muc : 'N/A';
    },
    removeMainImage() {
        this.mainImageFile = null;
        this.mainImagePreview = null;
        this.formData.hinh_anh_chinh = "";
        const f1 = document.getElementById('hinh_anh_chinh');
        if (f1) f1.value = '';
      },
      resetForm() {
      this.isEditMode = false;
      this.mainImagePreview = null;
      this.mainImageFile = null;
      this.galleryFiles = [];
      const f1 = document.getElementById('hinh_anh_chinh');
      if (f1) f1.value = '';
      const f2 = document.getElementById('thu_vien_anh');
      if (f2) f2.value = '';
      this.formData = {
        id: null,
        danh_muc_san_pham_id: null,
        ma_san_pham: "",
        ten_san_pham: "",
        slug: "",
        mo_ta_chi_tiet: "",
        thong_so_ky_thuat: "",
        gia_ban: null,
        is_gia_lien_he: false,
        so_luong_ton: 0,
        hinh_anh_chinh: "",
        thu_vien_anh: [],
        video_url: "",
        
        trang_thai: 1
      };
    },
    openAddModal() {
      this.resetForm();
    },
    openEditModal(item) {
      this.isEditMode = true;
      this.formData = { ...item };
      
      try {
        let tva = item.thu_vien_anh;
        if (typeof tva === 'string') tva = JSON.parse(tva);
        this.formData.thu_vien_anh = Array.isArray(tva) ? [...tva] : [];
      } catch(e) {
        this.formData.thu_vien_anh = [];
      }

      this.formData.is_gia_lien_he = item.is_gia_lien_he === 1 || item.is_gia_lien_he === true || item.gia_ban === 0 || item.gia_ban === "0.00" || item.gia_ban === "0";
      if (this.formData.is_gia_lien_he) {
        this.formData.gia_ban = 0;
      } else {
        this.formData.gia_ban = item.gia_ban ? Number(item.gia_ban) : null;
      }
      this.mainImagePreview = null;
      this.mainImageFile = null;
      this.galleryFiles = [];
      const f1 = document.getElementById('hinh_anh_chinh');
      if (f1) f1.value = '';
      const f2 = document.getElementById('thu_vien_anh');
      if (f2) f2.value = '';
    },
    async saveData() {
      if (!this.formData.ma_san_pham || !this.formData.ten_san_pham || !this.formData.danh_muc_san_pham_id) {
        this.$toast?.error("Vui lòng nhập đầy đủ thông tin bắt buộc!");
        return;
      }

      if (!this.formData.is_gia_lien_he) {
        if (this.formData.gia_ban === null || this.formData.gia_ban === undefined || this.formData.gia_ban === "" || Number(this.formData.gia_ban) <= 0) {
          this.$toast?.error("Vui lòng nhập giá bán lớn hơn 0 hoặc tích chọn 'Liên hệ'!");
          return;
        }
      }

      this.isSubmitting = true;
      try {
        const payload = new FormData();
        payload.append('danh_muc_san_pham_id', this.formData.danh_muc_san_pham_id);
        payload.append('ma_san_pham', this.formData.ma_san_pham);
        payload.append('ten_san_pham', this.formData.ten_san_pham);
        payload.append('slug', this.formData.slug);
        if (this.formData.mo_ta_chi_tiet) payload.append('mo_ta_chi_tiet', this.formData.mo_ta_chi_tiet);
        if (this.formData.thong_so_ky_thuat) payload.append('thong_so_ky_thuat', this.formData.thong_so_ky_thuat);
        if (this.formData.video_url) payload.append('video_url', this.formData.video_url);
        payload.append('gia_ban', this.formData.gia_ban);
        payload.append('is_gia_lien_he', this.formData.is_gia_lien_he ? 1 : 0);
        payload.append('so_luong_ton', this.formData.so_luong_ton);
        
        payload.append('trang_thai', this.formData.trang_thai);

        if (this.mainImageFile) {
          payload.append('hinh_anh_chinh_file', this.mainImageFile);
        }
        
        payload.append('thu_vien_anh_kept', JSON.stringify(this.formData.thu_vien_anh || []));
        
        this.galleryFiles.forEach((file) => {
          payload.append('thu_vien_anh_files[]', file);
        });

        
        const id = this.isEditMode ? this.formData.id : null;
        await this.saveMutation.mutateAsync({ id, payload, data: { ...this.formData } });
        
        document.getElementById('btnCloseModal')?.click();
        this.resetForm();

      } catch (error) {
        const msg = error.response?.data?.message || "Có lỗi xảy ra khi lưu dữ liệu";
        this.$toast?.error(msg);
      } finally {
        this.isSubmitting = false;
      }
    },
    async confirmDelete(item) {
      const result = await Swal.fire({
        title: 'Xác nhận xóa',
        html: `Bạn có chắc chắn muốn xóa sản phẩm <b>"${item.ten_san_pham}"</b>?<br><br><small class="text-danger">CẢNH BÁO: Dữ liệu liên kết có thể bị ảnh hưởng.</small>` ,
        icon: 'warning',
          iconColor: '#ff3e1d',
        showCancelButton: true,
        confirmButtonText: 'Xóa Sản Phẩm',
        cancelButtonText: 'Hủy',
        reverseButtons: true,
        animation: false,
        customClass: {
            popup: 'swal-shift-up',
            confirmButton: 'btn btn-danger px-4 shadow-sm',
            cancelButton: 'btn btn-outline-secondary px-4 me-3'
          },
        buttonsStyling: false
      });

      if (result.isConfirmed) {
        
        try {
          await this.deleteMutation.mutateAsync(item.id);
        } catch (error) {
          // Error is handled by mutation onError
        }
      }
    }
  }
};
</script>



<style scoped>
.custom-line-tab {
  cursor: pointer;
  min-width: 160px;
  text-align: center;
  color: #a1acb8; /* text-muted */
  border-bottom: 3px solid transparent;
  transition: all 0.2s ease;
}
.custom-line-tab.active {
  color: var(--bs-primary);
  border-bottom: 3px solid var(--bs-primary);
}
.custom-line-tab:hover:not(.active) {
  color: #697a8d;
}
</style>














<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Động cơ & Phương tiện /</span> Sơ Đồ Phụ Tùng 
    </h4>

    <!-- Card danh sách -->
    <div class="card">
      <div class="card-header border-bottom">
        <h5 class="mb-0 fw-bold text-dark">Danh sách Sơ Đồ Phụ Tùng</h5>
      </div>
      
      <!-- Bộ lọc (Filters) -->
      <div class="card-body pt-3 pb-3">
        <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
          <div class="flex-grow-1" style="min-width: 200px;">
            <input type="text" class="form-control" placeholder="Tìm kiếm sơ đồ..." v-model="filters.search">
          </div>
          <div style="width: 200px;">
            <v-select
              v-model="filterThuongHieuId"
              :options="[{value: null, label: 'Thương Hiệu'}, ...brandList]"
              placeholder="Thương Hiệu"
              :searchable="true"
              :can-clear="false"
            />
          </div>
          <div style="width: 200px;">
            <v-select
              v-model="filterLoaiMayId"
              :options="[{value: null, label: 'Loại Động Cơ'}, ...loaiMayList]"
              placeholder="Loại Động Cơ"
              :searchable="true"
              :can-clear="false"
            />
          </div>
          <div style="width: 200px;">
            <v-select
              v-model="filterNhomId"
              :options="[{value: null, label: 'Nhóm Phụ Tùng'}, ...nhomPhuTungList]"
              placeholder="Nhóm Phụ Tùng"
              :searchable="true"
              :can-clear="false"
            />
          </div>
          <button class="btn btn-secondary text-nowrap" @click="resetFilters">
            Đặt lại
          </button>
          <button v-if="canAdd" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#modalSoDo" @click="openAddModal">
            <i class="bx bx-plus me-1"></i> Thêm Mới
          </button>
        </div>
      </div>

      <div class="table-responsive text-nowrap custom-scroll" style="max-height: calc(100vh - 260px); min-height: 400px; overflow-y: auto;">
        <table class="table table-hover mb-0">
          <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
            <tr>
              <th class="text-center" style="width: 5%">STT</th>
              <th class="text-center" style="width: 10%">Hình Sơ Đồ</th>
              <th :style="{ width: canManage ? '25%' : '35%' }">Nhóm Phụ Tùng</th>
              <th style="width: 25%">Model Động Cơ</th>
                <th style="width: 15%">Thương Hiệu / Loại</th>
              <th class="text-center" style="width: 10%" v-if="canManage">Trạng thái</th>
              <th class="text-center" :style="{ width: canManage ? '10%' : '15%' }" v-if="canManage">Hành động</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr v-if="filteredDiagrams.length === 0">
              <td :colspan="canManage ? 7 : 5" class="text-center py-5 text-muted">
                <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                Không có dữ liệu
              </td>
            </tr>
            <tr v-for="(item, index) in filteredDiagrams" :key="item.id">
              <td class="text-center align-middle"><strong>{{ index + 1 }}</strong></td>
              
              <td class="text-center align-middle">
                <div class="d-inline-flex justify-content-center align-items-center border rounded" style="width: 60px; height: 60px; background-color: #fff;">
                  <img 
                    v-if="item.hinh_anh" 
                    :src="getImageUrl(item.hinh_anh)" 
                    alt="Diagram" 
                    style="max-height: 40px; max-width: 50px; object-fit: contain;" 
                    @error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='inline-block'"
                  >
                  <i class="bx bx-image text-muted fs-3" :style="{ display: item.hinh_anh ? 'none' : 'inline-block' }"></i>
                </div>
              </td>

              <td class="align-middle">
                <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ item.nhom_phu_tung?.ten_nhom || getNhomName(item.nhom_phu_tung_id) }}</span>
              </td>
              
              <td class="align-middle" :title="getFullModelName(item.may_thuy_model_id)">
                  <span class="fw-semibold text-secondary">{{ getFullModelName(item.may_thuy_model_id) }}</span>
                </td>
                
                <td class="align-middle">
                  <div class="d-flex flex-column">
                    <span class="fw-bold text-primary" style="font-size: 0.85rem; text-transform: uppercase;">{{ getModelBrand(item.may_thuy_model_id) }}</span>
                    <span class="fw-semibold text-dark" style="font-size: 0.8rem;">{{ getModelType(item.may_thuy_model_id) }}</span>
                  </div>
                </td>

              <td class="text-center align-middle" v-if="canManage">
                <span v-if="item.trang_thai === 1" class="badge badge-custom bg-pastel-success">HOẠT ĐỘNG</span>
                <span v-else class="badge badge-custom bg-pastel-danger">TẠM KHÓA</span>
              </td>
              <td class="text-center align-middle" v-if="canManage">
                <div class="d-flex align-items-center justify-content-center">
                  <button 
                    v-if="canEdit"
                    type="button" 
                    class="action-btn text-primary me-1" 
                    @click="openEditModal(item)"
                    data-bs-toggle="modal" 
                    data-bs-target="#modalSoDo"
                    title="Sửa"
                  >
                    <i class="bx bx-edit-alt"></i>
                  </button>
                  <button 
                    v-if="canDelete"
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
          <span class="text-muted fw-medium">Tổng cộng: {{ filteredDiagrams.length }} mục</span>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modalSoDo" tabindex="-1" aria-hidden="true" ref="modalSoDo">
      <div class="modal-dialog modal-xl mt-5" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-bottom pb-3">
            <h4 class="modal-title fw-bold text-dark mb-0 fs-4" id="modalSoDoTitle">
              {{ isEditMode ? 'Cập Nhật Sơ Đồ' : 'Thêm Mới Sơ Đồ' }}
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="saveData">
            <div class="modal-body pb-2">
              <div class="row">
                <!-- Cột trái: Thông tin cơ bản -->
                <div class="col-lg-4 border-end pe-4">
                  
                  <!-- Chọn Model -->
                  <div class="mb-4">
                    <label for="model_dong_co" class="form-label fw-medium text-dark">Model Động Cơ <span class="text-danger">*</span></label>
                    <v-select
                      v-model="formData.may_thuy_model_id"
                      :options="models"
                      placeholder="-- Chọn Model --"
                      :searchable="true"
                      :can-clear="false"
                      required
                    />
                  </div>

                  <!-- Chọn Nhóm Phụ Tùng -->
                  <div class="mb-4">
                    <label for="nhom_phu_tung" class="form-label fw-medium text-dark">Nhóm Phụ Tùng <span class="text-danger">*</span></label>
                    <v-select
                      v-model="formData.nhom_phu_tung_id"
                      :options="nhomPhuTungList"
                      placeholder="-- Chọn Nhóm Phụ Tùng --"
                      :searchable="true"
                      :can-clear="false"
                      required
                    />
                  </div>

                  <!-- Hình Ảnh Sơ Đồ -->
                  <div class="mb-4">
                    <label for="hinh_anh" class="form-label fw-medium text-dark">Hình Ảnh (Bản vẽ nổ) <span class="text-danger">*</span></label>
                    <input
                      type="file"
                      id="hinh_anh"
                      class="form-control mb-2"
                      accept="image/*"
                      @change="handleImageUpload"
                    />
                    <div class="d-flex justify-content-center align-items-center border rounded bg-light mt-2 position-relative" style="height: 180px;">
                      <img v-if="formData.hinh_anh" :src="getImageUrl(formData.hinh_anh)" style="max-height: 160px; max-width: 100%; object-fit: contain;">
                      <button v-if="formData.hinh_anh" type="button" class="btn btn-icon btn-sm bg-white text-danger position-absolute top-0 end-0 m-2 rounded-circle shadow-sm border" @click="removeImage" style="width: 28px; height: 28px; padding: 0;" title="Xóa ảnh">
                        <i class="bx bx-trash"></i>
                      </button>
                      <span class="text-muted" v-else><i class='bx bx-image fs-1'></i></span>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-6 mb-4">
                      <label for="thu_tu" class="form-label fw-medium text-dark">Thứ tự hiển thị</label>
                      <input
                        type="number"
                        id="thu_tu"
                        class="form-control"
                        v-model="formData.thu_tu"
                        min="1"
                      />
                    </div>
                    <div class="col-md-6 mb-4">
                      <label for="status" class="form-label fw-medium text-dark">Trạng Thái <span class="text-danger">*</span></label>
                      <v-select
                        v-model="formData.trang_thai"
                        :options="[{value: 1, label: 'Hoạt Động'}, {value: 0, label: 'Tạm Khóa'}]"
                        :can-clear="false"
                      />
                    </div>
                  </div>
                </div>
                
                <!-- Cột phải: Ráp nối phụ tùng -->
                <div class="col-lg-8 ps-4 d-flex flex-column">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold text-dark fs-6">Bảng Ráp Nối Phụ Tùng (Chi Tiết Sơ Đồ)</h5>
                    <button type="button" class="btn btn-sm btn-primary px-3 shadow-sm" @click="addChiTiet">
                      <i class="bx bx-plus me-1"></i> Thêm dòng
                    </button>
                  </div>
                  
                  <div class="table-responsive custom-scroll border rounded flex-grow-1 bg-white" style="max-height: 550px; overflow-y: auto; padding: 1px;">
                    <table class="table table-hover mb-0">
                      <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
                        <tr>
                          <th class="text-center bg-white text-nowrap" style="width: 14%; position: sticky; top: 0; z-index: 2; box-shadow: inset 0 -1px 0 #dee2e6;">STT (REF)</th>
                          <th class="bg-white text-nowrap" style="width: 60%; position: sticky; top: 0; z-index: 2; box-shadow: inset 0 -1px 0 #dee2e6;">Mã Phụ Tùng (SKU)</th>
                          <th class="text-center bg-white text-nowrap" style="width: 14%; position: sticky; top: 0; z-index: 2; box-shadow: inset 0 -1px 0 #dee2e6;">Số Lượng</th>
                          <th class="text-center bg-white text-nowrap" style="width: 12%; position: sticky; top: 0; z-index: 2; box-shadow: inset 0 -1px 0 #dee2e6;">Xóa</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-if="formData.chiTiet && formData.chiTiet.length === 0">
                          <td colspan="4" class="text-center text-muted py-4">
                            <i class="bx bx-folder-open text-secondary fs-2 d-block mb-1"></i>
                            Không có dữ liệu
                          </td>
                        </tr>
                        <tr v-for="(item, index) in formData.chiTiet" :key="index">
                          <td class="align-middle px-2">
                            <input type="text" class="form-control form-control-sm text-center" v-model="item.ref_number" placeholder="VD: 5" required>
                          </td>
                          <td class="align-middle px-2" style="position: relative;">
                            <v-select
                              v-model="item.phu_tung_id"
                              :options="phuTungList"
                              placeholder="Gõ mã SKU..."
                              :searchable="true"
                              :can-clear="false"
                              required
                            />
                          </td>
                          <td class="align-middle px-2">
                            <input type="number" class="form-control form-control-sm text-center" v-model="item.qty" min="1" required>
                          </td>
                          <td class="text-center align-middle px-2">
                            <button type="button" class="action-btn text-danger" @click="removeChiTiet(index)" title="Xóa">
                              <i class="bx bx-trash"></i>
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer pt-3 border-top">
              <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Hủy</button>
              <button type="submit" class="btn btn-primary px-4 shadow-sm" :disabled="isSubmitting"><span v-if="isSubmitting" class="spinner-border spinner-border-sm me-1"></span>{{ isEditMode ? 'Cập Nhật' : 'Thêm Mới' }}</button>
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
import Swal from 'sweetalert2';
import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query';
import { useAuthStore } from '../../../stores/authStore';

export default {
  name: "SoDoPhuTung",
  components: {
    vSelect
  },
  data() {
    return {
      activeTab: 'MARINE',
      currentUserRole: JSON.parse(localStorage.getItem('auth_user') || '{}').roles?.[0]?.toUpperCase() || 'ADMIN',
      filterThuongHieuId: null,
      filterLoaiMayId: null,
      filterModelId: null,
      filterNhomId: null,
      filters: { search: "" },
      isEditMode: false,
      isSubmitting: false,
      imageFile: null,
      queryClient: useQueryClient(),

      formData: {
        id: null,
        may_thuy_model_id: null,
        nhom_phu_tung_id: null,
        hinh_anh: "",
        thu_tu: 1,
        trang_thai: 1,
        chiTiet: []
      }
    };
  },
  mounted() {
    // Smart Routing Workflow: Pre-select Model from query params
    if (this.$route.query.tab) {
      this.activeTab = this.$route.query.tab;
    }
    if (this.$route.query.model_id) {
      const parsedId = parseInt(this.$route.query.model_id);
      this.filterModelId = parsedId;
    }
  },
  computed: {
    canAdd() {
      return this.authStore?.isAdmin || this.authStore?.hasPermission('Thêm Phụ tùng');
    },
    canEdit() {
      return this.authStore?.isAdmin || this.authStore?.hasPermission('Sửa Phụ tùng');
    },
    canDelete() {
      return this.authStore?.isAdmin || this.authStore?.hasPermission('Xóa Phụ tùng');
    },
    canManage() {
      return this.canEdit || this.canDelete;
    },
    isGlobalFetching() {
      return this.isFetchingBrands || this.isFetchingTypes || this.isFetchingModels || this.isFetchingNhom || this.isFetchingPhuTungs || this.isFetchingDiagrams;
    },
    
    // Mapped computed properties for UI compatibility
    models() {
      const items = Array.isArray(this.engineModels) ? this.engineModels : (this.engineModels?.data || []);
      return items
        .filter(m => m.trang_thai === 1 || m.id === this.formData.may_thuy_model_id || m.id === this.filterModelId)
        .map(m => ({
          value: m.id,
          label: `${m.ten_model} - ${m.ma_model}`,
          code: m.ma_model,
            ten_model: m.ten_model,
          thuong_hieu_id: m.thuong_hieu_id,
          loai_may_id: m.loai_dong_co_id
        }));
    },
    brandList() {
      const items = Array.isArray(this.brands) ? this.brands : (this.brands?.data || []);
      return items
        
        .map(b => ({
          value: b.id,
          label: b.ten_thuong_hieu || b.name
        }));
    },
    loaiMayList() {
      const items = Array.isArray(this.equipmentTypes) ? this.equipmentTypes : (this.equipmentTypes?.data || []);
      return items
        
        .map(lm => ({
          value: lm.id,
          label: lm.ten_loai || lm.ten_loai_dong_co
        }));
    },
    nhomPhuTungList() {
      const items = Array.isArray(this.nhomPhuTungs) ? this.nhomPhuTungs : (this.nhomPhuTungs?.data || []);
      return items
        .filter(n => n.trang_thai === 1 || n.id === this.formData.nhom_phu_tung_id || n.id === this.filterNhomId)
        .map(n => ({
          value: n.id,
          label: n.ten_nhom
        }));
    },
    phuTungList() {
      const items = Array.isArray(this.phuTungs) ? this.phuTungs : (this.phuTungs?.data || []);
      return items
        .filter(p => p.trang_thai === 1 || this.formData.chiTiet.some(ct => ct.phu_tung_id === p.id))
        .map(p => ({
          value: p.id,
          label: `${p.ma_phu_tung} - ${p.ten_phu_tung}`
        }));
    },
    filteredModelsList() {
      let res = this.models;
      if (this.filterThuongHieuId) res = res.filter(m => m.thuong_hieu_id === this.filterThuongHieuId);
      if (this.filterLoaiMayId) res = res.filter(m => m.loai_may_id === this.filterLoaiMayId);
      return res;
    },
    filteredDiagrams() {
      const items = Array.isArray(this.diagrams) ? this.diagrams : (this.diagrams?.data || []);
      // Only keep diagrams whose engine model belongs to the current active ecosystem
      let result = items.filter(d => 
        this.models.some(m => m.value === d.may_thuy_model_id)
      );

      if (this.filterThuongHieuId || this.filterLoaiMayId) {
        result = result.filter(d => {
          const model = this.models.find(m => m.value === d.may_thuy_model_id);
          if (!model) return false;
          let match = true;
          if (this.filterThuongHieuId && model.thuong_hieu_id !== this.filterThuongHieuId) match = false;
          if (this.filterLoaiMayId && model.loai_may_id !== this.filterLoaiMayId) match = false;
          return match;
        });
      }
      if (this.filterModelId) {
        result = result.filter(d => d.may_thuy_model_id === this.filterModelId);
      }
      if (this.filterNhomId) {
        result = result.filter(d => d.nhom_phu_tung_id === this.filterNhomId);
      }
      if (this.filters.search) {
        const keyword = this.filters.search.toLowerCase();
        result = result.filter(d => {
          const modelName = this.getModelName(d.may_thuy_model_id).toLowerCase();
          const nhomName = (d.nhom_phu_tung?.ten_nhom || this.getNhomName(d.nhom_phu_tung_id)).toLowerCase();
          return modelName.includes(keyword) || nhomName.includes(keyword);
        });
      }
      // Sắp xếp theo thứ tự hiển thị
      return result.sort((a, b) => a.thu_tu - b.thu_tu);
    }
  },
  setup() {
    const queryClient = useQueryClient();
    const authStore = useAuthStore();
    
    const fetchApi = async (url) => (await axiosClient.get(url)).data;
    
    const { data: brands, isFetching: isFetchingBrands } = useQuery({ queryKey: ['brands'], queryFn: () => fetchApi('/admin/thuong-hieu') });
    const { data: equipmentTypes, isFetching: isFetchingTypes } = useQuery({ queryKey: ['engineTypes'], queryFn: () => fetchApi('/admin/loai-dong-co') });
    const { data: engineModels, isFetching: isFetchingModels } = useQuery({ queryKey: ['engineModels'], queryFn: () => fetchApi('/admin/dong-co-model') });
    const { data: nhomPhuTungs, isFetching: isFetchingNhom } = useQuery({ queryKey: ['nhom-phu-tung'], queryFn: () => fetchApi('/admin/nhom-phu-tung?all=1') });
    const { data: phuTungs, isFetching: isFetchingPhuTungs } = useQuery({ queryKey: ['phu-tung'], queryFn: () => fetchApi('/admin/phu-tung') });
    const { data: diagrams, isFetching: isFetchingDiagrams } = useQuery({ queryKey: ['so-do'], queryFn: () => fetchApi('/admin/so-do') });
    
    
    const toaster = window.toaster || { success: () => {}, error: () => {} };

    const deleteMutation = useMutation({
      mutationFn: async (id) => {
        return await axiosClient.delete(`/admin/so-do/${id}`);
      },
      onError: (err, id, context) => {
        toaster.error(err.response?.data?.message || "Lỗi khi xóa sơ đồ!");
      },
      onSuccess: () => {
        toaster.success("Đã xóa sơ đồ thành công!");
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ['so-do'] });
      }
    });

    const saveMutation = useMutation({
      mutationFn: async ({ isEdit, id, data }) => {
        if (isEdit) {
          data.append('_method', 'PUT');
          return await axiosClient.post(`/admin/so-do/${id}`, data, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });
        } else {
          return await axiosClient.post('/admin/so-do', data, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });
        }
      },
      onError: (err, variables, context) => {
        toaster.error(err.response?.data?.message || "Lỗi lưu dữ liệu sơ đồ!");
      },
      onSettled: () => {
        queryClient.invalidateQueries({ queryKey: ['so-do'] });
      }
    });
    return { authStore, queryClient, brands, isFetchingBrands, equipmentTypes, isFetchingTypes, engineModels, isFetchingModels, nhomPhuTungs, isFetchingNhom, phuTungs, isFetchingPhuTungs, diagrams, isFetchingDiagrams , deleteMutation, saveMutation };
  },
  methods: {
    getImageUrl(path) {
      if (!path) return '';
      if (path.startsWith('blob:') || path.startsWith('http')) return path;
      if (path.startsWith('/client/image/')) return path;
      return `http://localhost:8000/storage/${path.replace(/^\/?storage\//, '')}`;
    },
    handleImageUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.imageFile = file;
        this.formData.hinh_anh = URL.createObjectURL(file);
      } else {
        this.imageFile = null;
      }
    },
    removeImage() {
      this.imageFile = null;
      this.formData.hinh_anh = "";
      const fileInput = document.getElementById('hinh_anh');
      if (fileInput) fileInput.value = '';
    },
    switchTab(tab) {
      this.activeTab = tab;
    },
    resetFilters() {
      this.filters.search = "";
      this.filterThuongHieuId = null;
      this.filterLoaiMayId = null;
      this.filterModelId = null;
      this.filterNhomId = null;
    },
    getModelName(id) {
        const model = this.models.find(m => m.value === id);
        return model ? model.code : 'N/A';
      },
      getFullModelName(id) {
        const model = this.models.find(m => m.value === id);
        return model ? (model.ten_model || '') : '';
      },
    getModelBrand(id) {
      const m = this.models.find(x => x.value === id);
      if(!m) return '';
      const b = this.brandList.find(x => x.value === m.thuong_hieu_id);
      return b ? b.label : '';
    },
    getModelType(id) {
      const m = this.models.find(x => x.value === id);
      if(!m) return 'N/A';
      const type = this.loaiMayList.find(x => x.value === m.loai_may_id);
      return type ? type.label : 'N/A';
    },
    getNhomName(id) {
      const nhom = this.nhomPhuTungList.find(n => n.value === id);
      return nhom ? nhom.label : 'N/A';
    },
    openAddModal() {
      this.isEditMode = false;
      this.imageFile = null;
      this.formData = {
        id: null,
        may_thuy_model_id: this.filterModelId || null,
        nhom_phu_tung_id: this.filterNhomId || null,
        hinh_anh: "",
        ten_so_do: "",
        thu_tu: 1,
        trang_thai: 1,
        chiTiet: []
      };
    },
    async openEditModal(item) {
      this.isEditMode = true;
      this.imageFile = null;
      this.formData = { ...item };
      this.formData.nhom_phu_tung_id = parseInt(item.nhom_phu_tung_id) || null;
      this.formData.may_thuy_model_id = parseInt(item.may_thuy_model_id) || null;
      this.formData.chiTiet = [];
      
      try {
        const res = await axiosClient.get(`/admin/so-do/${item.id}`);
        if (res.success && res.data && res.data.chi_tiets) {
          this.formData.chiTiet = res.data.chi_tiets.map(ct => ({
            id: ct.id,
            ref_number: ct.ref_number,
            phu_tung_id: ct.phu_tung_id,
            qty: ct.qty || 1,
            x: ct.x,
            y: ct.y,
            width: ct.width,
            height: ct.height
          }));
        }
      } catch (error) {
        console.error("Lỗi khi tải chi tiết sơ đồ", error);
      }
    },
    addChiTiet() {
      this.formData.chiTiet.push({
        ref_number: "",
        phu_tung_id: null,
        qty: 1
      });
    },
    removeChiTiet(index) {
      this.formData.chiTiet.splice(index, 1);
    },
    async saveData() {
      if (!this.formData.may_thuy_model_id || !this.formData.nhom_phu_tung_id) {
        this.$toast?.error("Vui lòng nhập đầy đủ thông tin bắt buộc!");
        return;
      }

      this.isSubmitting = true;

      try {
        const formDataToSend = new FormData();
        
        let autoName = "Sơ Đồ Phụ Tùng";
        const m = this.models.find(x => x.value === this.formData.may_thuy_model_id);
        const n = this.nhomPhuTungList.find(x => x.value === this.formData.nhom_phu_tung_id);
        if (m && n) autoName = `${n.label} - ${m.code}`;
        
        formDataToSend.append('ten_so_do', this.formData.ten_so_do || autoName);
        formDataToSend.append('may_thuy_model_id', this.formData.may_thuy_model_id);
        formDataToSend.append('nhom_phu_tung_id', this.formData.nhom_phu_tung_id);
        formDataToSend.append('thu_tu', this.formData.thu_tu || 1);
        formDataToSend.append('trang_thai', this.formData.trang_thai);
        formDataToSend.append('is_update_chi_tiet', '1');
        
        if (this.imageFile) {
          formDataToSend.append('hinh_anh_file', this.imageFile);
        } else if (this.isEditMode && !this.formData.hinh_anh) {
          formDataToSend.append('hinh_anh', '');
        }

        if (this.formData.chiTiet && this.formData.chiTiet.length > 0) {
          this.formData.chiTiet.forEach((ct, index) => {
            if (ct.phu_tung_id) formDataToSend.append(`chiTiet[${index}][phu_tung_id]`, ct.phu_tung_id);
            if (ct.ref_number) formDataToSend.append(`chiTiet[${index}][ref_number]`, ct.ref_number);
            formDataToSend.append(`chiTiet[${index}][qty]`, ct.qty || 1);
          });
        }

        await this.saveMutation.mutateAsync({
          isEdit: this.isEditMode,
          id: this.formData.id,
          data: formDataToSend
        });
        this.$toast?.success(this.isEditMode ? "Cập nhật sơ đồ thành công!" : "Đã tạo sơ đồ mới!");
        
        const closeBtn = document.querySelector("#modalSoDo .btn-close");
        if (closeBtn) {
           closeBtn.click();
        }
      } catch (error) {
        this.$toast?.error(error.response?.data?.message || "Lỗi lưu dữ liệu sơ đồ!");
      } finally {
        this.isSubmitting = false;
      }
    },
    async confirmDelete(item) {
      const result = await Swal.fire({
        title: 'Xác nhận xóa',
        html: `Bạn có chắc chắn muốn xóa sơ đồ này?`,
        icon: 'warning',
        iconColor: '#ff3e1d',
        showCancelButton: true,
        confirmButtonText: 'Xóa sơ đồ',
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
        if (this.isEditMode && this.formData.id === item.id) { this.resetForm(); }
      this.deleteMutation.mutate(item.id);
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

/* Đẩy thanh cuộn xuống dưới thanh header của bảng */
.custom-scroll::-webkit-scrollbar-track {
  margin-top: 42px; /* Chiều cao xấp xỉ của thẻ th */
}
</style>





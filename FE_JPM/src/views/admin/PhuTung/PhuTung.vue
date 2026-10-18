<template>
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Quản Lý Phụ Tùng /</span> Danh Sách Phụ
      Tùng
    </h4>

    <!-- Card danh sách -->
    <div class="card">
      <div class="card-header border-bottom">
        <h5 class="mb-0 fw-bold text-dark">Danh Sách Phụ Tùng</h5>
      </div>

      <!-- Bộ lọc (Filters) -->
      <div class="card-body pt-3 pb-3">
        <div class="d-flex align-items-center gap-2">
          <div class="flex-grow-1">
            <input
              type="text"
              class="form-control"
              placeholder="Tìm Mã PT hoặc Tên..."
              v-model="searchKeyword"
            />
          </div>
          <div style="width: 25%">
            <v-select
              v-model="filterThuongHieuId"
              :options="[
                { value: null, label: 'Tất cả Thương Hiệu' },
                ...thuongHieuList,
              ]"
              placeholder="Thương Hiệu"
              :searchable="true"
              :can-clear="false"
            />
          </div>
          <div style="width: 25%">
            <v-select
              v-model="filterNhomId"
              :options="[
                { value: null, label: 'Tất cả Nhóm' },
                ...nhomPhuTungList,
              ]"
              placeholder="Nhóm Phụ Tùng"
              :searchable="true"
              :can-clear="false"
            />
          </div>

          <button class="btn btn-secondary text-nowrap" @click="resetFilters">
            Đặt lại
          </button>
          <button
            v-if="canAdd"
            class="btn btn-primary text-nowrap"
            data-bs-toggle="modal"
            data-bs-target="#modalPhuTung"
            @click="openAddModal"
          >
            <i class="bx bx-plus me-1"></i> Thêm Mới
          </button>
        </div>
      </div>

      <div
        class="table-responsive text-nowrap custom-scroll"
        style="height: calc(100vh - 230px); min-height: 400px; overflow-y: auto"
      >
        <table class="table table-hover">
          <thead style="position: sticky; top: 0; z-index: 1; background: #fff;">
            <tr>
              <th class="text-center" style="width: 5%">STT</th>
              <th class="text-center" style="width: 8%">Hình ảnh</th>
              <th style="width: 15%">Mã Phụ Tùng (SKU)</th>
              <th style="width: 25%">Tên Phụ Tùng</th>
              <th style="width: 15%">Thương Hiệu</th>
              <th style="width: 15%">Nhóm Phụ Tùng</th>
              <th class="text-end" style="width: 10%">Giá Bán</th>
              <th class="text-center" style="width: 5%">Trạng thái</th>
              <th
                class="text-center"
                style="width: 10%"
                v-if="canManage"
              >
                Hành động
              </th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr v-if="isPending">
              <td
                :colspan="canManage ? 9 : 8"
                class="text-center py-5"
              >
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </td>
            </tr>
            <tr v-else-if="filteredPhuTung.length === 0">
              <td
                :colspan="canManage ? 9 : 8"
                class="text-center py-5 text-muted"
              >
                <i class="bx bx-folder-open text-secondary fs-1 d-block mb-2"></i>
                Không có dữ liệu
              </td>
            </tr>
            <tr v-for="(item, index) in filteredPhuTung" :key="item.id" v-else>
              <td class="text-center align-middle">
                <strong>{{ index + 1 }}</strong>
              </td>

              <td class="text-center align-middle">
                <div
                  class="d-inline-flex justify-content-center align-items-center border rounded bg-white"
                  style="width: 60px; height: 60px"
                >
                  <img
                    v-if="item.hinh_anh"
                    :src="getImageUrl(item.hinh_anh)"
                    alt="Part"
                    style="
                      max-height: 40px;
                      max-width: 50px;
                      object-fit: contain;
                    "
                  />
                  <i v-else class="bx bx-image text-muted fs-3"></i>
                </div>
              </td>

              <td class="align-middle">
                <span class="fw-bold text-primary">{{ item.ma_phu_tung }}</span>
              </td>

              <td class="align-middle text-wrap" style="min-width: 200px">
                <span class="fw-bold text-dark d-block">{{
                  item.ten_phu_tung
                }}</span>
              </td>

              <td class="align-middle">
                <span class="fw-semibold text-secondary">{{
                    getThuongHieuName(item.thuong_hieu_id)
                  }}</span>
              </td>

              <td class="align-middle">
                <span class="fw-bold text-dark">{{
                    item.nhom_phu_tung?.ten_nhom ||
                    getNhomName(item.nhom_phu_tung_id)
                  }}</span>
              </td>

              <td class="text-end align-middle">
                <span v-if="item.is_gia_lien_he" class="fw-bold text-danger"
                  >Liên hệ</span
                >
                <span v-else class="fw-bold text-danger">{{
                  formatCurrency(item.gia_ban)
                }}</span>
              </td>

              <td class="text-center align-middle">
                <span
                  v-if="item.trang_thai === 1"
                  class="badge badge-custom bg-pastel-success"
                  >HOẠT ĐỘNG</span
                >
                <span v-else class="badge badge-custom bg-pastel-danger"
                  >TẠM KHÓA</span
                >
              </td>
              <td
                class="text-center align-middle"
                v-if="canManage"
              >
                <div class="d-flex align-items-center justify-content-center">
                  <button
                    v-if="canEdit"
                    type="button"
                    class="action-btn text-primary me-1"
                    @click="openEditModal(item)"
                    data-bs-toggle="modal"
                    data-bs-target="#modalPhuTung"
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
        <span class="text-muted fw-medium"
          >Tổng cộng: {{ filteredPhuTung.length }} mục</span
        >
      </div>
    </div>

        <!-- Modal Form -->
    <div
      class="modal fade"
      id="modalPhuTung"
      tabindex="-1"
      aria-hidden="true"
      ref="modalPhuTung"
    >
      <div class="modal-dialog modal-xl mt-5" role="document">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header border-bottom pb-3">
            <h4
              class="modal-title fw-bold text-dark mb-0 fs-4"
              id="modalPhuTungTitle"
            >
              {{ isEditMode ? "Cập Nhật Phụ Tùng" : "Thêm Phụ Tùng Mới" }}
            </h4>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="resetForm"
            ></button>
          </div>
          <form @submit.prevent="saveData">
            <div class="modal-body pb-2 pt-4">
              <div class="row">
                <!-- Cột trái: Thông tin cơ bản & Mô tả -->
                <div class="col-md-7 pe-md-4">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-medium text-dark"
                        >Thương Hiệu <span class="text-danger">*</span></label
                      >
                      <v-select
                        v-model="formData.thuong_hieu_id"
                        :options="thuongHieuList"
                        placeholder="Chọn Thương Hiệu"
                        searchable
                        :reduce="(x) => x.value"
                          label="label"
                          required
                          :can-clear="false"
                        />
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-medium text-dark"
                        >Nhóm Phụ Tùng <span class="text-danger">*</span></label
                      >
                      <v-select
                        v-model="formData.nhom_phu_tung_id"
                        :options="nhomPhuTungList"
                        placeholder="Chọn Nhóm"
                        searchable
                        :reduce="(x) => x.value"
                          label="label"
                          required
                          :can-clear="false"
                        />
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-4 mb-3">
                      <label class="form-label fw-medium text-dark"
                        >Mã Phụ Tùng <span class="text-danger">*</span></label
                      >
                      <input
                        type="text"
                        class="form-control text-uppercase fw-bold"
                        v-model="formData.ma_phu_tung"
                        required
                        placeholder="VD: 11912-ZY6"
                        
                      />
                    </div>
                    <div class="col-md-8 mb-3">
                      <label class="form-label fw-medium text-dark"
                        >Tên Phụ Tùng <span class="text-danger">*</span></label
                      >
                      <input
                        type="text"
                        class="form-control"
                        v-model="formData.ten_phu_tung"
                        required
                        placeholder="Tên Phụ Tùng..."
                      />
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12 mb-3">
                      <label class="form-label fw-medium text-dark"
                        >Đơn Vị Tính</label
                      >
                      <input
                        type="text"
                        class="form-control"
                        v-model="formData.don_vi_tinh"
                        placeholder="Cái, Bộ..."
                      />
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12 mb-3">
                      <label class="form-label fw-medium text-dark"
                        >Mô tả chi tiết</label
                      >
                      <textarea
                        class="form-control"
                        rows="4"
                        v-model="formData.mo_ta"
                        placeholder="Nhập mô tả chi tiết..."
                      ></textarea>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12 mb-3">
                      <label class="form-label fw-medium text-dark"
                        >Thông số kỹ thuật</label
                      >
                      <textarea
                        class="form-control"
                        rows="4"
                        v-model="formData.thong_so_ky_thuat"
                        placeholder="Nhập thông số kỹ thuật..."
                      ></textarea>
                    </div>
                  </div>

                  <div class="row mt-2">
                    <div class="col-md-12 mb-3">
                      <label class="form-label fw-medium text-dark"
                        >Video Youtube URL (Tùy chọn)</label
                      >
                      <input
                        type="text"
                        class="form-control"
                        v-model="formData.video_url"
                        placeholder="VD: https://www.youtube.com/embed/..."
                      />
                    </div>
                  </div>
                </div>

                <!-- Cột phải: Hình ảnh, Giá cả & Trạng thái -->
                <div class="col-md-5 border-start ps-md-4">
                  <div class="row">
                    <div class="col-md-12 mb-3">
                      <label class="form-label fw-medium text-dark"
                        >Hình Ảnh Chính</label
                      >
                      <input
                        type="file"
                        id="hinh_anh_file"
                        class="form-control mb-2"
                        accept="image/*"
                        @change="handleMainImageUpload"
                      />
                      <div
                        class="d-flex justify-content-center align-items-center border rounded bg-light mb-3"
                        style="height: 180px"
                      >
                        <div
                          v-if="mainImagePreview"
                          class="position-relative d-inline-block"
                        >
                          <img
                            :src="mainImagePreview"
                            style="
                              max-height: 160px;
                              max-width: 100%;
                              object-fit: contain;
                            "
                          />
                          <button
                              type="button"
                              class="btn btn-icon btn-sm bg-white text-danger position-absolute top-0 end-0 m-2 rounded-circle shadow-sm border"
                              @click="clearMainImage"
                              title="Xóa ảnh"
                              style="width: 28px; height: 28px; padding: 0;"
                            >
                              <i class="bx bx-trash"></i>
                            </button>
                        </div>
                        <span class="text-muted" v-else
                          ><i class="bx bx-image fs-1"></i
                        ></span>
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
                      <div
                        class="d-flex justify-content-between align-items-center mb-1"
                      >
                        <label class="form-label fw-medium text-dark mb-0"
                          >Giá Bán (VNĐ) <span v-if="!formData.is_gia_lien_he" class="text-danger">*</span></label
                        >
                        <div
                          class="form-check mb-0 d-flex align-items-center gap-1"
                        >
                          <input
                            class="form-check-input m-0"
                            type="checkbox"
                            id="is_gia_lien_he"
                            v-model="formData.is_gia_lien_he"
                            :true-value="true"
                            :false-value="false"
                            @change="onGiaLienHeChange"
                          />
                          <label
                            class="form-check-label text-muted text-nowrap"
                            for="is_gia_lien_he"
                            style="font-size: 0.8rem; cursor: pointer"
                            >Liên hệ</label
                          >
                        </div>
                      </div>
                      <input
                        type="number"
                        class="form-control"
                        v-model="formData.gia_ban"
                        min="0"
                        placeholder="Nhập giá bán..."
                        :disabled="formData.is_gia_lien_he === true"
                      />
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12 mb-3">
                      <label class="form-label fw-medium text-dark"
                        >Trạng Thái <span class="text-danger">*</span></label
                      >
                      <v-select
                        v-model="formData.trang_thai"
                        :options="[
                          { value: 1, label: 'Hoạt Động' },
                          { value: 0, label: 'Tạm Khóa' },
                        ]"
                        :reduce="(x) => x.value"
                        label="label"
                        :can-clear="false"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer border-top py-3 mt-2 bg-light">
              <button
                type="button"
                class="btn btn-outline-secondary px-4 me-3"
                data-bs-dismiss="modal"
                @click="resetForm"
              >
                Hủy
              </button>
              <button
                type="submit"
                class="btn btn-primary px-5 shadow-sm"
                :disabled="isSubmitting"
              >
                <span
                  v-if="isSubmitting"
                  class="spinner-border spinner-border-sm me-1"
                  role="status"
                  aria-hidden="true"
                ></span>
                {{ isEditMode ? "Lưu Thay Đổi" : "Thêm Phụ Tùng" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from "@vueform/multiselect";
import axiosClient from "../../../api/axiosClient";
import { computed } from "vue";
import { useAuthStore } from "../../../stores/authStore";
import { useQuery, useMutation, useQueryClient } from "@tanstack/vue-query";
import Swal from "sweetalert2";
import { createToaster } from "@meforma/vue-toaster";

export default {
  name: "PhuTung",
  components: {
    vSelect,
  },
  setup() {
    const queryClient = useQueryClient();
    const authStore = useAuthStore();
    const toaster = createToaster({ position: "top-right", duration: 3000 });
    const fetchApi = async (url) => (await axiosClient.get(url)).data;

    const { data: rawPhuTungList, isFetching: isFetchingPhuTungs , isPending: isPendingrawPhuTungList } = useQuery({
      queryKey: ["phu-tung"],
      queryFn: () => fetchApi("/admin/phu-tung"),
    });
    const { data: rawThuongHieuList, isFetching: isFetchingBrands , isPending: isPendingrawThuongHieuList } = useQuery({
      queryKey: ["brands"],
      queryFn: () => fetchApi("/admin/thuong-hieu?all=true"),
    });
    const { data: rawNhomPhuTungList, isFetching: isFetchingNhom , isPending: isPendingrawNhomPhuTungList } = useQuery({
      queryKey: ["nhom-phu-tung"],
      queryFn: () => fetchApi("/admin/nhom-phu-tung?all=true"),
    });

    const deleteMutation = useMutation({
      mutationFn: async (id) =>
        await axiosClient.delete(`/admin/phu-tung/${id}`),
      onError: (err, id, context) => {
        toaster.error(err.response?.data?.message || "Lỗi khi xóa phụ tùng");
      },
      onSuccess: () => toaster.success("Đã xóa phụ tùng!"),
      onSettled: () =>
        queryClient.invalidateQueries({ queryKey: ["phu-tung"] }),
    });

    const saveMutation = useMutation({
      mutationFn: async ({ id, payload }) => {
        if (id) {
          payload.append("_method", "PUT");
          return await axiosClient.post(`/admin/phu-tung/${id}`, payload, {
            headers: { "Content-Type": "multipart/form-data" },
          });
        }
        return await axiosClient.post("/admin/phu-tung", payload, {
          headers: { "Content-Type": "multipart/form-data" },
        });
      },
      onError: (err, vars, context) => {
        toaster.error(err.response?.data?.message || "Lỗi lưu phụ tùng");
      },
      onSuccess: (data, vars) =>
        toaster.success(
          vars.id ? "Cập nhật phụ tùng thành công!" : "Đã thêm phụ tùng mới!",
        ),
      onSettled: () =>
        queryClient.invalidateQueries({ queryKey: ["phu-tung"] }),
    });

    return {
      queryClient,
      authStore,
      rawPhuTungList,
      isFetchingPhuTungs,
      rawThuongHieuList,
      isFetchingBrands,
      rawNhomPhuTungList,
      isFetchingNhom,
      deleteMutation,
      saveMutation,
      toaster,
    };
  },
  data() {
    return {
      activeTab: "MARINE",
      currentUserRole:
        JSON.parse(
          localStorage.getItem("auth_user") || "{}",
        ).roles?.[0]?.toUpperCase() || "ADMIN",
      searchKeyword: "",
      filterThuongHieuId: null,
      filterNhomId: null,
      isEditMode: false,
      isSubmitting: false,
      mainImageFile: null,
      mainImagePreview: null,
      galleryFiles: [],

      formData: {
        id: null,
        thuong_hieu_id: null,
        nhom_phu_tung_id: null,
        ma_phu_tung: "",
        ten_phu_tung: "",
        gia_ban: null,
        is_gia_lien_he: false,
        don_vi_tinh: "Cái",

        mo_ta: "",
        thong_so_ky_thuat: "",
        video_url: "",
        hinh_anh: "",
        thu_vien_anh: [],
        trang_thai: 1,
      },
    };
  },
  computed: {
    canAdd() {
      return this.authStore.isAdmin || this.authStore.hasPermission('Thêm Phụ tùng');
    },
    canEdit() {
      return this.authStore.isAdmin || this.authStore.hasPermission('Sửa Phụ tùng');
    },
    canDelete() {
      return this.authStore.isAdmin || this.authStore.hasPermission('Xóa Phụ tùng');
    },
    canManage() {
      return this.canEdit || this.canDelete;
    },
    isFetching() {
      return (
        this.isFetchingPhuTungs || this.isFetchingBrands || this.isFetchingNhom
      );
    },
    thuongHieuList() {
      const items = this.rawThuongHieuList?.data
        ? this.rawThuongHieuList.data
        : this.rawThuongHieuList || [];
      return items
        .filter(th => th.status === 1 || th.id === this.formData.thuong_hieu_id || th.id === this.filterThuongHieuId)
        .map((th) => ({ value: th.id, label: th.name }));
    },
    nhomPhuTungList() {
      const items = this.rawNhomPhuTungList?.data
        ? this.rawNhomPhuTungList.data
        : this.rawNhomPhuTungList || [];
      return items.map((nhom) => ({
        value: nhom.id,
        label: nhom.ten_nhom,
      }));
    },
    phuTungList() {
      const items = this.rawPhuTungList?.data
        ? this.rawPhuTungList.data
        : this.rawPhuTungList || [];
      return items.map((p) => ({
        ...p,
        is_gia_lien_he:
          p.is_gia_lien_he == 1 || p.is_gia_lien_he === true || p.gia_ban === 0,
      }));
    },
    filteredPhuTung() {
      let result = [...this.phuTungList];

      // Lọc theo thương hiệu
      if (this.filterThuongHieuId) {
        result = result.filter(
          (p) => p.thuong_hieu_id === this.filterThuongHieuId,
        );
      }

      // Lọc theo nhóm
      if (this.filterNhomId) {
        result = result.filter((p) => p.nhom_phu_tung_id === this.filterNhomId);
      }

      // Lọc theo từ khóa (Mã hoặc Tên)
      if (this.searchKeyword) {
        const keyword = this.searchKeyword.toLowerCase().trim();
        result = result.filter(
          (p) =>
            String(p.ma_phu_tung || '').toLowerCase().includes(keyword) ||
            String(p.ten_phu_tung || '').toLowerCase().includes(keyword),
        );
      }

      return result;
    },
  },
  methods: {
    switchTab(tab) {
      this.activeTab = tab;
      this.resetFilters();
    },
    resetFilters() {
      this.searchKeyword = "";
      this.filterThuongHieuId = null;
      this.filterNhomId = null;
    },
    getThuongHieuName(id) {
      const th = this.thuongHieuList.find((t) => t.value === id);
      return th ? th.label : "N/A";
    },
    getNhomName(id) {
      const nhom = this.nhomPhuTungList.find((n) => n.value === id);
      return nhom ? nhom.label : "N/A";
    },
    formatCurrency(value) {
      if (!value) return "0 đ";
      return new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
      }).format(value);
    },
    getImageUrl(path) {
      if (!path) return "";
      if (path.startsWith("http")) return path;
      return "http://localhost:8000" + path;
    },
    getBlobUrl(file) {
      if (!file) return "";
      return URL.createObjectURL(file);
    },
    onGiaLienHeChange() {
      if (this.formData.is_gia_lien_he) {
        this.formData.gia_ban = 0;
      } else {
        this.formData.gia_ban = null;
      }
    },
    clearMainImage() {
      this.mainImageFile = null;
      this.mainImagePreview = null;
      this.formData.hinh_anh = "";
      const f1 = document.getElementById("hinh_anh_file");
      if (f1) f1.value = "";
    },
    handleMainImageUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.mainImageFile = file;
        this.mainImagePreview = URL.createObjectURL(file);
      }
    },
    handleGalleryUpload(event) {
      const files = Array.from(event.target.files);
      if (files && files.length > 0) {
        this.galleryFiles = [...this.galleryFiles, ...files];
      }
      event.target.value = "";
    },
    resetForm() {
      this.isEditMode = false;
      this.formData = {
        id: null,
        thuong_hieu_id: null,
        nhom_phu_tung_id: null,
        ma_phu_tung: "",
        ten_phu_tung: "",
        gia_ban: null,
        is_gia_lien_he: false,
        don_vi_tinh: "Cái",
        hinh_anh: "",
        thu_vien_anh: [],
        mo_ta: "",

        thong_so_ky_thuat: "",
        video_url: "",
        trang_thai: 1,
      };
      this.mainImageFile = null;
      this.mainImagePreview = null;
      this.galleryFiles = [];
      const f1 = document.getElementById("hinh_anh_file");
      if (f1) f1.value = "";
      const f2 = document.getElementById("thu_vien_anh");
      if (f2) f2.value = "";
    },
    openAddModal() {
      this.resetForm();
      this.formData.thuong_hieu_id = this.filterThuongHieuId || null;
      this.formData.nhom_phu_tung_id = this.filterNhomId || null;
    },
    openEditModal(item) {
      this.isEditMode = true;
      this.formData = { ...item };
      this.formData.is_gia_lien_he =
        item.is_gia_lien_he === 1 || item.is_gia_lien_he === true || item.gia_ban === 0 || item.gia_ban === "0.00" || item.gia_ban === "0";
      if (this.formData.is_gia_lien_he) {
        this.formData.gia_ban = 0;
      } else {
        this.formData.gia_ban = item.gia_ban ? Number(item.gia_ban) : null;
      }
      this.formData.thu_vien_anh = item.thu_vien_anh || [];
      this.mainImageFile = null;
      this.mainImagePreview = item.hinh_anh
        ? this.getImageUrl(item.hinh_anh)
        : null;
      this.galleryFiles = [];
    },
    async saveData() {
      if (
        !this.formData.thuong_hieu_id ||
        !this.formData.nhom_phu_tung_id ||
        !this.formData.ma_phu_tung ||
        !this.formData.ten_phu_tung
      ) {
        this.toaster.error("Vui lòng nhập đầy đủ thông tin bắt buộc!");
        return;
      }

      if (!this.formData.is_gia_lien_he) {
        if (this.formData.gia_ban === null || this.formData.gia_ban === undefined || this.formData.gia_ban === "" || Number(this.formData.gia_ban) <= 0) {
          this.toaster.error("Vui lòng nhập giá bán lớn hơn 0 hoặc tích chọn 'Liên hệ'!");
          return;
        }
      }

      this.isSubmitting = true;
      try {
        const payload = new FormData();
        payload.append("thuong_hieu_id", this.formData.thuong_hieu_id || "");
        payload.append(
          "nhom_phu_tung_id",
          this.formData.nhom_phu_tung_id || "",
        );
        payload.append("ma_phu_tung", this.formData.ma_phu_tung);
        payload.append("ten_phu_tung", this.formData.ten_phu_tung);
        payload.append("gia_ban", this.formData.gia_ban);
        payload.append("is_gia_lien_he", this.formData.is_gia_lien_he ? 1 : 0);

        payload.append("trang_thai", this.formData.trang_thai);
        if (this.formData.don_vi_tinh)
          payload.append("don_vi_tinh", this.formData.don_vi_tinh);
        if (this.formData.mo_ta) payload.append("mo_ta", this.formData.mo_ta);
        if (this.formData.thong_so_ky_thuat)
          payload.append("thong_so_ky_thuat", this.formData.thong_so_ky_thuat);
        if (this.formData.video_url)
          payload.append("video_url", this.formData.video_url);

        if (this.mainImageFile) {
          payload.append("hinh_anh_file", this.mainImageFile);
        } else if (this.formData.hinh_anh === "") {
          payload.append("hinh_anh_file", "");
        }

        payload.append(
          "thu_vien_anh_kept",
          JSON.stringify(this.formData.thu_vien_anh || []),
        );

        this.galleryFiles.forEach((file) => {
          payload.append("thu_vien_anh_files[]", file);
        });

        if (this.isEditMode) {
          await this.saveMutation.mutateAsync({
            id: this.formData.id,
            payload,
          });
        } else {
          await this.saveMutation.mutateAsync({ payload });
        }

        this.resetForm();
        const closeBtn = document.querySelector("#modalPhuTung .btn-close");
        if (closeBtn) closeBtn.click();
      } catch (error) {
        console.error("Lỗi chuẩn bị dữ liệu phụ tùng:", error);
        this.toaster.error("Lỗi chuẩn bị dữ liệu!");
      } finally {
        this.isSubmitting = false;
      }
    },
    async confirmDelete(item) {
        const result = await Swal.fire({
          title: 'Xác nhận xóa',
          html: `Bạn có chắc chắn muốn xóa phụ tùng <b>"${item.ten_phu_tung}"</b>?<br><br><small class="text-danger">CẢNH BÁO: Liên kết đơn hàng sẽ bị ảnh hưởng.</small>`, 
          icon: 'warning',
          iconColor: '#ff3e1d',
          showCancelButton: true,
          confirmButtonText: 'Xóa Phụ tùng',
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
      },
  },
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

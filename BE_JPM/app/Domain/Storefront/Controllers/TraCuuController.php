<?php

namespace App\Domain\Storefront\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Domain\MarineCatalog\Models\ThuongHieu;
use App\Domain\PIM\Models\NhomPhuTung;
use App\Domain\MarineCatalog\Models\DongCoModel;
use App\Domain\PIM\Models\PhuTung;
use App\Domain\PIM\Models\SoDoPhuTung;
use App\Domain\CMS\Models\Faq;
use App\Domain\CMS\Models\LienHe;

class TraCuuController extends Controller
{
    public function submitLienHe(Request $request)
    {
        $data = $request->validate([
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'so_dien_thoai' => 'required|string|max:20',
            'noi_dung' => 'required|string',
        ]);
        
        $data['trang_thai'] = 'Mới';
        LienHe::create($data);
        
        return response()->json(['success' => true, 'message' => 'Đã gửi liên hệ thành công.']);
    }

    public function faqs(Request $request)
    {
        $limit = (int) $request->get('limit', 4);
        $faqs = \App\Domain\CMS\Models\Faq::where('status', 'active')
            ->orWhere('status', 1)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $faqs
        ]);
    }


    public function sanPhamBanChay(Request $request)
    {
        $limit = (int) $request->get('limit', 4);
        $products = \App\Domain\CMS\Models\SanPham::with('danhMuc')
            ->where('trang_thai', 1)
                        ->orderBy('id', 'desc') // TODO: Implement real order counting logic later
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }


    public function traCuuNhanh(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'phone' => 'required|string'
        ]);

        $code = trim($request->code);
        $phone = trim($request->phone);

        // Check if it's an Order (Don Hang)
        $order = \App\Domain\Sales\Models\DonHang::with('chiTiet')
            ->where('ma_don_hang', $code)
            ->where('khach_hang_sdt', $phone)
            ->first();

        if ($order) {
            return response()->json([
                'success' => true,
                'type' => 'order',
                'data' => $order
            ]);
        }

        // Check if it's a Quote (Yeu Cau Bao Gia)
        $quote = \App\Domain\Sales\Models\YeuCauBaoGia::with('chiTiet')
            ->where('ma_yeu_cau', $code)
            ->where('khach_hang_sdt', $phone)
            ->first();

        if ($quote) {
            return response()->json([
                'success' => true,
                'type' => 'quote',
                'data' => $quote
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy giao dịch nào khớp với Mã và Số điện thoại này.'
        ], 404);
    }

    // Lấy sản phẩm ngẫu nhiên cho trang chủ
    public function phuTungNoiBat(Request $request)
    {
        $limit = (int) $request->get('limit', 4);
        $products = PhuTung::with(['thuongHieu', 'nhomPhuTung'])
            ->where('trang_thai', 1)
                        ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
    // Lấy danh sách thương hiệu
    public function thuongHieu()
    {
        $brands = Cache::remember('public_thuong_hieu', 86400, function () {
            return ThuongHieu::where('status', 1)->orderBy('created_at', 'asc')->get();
        });
        return response()->json([
            'success' => true,
            'data' => $brands
        ]);
    }

    // Lấy chi tiết thương hiệu theo code (YAM, HON...) để load catalog
    public function thuongHieuChiTiet($code)
    {
        $brand = ThuongHieu::where('code', strtoupper($code))->orWhere('name', 'LIKE', $code)->first();
        if (!$brand) {
            return response()->json(['success' => false, 'message' => 'Brand not found'], 404);
        }

        // Lấy tất cả models của hãng này
        $models = DongCoModel::with('loaiDongCo')->where('thuong_hieu_id', $brand->id)->where('trang_thai', 1)->get();
        
        // Bóc tách danh sách Loại động cơ duy nhất từ các models này
        $loaiDongCos = $models->pluck('loaiDongCo')->unique('id')->filter()->values();
        
        // Tính khoảng năm
        $years = $models->pluck('nam_san_xuat')->filter()->unique()->sortDesc()->values()->toArray();

        // Tạo mảng models tối giản để trả về Frontend
        $mappedModels = $models->map(function($m) {
            return [
                'id' => $m->id,
                'hp' => $m->ma_luc,
                'year' => $m->nam_san_xuat,
                'code' => $m->ma_model,
                'name' => $m->ten_model,
                'stroke' => $m->loai_nhip,
                'loai' => $m->loaiDongCo ? $m->loaiDongCo->ma_loai_dong_co : '',
                'desc' => $m->mo_ta ?? $m->ten_model,
            ];
        });

        // Tạo mảng Loại động cơ tối giản
        $mappedLoai = $loaiDongCos->map(function($l) {
            return [
                'value' => $l->ma_loai_dong_co,
                'label' => $l->ten_loai_dong_co,
            ];
        });

        // Lấy danh sách các loại Thì (Stroke) duy nhất
        $strokes = $models->pluck('loai_nhip')->filter()->unique()->values()->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'brand' => $brand,
                'loaiDongCoList' => $mappedLoai,
                'models' => $mappedModels,
                'years' => $years,
                'strokes' => $strokes
            ]
        ]);
    }

    // Lấy danh sách nhóm phụ tùng
    public function nhomPhuTung(Request $request)
    {
        $thuongHieuId = $request->get('thuong_hieu_id');
        $loaiDongCoId = $request->get('loai_dong_co_id');
        $dongCoModelId = $request->get('dong_co_model_id');
        $namSx = $request->get('nam_sx');
        
        $cacheKey = 'public_nhom_phu_tung_' . ($thuongHieuId ?? 'all') . '_' . ($loaiDongCoId ?? 'all') . '_' . ($dongCoModelId ?? 'all') . '_' . ($namSx ?? 'all');
        
        $groups = Cache::remember($cacheKey, 86400, function () use ($thuongHieuId, $loaiDongCoId, $dongCoModelId, $namSx) {
            $query = NhomPhuTung::where('trang_thai', 1);
            
            if ($thuongHieuId || $loaiDongCoId || $dongCoModelId || $namSx) {
                $query->whereHas('phuTungs', function($q) use ($thuongHieuId, $loaiDongCoId, $dongCoModelId, $namSx) {
                    $q->where('trang_thai', 1);
                    if ($thuongHieuId) {
                        $q->where('thuong_hieu_id', $thuongHieuId);
                    }
                    if ($loaiDongCoId || $dongCoModelId || $namSx) {
                        $q->where(function($q3) use ($loaiDongCoId, $dongCoModelId, $namSx) {
                            $q3->whereHas('dongCoModels', function($q2) use ($loaiDongCoId, $dongCoModelId, $namSx) {
                                if ($dongCoModelId) {
                                    $q2->where('dong_co_models.id', $dongCoModelId);
                                } else {
                                    if ($loaiDongCoId) {
                                        $q2->where('dong_co_models.loai_dong_co_id', $loaiDongCoId);
                                    }
                                }
                                if ($namSx) {
                                    $q2->where('nam_san_xuat', $namSx);
                                }
                            });
                            
                            if ($dongCoModelId || $namSx) {
                                $q3->orWhereHas('soDoPhuTungs', function($q4) use ($dongCoModelId, $namSx) {
                                    $q4->whereHas('model', function($q5) use ($dongCoModelId, $namSx) {
                                        if ($dongCoModelId) {
                                            $q5->where('id', $dongCoModelId);
                                        }
                                        if ($namSx) {
                                            $q5->where('nam_san_xuat', $namSx);
                                        }
                                    });
                                });
                            }
                        });
                    }
                });
            }
            return $query->get();
        });

        return response()->json([
            'success' => true,
            'data' => $groups
        ]);
    }

    // Lấy danh sách model động cơ (có thể lọc theo brand)
    public function loaiDongCo(Request $request)
    {
        $cacheKey = 'public_loai_dong_co_' . ($request->thuong_hieu_id ?? 'all');
        $types = \Illuminate\Support\Facades\Cache::remember($cacheKey, 86400, function () use ($request) {
            $query = \App\Domain\MarineCatalog\Models\LoaiDongCo::where('trang_thai', 1);
            if ($request->has('thuong_hieu_id') && $request->thuong_hieu_id != '') {
                $brandId = $request->thuong_hieu_id;
                $query->whereHas('dongCoModels', function($q) use ($brandId) {
                    $q->where('thuong_hieu_id', $brandId);
                });
            }
            return $query->get();
        });
        
        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    public function dongCoModel(Request $request)
    {
        $cacheKey = 'public_dong_co_model_' . ($request->thuong_hieu_id ?? 'all') . '_' . ($request->loai_dong_co_id ?? 'all') . '_' . ($request->nam_sx ?? 'all');
        $models = Cache::remember($cacheKey, 86400, function () use ($request) {
            $query = DongCoModel::where('trang_thai', 1);
            
            if ($request->has('thuong_hieu_id') && $request->thuong_hieu_id != '') {
                $query->where('thuong_hieu_id', $request->thuong_hieu_id);
            }
            if ($request->has('loai_dong_co_id') && $request->loai_dong_co_id != '') {
                $query->where('loai_dong_co_id', $request->loai_dong_co_id);
            }
            if ($request->has('nam_sx') && $request->nam_sx != '') {
                $query->where('nam_san_xuat', (int)$request->nam_sx);
            }
            return $query->get();
        });

        return response()->json([
            'success' => true,
            'data' => $models
        ]);
    }

    // Lấy danh sách năm sản xuất dựa trên dữ liệu model (hoặc range tĩnh)
    public function namSanXuat(Request $request)
    {
        $cacheKey = 'public_nam_san_xuat_' . ($request->thuong_hieu_id ?? 'all') . '_' . ($request->loai_dong_co_id ?? 'all');
        
        $years = Cache::remember($cacheKey, 86400, function () use ($request) {
            $query = DongCoModel::where('trang_thai', 1);

            if ($request->has('thuong_hieu_id') && $request->thuong_hieu_id != '') {
                $query->where('thuong_hieu_id', $request->thuong_hieu_id);
            }
            if ($request->has('loai_dong_co_id') && $request->loai_dong_co_id != '') {
                $query->where('loai_dong_co_id', $request->loai_dong_co_id);
            }

            return $query->select('nam_san_xuat')->whereNotNull('nam_san_xuat')->distinct()->orderBy('nam_san_xuat', 'desc')->pluck('nam_san_xuat')->toArray();
        });

        return response()->json([
            'success' => true,
            'data' => $years
        ]);
    }

    // Tra cứu phụ tùng chính
        public function traCuuSoDo(Request $request)
    {
        $query = \App\Domain\PIM\Models\SoDoPhuTung::with(['nhomPhuTung', 'model.thuongHieu'])
            ->where('trang_thai', 1);

        $query->whereHas('model', function($q) use ($request) {
            if ($request->has('thuong_hieu_id') && $request->thuong_hieu_id != '') {
                $q->where('thuong_hieu_id', $request->thuong_hieu_id);
            }
            if ($request->has('loai_dong_co_id') && $request->loai_dong_co_id != '') {
                $q->where('loai_dong_co_id', $request->loai_dong_co_id);
            }
            if ($request->has('nam_sx') && $request->nam_sx != '') {
                $q->where('nam_san_xuat', $request->nam_sx);
            }
            if ($request->has('dong_co_model_id') && $request->dong_co_model_id != '') {
                $q->where('id', $request->dong_co_model_id);
            }
        });

        if ($request->has('nhom_phu_tung_id') && $request->nhom_phu_tung_id != '') {
            $query->where('nhom_phu_tung_id', $request->nhom_phu_tung_id);
        }

        if ($request->has('keyword') && $request->keyword != '') {
            $query->where('ten_so_do', 'like', '%' . $request->keyword . '%');
        }

        $perPage = $request->per_page ?? 24;
        $diagrams = $query->orderBy('may_thuy_model_id')->orderBy('thu_tu')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $diagrams
        ]);
    }

    public function traCuu(Request $request)
    {
        $keyword = $request->keyword ?? '';
        
        $query = PhuTung::search($keyword);

        $query->where('trang_thai', 1);

        if ($request->has('thuong_hieu_id') && $request->thuong_hieu_id != '') {
            $query->where('thuong_hieu_id', $request->thuong_hieu_id);
        }

        if (config('scout.driver') === 'database') {
            $query->query(function($q) use ($request) {
                $q->with(['thuongHieu', 'nhomPhuTung']);
                
                if ($request->has('nhom_phu_tung_id') && $request->nhom_phu_tung_id != '') {
                    $q->where(function($q3) use ($request) {
                        if (is_array($request->nhom_phu_tung_id)) {
                            $q3->whereIn('nhom_phu_tung_id', $request->nhom_phu_tung_id);
                        } else {
                            $q3->where('nhom_phu_tung_id', $request->nhom_phu_tung_id);
                        }
                        
                        $q3->orWhereHas('soDoPhuTungs', function($q4) use ($request) {
                            if (is_array($request->nhom_phu_tung_id)) {
                                $q4->whereIn('nhom_phu_tung_id', $request->nhom_phu_tung_id);
                            } else {
                                $q4->where('nhom_phu_tung_id', $request->nhom_phu_tung_id);
                            }
                        });
                    });
                }

                if ($request->has('dong_co_model_id') && $request->dong_co_model_id != '') {
                    $q->where(function($q3) use ($request) {
                        $q3->whereHas('dongCoModels', function($q2) use ($request) {
                            $q2->where('dong_co_models.id', $request->dong_co_model_id);
                        })->orWhereHas('soDoPhuTungs', function($q4) use ($request) {
                            $q4->where('may_thuy_model_id', $request->dong_co_model_id)
                               ->where('trang_thai', 1);
                        });
                    });
                }

                if ($request->has('nam_sx') && $request->nam_sx != '') {
                    $q->where(function($q3) use ($request) {
                        $q3->whereHas('dongCoModels', function($q2) use ($request) {
                            $q2->where('nam_san_xuat', $request->nam_sx);
                        })->orWhereHas('soDoPhuTungs', function($q4) use ($request) {
                            $q4->whereHas('model', function($q5) use ($request) {
                                $q5->where('nam_san_xuat', $request->nam_sx);
                            });
                        });
                    });
                }
            });
        } else {
            $query->query(fn ($q) => $q->with(['thuongHieu', 'nhomPhuTung']));
            
            if ($request->has('nhom_phu_tung_id') && $request->nhom_phu_tung_id != '') {
                if (is_array($request->nhom_phu_tung_id)) {
                    $query->whereIn('nhom_phu_tung_id', $request->nhom_phu_tung_id);
                } else {
                    $query->where('nhom_phu_tung_id', $request->nhom_phu_tung_id);
                }
            }

            if ($request->has('dong_co_model_id') && $request->dong_co_model_id != '') {
                $query->whereIn('dong_co_model_ids', [(int)$request->dong_co_model_id]);
            }

            if ($request->has('nam_sx') && $request->nam_sx != '') {
                $query->whereIn('applicable_years', [(int)$request->nam_sx]);
            }
        }

        if ($request->has('min_price') && $request->min_price != '') {
            $query->where('gia_ban', '>=', (float)$request->min_price);
        }
        if ($request->has('max_price') && $request->max_price != '') {
            $query->where('gia_ban', '<=', (float)$request->max_price);
        }

        if ($request->has('sort_by') && $request->sort_by != '') {
        }

        $perPage = $request->has('per_page') ? (int) $request->per_page : 12;
        $phuTungs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $phuTungs
        ]);
    }

    // Lấy danh sách sơ đồ và phụ tùng thuộc Model Động Cơ
    
    public function soDoByModelId($modelId)
    {
        $diagrams = \App\Domain\PIM\Models\SoDoPhuTung::with(['nhomPhuTung'])->withCount('phuTungs')
            ->where('may_thuy_model_id', $modelId)
            ->where('trang_thai', 1)
            ->orderBy('thu_tu')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $diagrams
        ]);
    }
    public function soDoModel($modelCode)
    {
        $model = DongCoModel::with('thuongHieu')->where('ma_model', strtoupper($modelCode))->first();
        if (!$model) {
            return response()->json(['success' => false, 'message' => 'Model not found'], 404);
        }

        $diagrams = SoDoPhuTung::with(['nhomPhuTung', 'chiTiets.phuTung'])
            ->where('may_thuy_model_id', $model->id)
            ->where('trang_thai', 1)
            ->orderBy('thu_tu')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'model' => $model,
                'diagrams' => $diagrams
            ]
        ]);
    }

    public function quickSearch(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([
                'success' => true,
                'data' => [
                    'query' => $q,
                    'exact_match' => null,
                    'models' => [],
                    'phu_tungs' => [],
                    'san_phams' => []
                ]
            ]);
        }

        $upperQ = strtoupper($q);

        // 1. Check exact match
        $exactMatch = null;

        $exactPart = PhuTung::with('thuongHieu')
            ->where('trang_thai', 1)
            ->where('ma_phu_tung', $upperQ)
            ->first();

        if ($exactPart) {
            $exactMatch = [
                'type' => 'phu_tung',
                'title' => $exactPart->ma_phu_tung . ' - ' . $exactPart->ten_phu_tung,
                'code' => $exactPart->ma_phu_tung,
                'url' => '/chi-tiet-phu-tung/' . $exactPart->ma_phu_tung,
                'price' => $exactPart->gia_ban,
                'is_contact_price' => (bool)$exactPart->is_gia_lien_he,
                'brand' => $exactPart->thuongHieu?->name
            ];
        } else {
            $exactModel = DongCoModel::with('thuongHieu')
                ->where('trang_thai', 1)
                ->where('ma_model', $upperQ)
                ->first();

            if ($exactModel) {
                $exactMatch = [
                    'type' => 'model',
                    'title' => $exactModel->ten_model . ' (' . $exactModel->ma_model . ')',
                    'code' => $exactModel->ma_model,
                    'url' => '/so-do/' . $exactModel->ma_model,
                    'brand' => $exactModel->thuongHieu?->name,
                    'year' => $exactModel->nam_san_xuat
                ];
            }
        }

        // 2. Search DongCoModel (Engine Diagrams)
        $models = DongCoModel::with('thuongHieu')
            ->where('trang_thai', 1)
            ->where(function ($sub) use ($q) {
                $sub->where('ma_model', 'LIKE', "%{$q}%")
                    ->orWhere('ten_model', 'LIKE', "%{$q}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'code' => $m->ma_model,
                    'name' => $m->ten_model,
                    'hp' => $m->ma_luc,
                    'year' => $m->nam_san_xuat,
                    'brand' => $m->thuongHieu?->name,
                    'brand_code' => $m->thuongHieu?->code,
                    'url' => '/so-do/' . $m->ma_model
                ];
            });

        // 3. Search PhuTung (OEM & Aftermarket Parts)
        $phuTungs = PhuTung::with(['thuongHieu', 'nhomPhuTung'])
            ->where('trang_thai', 1)
            ->where(function ($sub) use ($q) {
                $sub->where('ma_phu_tung', 'LIKE', "%{$q}%")
                    ->orWhere('ten_phu_tung', 'LIKE', "%{$q}%");
            })
            ->limit(6)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'code' => $p->ma_phu_tung,
                    'name' => $p->ten_phu_tung,
                    'price' => $p->gia_ban,
                    'is_contact_price' => (bool)$p->is_gia_lien_he,
                    'image' => $p->hinh_anh,
                    'group' => $p->nhomPhuTung?->ten_nhom,
                    'brand' => $p->thuongHieu?->name,
                    'url' => '/chi-tiet-phu-tung/' . $p->ma_phu_tung
                ];
            });

        // 4. Search General Products (SanPham CMS)
        $sanPhams = \App\Domain\CMS\Models\SanPham::with('danhMuc')
            ->where('trang_thai', 1)
            ->where(function ($sub) use ($q) {
                $sub->where('ma_san_pham', 'LIKE', "%{$q}%")
                    ->orWhere('ten_san_pham', 'LIKE', "%{$q}%");
            })
            ->limit(4)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'code' => $s->ma_san_pham,
                    'name' => $s->ten_san_pham,
                    'slug' => $s->slug,
                    'price' => $s->gia_ban,
                    'sale_price' => $s->gia_khuyen_mai,
                    'image' => $s->hinh_anh,
                    'category' => $s->danhMuc?->ten_danh_muc,
                    'url' => '/chi-tiet-san-pham/' . $s->slug
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'query' => $q,
                'exact_match' => $exactMatch,
                'models' => $models,
                'phu_tungs' => $phuTungs,
                'san_phams' => $sanPhams
            ]
        ]);
    }
    // Lấy chi tiết phụ tùng
    public function chiTietPhuTung($code)
    {
        $phuTung = PhuTung::with(['thuongHieu', 'nhomPhuTung', 'dongCoModels', 'soDoPhuTungs.model.thuongHieu', 'maThayThes.phuTungMoi'])
            ->where('ma_phu_tung', strtoupper($code))
            ->first();

        if (!$phuTung) {
            return response()->json(['success' => false, 'message' => 'Phụ tùng không tồn tại'], 404);
        }

        // Lấy danh sách phụ tùng liên quan (cùng nhóm phụ tùng)
        $relatedParts = collect();
        if ($phuTung->nhom_phu_tung_id) {
            $relatedParts = PhuTung::with(['thuongHieu', 'nhomPhuTung'])
                ->where('nhom_phu_tung_id', $phuTung->nhom_phu_tung_id)
                ->where('id', '!=', $phuTung->id)
                ->where('trang_thai', 1)
                ->limit(10)
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'phu_tung' => $phuTung,
                'related_parts' => $relatedParts
            ]
        ]);
    }
}













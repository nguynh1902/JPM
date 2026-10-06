<?php namespace App\Domain\Procurement\Controllers;

use App\Http\Controllers\Controller; use App\Domain\Procurement\Models\TheKho; use App\Domain\Core\Resources\TheKhoResource; use Exception; class TheKhoController extends Controller { public function index() { try { return response()->json(["success" => true, "data" => TheKhoResource::collection(TheKho::with(["kho", "sanPham", "phuTung"])->orderBy("created_at", "desc")->cursorPaginate(50))]); } catch (Exception $e) { return response()->json(['success' => false, 'message' => $e->getMessage()], 500); } } }

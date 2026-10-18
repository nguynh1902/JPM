<?php

namespace App\Domain\CMS\Controllers;

use App\Http\Controllers\Controller;

use App\Domain\CMS\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function show($id)
    {
        $banner = Banner::findOrFail($id);
        return response()->json(['success' => true, 'data' => $banner]);
    }

    public function index()
    {
        $banners = Banner::orderBy('id', 'desc')->cursorPaginate(50);
        return response()->json([
            'success' => true,
            'data' => $banners
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'link_url' => 'nullable|string',
            'status' => 'required|boolean',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp|max:5120'
        ]);

        $imageUrl = '';
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
            $imageUrl = '/storage/' . $path;
        }

        $banner = Banner::create([
            'title' => $request->title,
            'image_url' => $imageUrl,
            'link_url' => $request->link_url,
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'data' => $banner
        ]);
    }

    public function update(Request $request, string $id)
    {
        $banner = Banner::findOrFail($id);
        
        $request->validate([
            'title' => 'nullable|string|max:255',
            'link_url' => 'nullable|string',
            'status' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp|max:5120'
        ]);

        $imageUrl = $banner->image_url;
        if ($request->hasFile('image')) {
            if ($imageUrl && Storage::disk('public')->exists(str_replace('/storage/', '', $imageUrl))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $imageUrl));
            }
            $path = $request->file('image')->store('banners', 'public');
            $imageUrl = '/storage/' . $path;
        }

        $banner->update([
            'title' => $request->title,
            'image_url' => $imageUrl,
            'link_url' => $request->link_url,
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'data' => $banner
        ]);
    }

    public function destroy(string $id)
    {
        $banner = Banner::findOrFail($id);
        
        if ($banner->image_url && Storage::disk('public')->exists(str_replace('/storage/', '', $banner->image_url))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $banner->image_url));
        }
        
        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa banner thành công'
        ]);
    }
}





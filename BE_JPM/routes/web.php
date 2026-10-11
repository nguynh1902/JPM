<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SeoBotController;

Route::get('/', function () {
    return view('welcome');
});

// ===== GOOGLE SEARCH CONSOLE & SITEMAPS =====
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/sitemaps/phu-tung.xml', [SitemapController::class, 'phuTung']);
Route::get('/sitemaps/so-do.xml', [SitemapController::class, 'soDo']);
Route::get('/sitemaps/san-pham.xml', [SitemapController::class, 'sanPham']);
Route::get('/sitemaps/chung.xml', [SitemapController::class, 'chung']);
Route::get('/robots.txt', [SitemapController::class, 'robotsTxt']);

// ===== DYNAMIC BOT CRAWLER & SEO PREVIEW ROUTES =====
Route::get('/chi-tiet-phu-tung/{code}', [SeoBotController::class, 'phuTung']);
Route::get('/so-do/{model}', [SeoBotController::class, 'soDo']);
Route::get('/chi-tiet-san-pham/{slug}', [SeoBotController::class, 'sanPham']);

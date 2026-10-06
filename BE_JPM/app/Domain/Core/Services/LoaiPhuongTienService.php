<?php

namespace App\Domain\Core\Services;

use App\Models\LoaiPhuongTien;
use Illuminate\Database\Eloquent\Collection;

class LoaiPhuongTienService
{
    public function index(): Collection
    {
        return LoaiPhuongTien::orderBy('id', 'desc')->get();
    }

    public function show(int $id): LoaiPhuongTien
    {
        return LoaiPhuongTien::findOrFail($id);
    }

    public function store(array $data): LoaiPhuongTien
    {
        return LoaiPhuongTien::create($data);
    }

    public function update(int $id, array $data): LoaiPhuongTien
    {
        $loaiPhuongTien = $this->timTheoId($id);
        $loaiPhuongTien->update($data);
        return $loaiPhuongTien;
    }

    public function destroy(int $id): bool
    {
        return $this->timTheoId($id)->delete();
    }
}

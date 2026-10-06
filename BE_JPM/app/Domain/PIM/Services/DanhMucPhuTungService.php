<?php

namespace App\Domain\PIM\Services;

use App\Models\DanhMucPhuTung;
use Illuminate\Database\Eloquent\Collection;

class DanhMucPhuTungService
{
    public function index(): Collection
    {
        return DanhMucPhuTung::orderBy('id', 'desc')->get();
    }

    public function show(int $id): DanhMucPhuTung
    {
        return DanhMucPhuTung::findOrFail($id);
    }

    public function store(array $data): DanhMucPhuTung
    {
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']) . '-' . time();
        }
        return DanhMucPhuTung::create($data);
    }

    public function update(int $id, array $data): DanhMucPhuTung
    {
        $danhMuc = $this->show($id);
        if (isset($data['name']) && $data['name'] !== $danhMuc->name) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']) . '-' . time();
        }
        $danhMuc->update($data);
        return $danhMuc;
    }

    public function destroy(int $id): bool
    {
        return $this->show($id)->delete();
    }
}

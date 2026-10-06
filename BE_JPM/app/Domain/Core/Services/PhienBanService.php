<?php

namespace App\Domain\Core\Services;

use App\Domain\Core\Models\PhienBan;
use Illuminate\Database\Eloquent\Collection;

class PhienBanService
{
    public function index(): Collection
    {
        return PhienBan::with('dongXe')->get();
    }

    public function show(int $id): PhienBan
    {
        return PhienBan::with('dongXe')->findOrFail($id);
    }

    public function store(array $data): PhienBan
    {
        return PhienBan::create($data);
    }

    public function update(int $id, array $data): PhienBan
    {
        $phienBan = $this->timTheoId($id);
        $phienBan->update($data);
        return $phienBan;
    }

    public function destroy(int $id): bool
    {
        return $this->timTheoId($id)->delete();
    }
}


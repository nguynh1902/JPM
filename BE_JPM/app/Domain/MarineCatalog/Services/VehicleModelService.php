<?php

namespace App\Domain\MarineCatalog\Services;

use App\Models\VehicleModel;
use Illuminate\Database\Eloquent\Collection;

class VehicleModelService
{
    public function index(): Collection
    {
        return VehicleModel::with('thuongHieu')->get();
    }

    public function show(int $id): VehicleModel
    {
        return VehicleModel::with('thuongHieu')->findOrFail($id);
    }

    public function store(array $data): VehicleModel
    {
        return VehicleModel::create($data);
    }

    public function update(int $id, array $data): VehicleModel
    {
        $dongXe = $this->timTheoId($id);
        $dongXe->update($data);
        return $dongXe;
    }

    public function destroy(int $id): bool
    {
        return $this->timTheoId($id)->delete();
    }
}

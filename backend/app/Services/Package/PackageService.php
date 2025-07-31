<?php

namespace App\Services\Package;

use App\Models\Package\Package;
use Illuminate\Support\Facades\DB;

class PackageService
{
    /**
     * Cria um novo pacote e associa exames a ele.
     *
     * @param array $data
     * @return Package 
     */
    public function create(array $data): Package
    {
        return DB::transaction(function () use ($data) {
            $package = Package::create([
                'name' => $data['name'],
                'observations' => $data['observations'] ?? null,
            ]);

            // Relaciona os exames ao pacote (tabela pivot)
            $package->exams()->attach($data['exams']);

            return $package->load('exams');
        });
    }

    /**
     * Retorna uma lista paginada de pacotes, com seus exames relacionados.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(array $filters = [])
    {
        return Package::query()
            ->with('exams')
            ->when($filters['name'] ?? null, fn($query, $name) =>
            $query->where('name', 'like', "%{$name}%"))
            ->latest()
            ->paginate($filters['per_page'] ?? 10);
    }

    /**
     * Busca um pacote pelo ID e carrega seus exames.
     *
     * @param int $id
     * @return Package|null 
     */
    public function findById(int $id): ?Package
    {
        return Package::with('exams')->find($id);
    }

    /**
     * Atualiza um pacote e sincroniza seus exames.
     *
     * @param int $id
     * @param array $data
     * @return Package|null
     */
    public function update(int $id, array $data): ?Package
    {
        return DB::transaction(function () use ($id, $data) {
            $package = Package::find($id);

            if (!$package) {
                return null;
            }

            // Atualiza os dados principais do pacote
            $package->update([
                'name' => $data['name'],
                'observations' => $data['observations'] ?? null,
            ]);

            // Sincroniza os exames relacionados
            $package->exams()->sync($data['exams']);

            return $package->load('exams');
        });
    }

    /**
     * Remove um pacote e seus relacionamentos com exames.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $package = Package::find($id);

        if (!$package) {
            return false;
        }

        // Remove os exames associados antes de deletar
        $package->exams()->detach();

        return $package->delete();
    }
}

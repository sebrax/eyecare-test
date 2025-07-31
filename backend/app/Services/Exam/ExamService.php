<?php

namespace App\Services\Exam;

use App\Enums\Exam\GroupEnum;
use App\Enums\Exam\LateralityEnum;
use App\Models\Exam\Exam;
use Illuminate\Database\Eloquent\Collection;

class ExamService
{
    /**
     * Cria um novo exame no banco de dados com os dados validados.
     *
     * @param array $data 
     * @return Exam 
     */
    public function create(array $data): Exam
    {
        return Exam::create($data);
    }

    /**
     * Retorna uma lista paginada de exames, ordenados por nome.
     *
     * @param int $perPage 
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(array $filters = [])
    {
        return Exam::query()
            ->when($filters['name'] ?? null, fn($query, $name) => $query->where('name', 'like', "%{$name}%"))
            ->when($filters['group'] ?? null, fn($query, $group) => $query->where('group', $group))
            ->when($filters['laterality'] ?? null, fn($query, $laterality) => $query->where('laterality', $laterality))
            ->latest()
            ->paginate($filters['per_page'] ?? 10);
    }

    /**
     * Busca um exame específico pelo seu ID.
     *
     * @param int $id
     * @return Exam|null 
     */
    public function findById($id): ?Exam
    {
        return Exam::find($id);
    }

    /**
     * Atualiza os dados de um exame existente.
     *
     * @param int $id
     * @param array $data
     * @return Exam|null
     */
    public function update(int $id, array $data): ?Exam
    {
        $exam = $this->findById($id);

        if (!$exam) {
            return null;
        }

        $exam->update($data);

        return $exam;
    }

    /**
     * Remove um exame do banco de dados.
     *
     * @param int
     * @return bool
     */
    public function delete(int $id): bool
    {
        $exam = $this->findById($id);

        if (!$exam) {
            return false;
        }

        return $exam->delete();
    }

    /**
     * Busca exames por IDs e retorna uma coleção ordenada.
     *
     * @param array $ids
     * @return Collection
     */
    public function getByIds(array $ids): Collection
    {
        return Exam::whereIn('id', $ids)->orderBy('name')->get();
    }

    /**
     * Retorna os dados de configuração necessários para a tela de exames.
     *
     * @return array
     */
    public function getConfigData(): array
    {
        return [
            'lateralityOptions' => LateralityEnum::descriptions(),
            'groupOptions' => GroupEnum::descriptions(),
        ];
    }
}

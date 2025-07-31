<?php

namespace App\Http\Controllers\Api\Package;

use App\Http\Controllers\Controller;
use App\Http\Requests\Package\PackageRequest;
use App\Http\Resources\Package\PackageResource;
use App\Services\Package\PackageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    use ApiResponse;

    protected PackageService $packageService;

    /**
     * Injeta o serviço de pacotes no controller.
     *
     * @param PackageService $packageService
     */
    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }

    /**
     * Lista os pacotes com seus exames relacionados.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['name', 'per_page']);

        $packages = $this->packageService->list($filters);

        return response()->json([
            'data' => PackageResource::collection($packages),
            'meta' => [
                'current_page' => $packages->currentPage(),
                'last_page' => $packages->lastPage(),
                'per_page' => $packages->perPage(),
                'total' => $packages->total(),
            ]
        ]);
    }
    /**
     * Cria um novo pacote e relaciona os exames.
     *
     * @param PackageRequest $request
     * @return JsonResponse
     */
    public function store(PackageRequest $request): JsonResponse
    {
        $package = $this->packageService->create($request->validated());

        return $this->success(new PackageResource($package), 'Pacote criado com sucesso.', 201);
    }

    /**
     * Retorna os dados de um pacote específico.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $package = $this->packageService->findById($id);

        if (!$package) {
            return $this->error('Pacote não encontrado.', 404);
        }

        return $this->success(new PackageResource($package), 'Pacote recuperado com sucesso.');
    }

    /**
     * Atualiza um pacote e seus exames.
     *
     * @param PackageRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(PackageRequest $request, int $id): JsonResponse
    {
        $package = $this->packageService->update($id, $request->validated());

        if (!$package) {
            return $this->error('Pacote não encontrado.', 404);
        }

        return $this->success(new PackageResource($package), 'Pacote atualizado com sucesso.');
    }

    /**
     * Deleta um pacote e desvincula seus exames.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->packageService->delete($id);

        if (!$deleted) {
            return $this->error('Pacote não encontrado.', 404);
        }

        return $this->success(null, 'Pacote removido com sucesso.');
    }
}

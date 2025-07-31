<?php

namespace App\Http\Controllers\Api\Exam;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exam\ExamRequest;
use App\Http\Requests\Exam\GeneratePdfRequest;
use App\Http\Resources\Exam\ExamResource;
use App\Services\Exam\ExamPdfService;
use App\Services\Exam\ExamService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    use ApiResponse;

    protected ExamService $examService;
    protected ExamPdfService $examPdfService;

    /**
     * Injeta os serviços de exames e PDF no controller.
     *
     * @param ExamService $examService
     * @param ExamPdfService $examPdfService
     */
    public function __construct(ExamService $examService, ExamPdfService $examPdfService)
    {
        $this->examService = $examService;
        $this->examPdfService = $examPdfService;
    }

    /**
     * Lista os exames de forma paginada.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['name', 'group', 'laterality', 'per_page']);
        $exams = $this->examService->list($filters);

        return response()->json([
            'data' => ExamResource::collection($exams),
            'meta' => [
                'current_page' => $exams->currentPage(),
                'last_page' => $exams->lastPage(),
                'per_page' => $exams->perPage(),
                'total' => $exams->total(),
            ],
            'config' => $this->examService->getConfigData(),
        ]);
    }
    /**
     * Armazena um novo exame no banco de dados.
     *
     * @param ExamRequest $request
     * @return JsonResponse
     */
    public function store(ExamRequest $request): JsonResponse
    {
        $exam = $this->examService->create($request->validated());

        return $this->success(new ExamResource($exam), 'Exame criado com sucesso.', 201);
    }

    /**
     * Retorna os dados de um exame específico.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $exam = $this->examService->findById($id);

        if (!$exam) {
            return $this->error('Exame não encontrado.', 404);
        }

        return $this->success(new ExamResource($exam), 'Exame recuperado com sucesso.');
    }

    /**
     * Atualiza os dados de um exame específico.
     *
     * @param ExamRequest $request 
     * @param int $id
     * @return JsonResponse
     */
    public function update(ExamRequest $request, int $id): JsonResponse
    {
        $exam = $this->examService->update($id, $request->validated());

        if (!$exam) {
            return $this->error('Exame não encontrado.', 404);
        }

        return $this->success(new ExamResource($exam), 'Exame atualizado com sucesso.');
    }

    /**
     * Remove um exame do banco de dados.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->examService->delete($id);

        if (!$deleted) {
            return $this->error('Exame não encontrado.', 404);
        }

        return $this->success(null, 'Exame removido com sucesso.');
    }

    /**
     * Gera o PDF da solicitação de exames.
     *
     * @param GeneratePdfRequest $request
     * @return Response
     */
    public function generatePdf(Request $request)
    {
        $groups = $request->input('groups', []);

        if (empty($groups)) {
            return response()->json(['message' => 'Nenhum exame informado.'], 422);
        }

        $pdf = $this->examPdfService->generatePdf($groups);

        return $pdf->download('solicitacao_exames.pdf');
    }
}

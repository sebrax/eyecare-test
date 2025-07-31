<?php

namespace App\Traits;

/**
 * Trait que padroniza as respostas JSON da API.
 */
trait ApiResponse
{
    /**
     * Retorna uma resposta JSON de sucesso com dados e mensagem.
     *
     * @param mixed  $data    
     * @param string $message 
     * @param int    $code
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data, string $message = '', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Retorna uma resposta JSON de erro com mensagem e código HTTP.
     *
     * @param string $message 
     * @param int    $code 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function error(string $message = '', int $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
        ], $code);
    }
}

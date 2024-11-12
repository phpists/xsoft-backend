<?php

namespace App\Http\Controllers\Traits;

trait HasJsonResponses
{
    /**
     * Возвращает данные
     * @param $data
     * @param int|null $httpCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseSuccess($data, ?int $httpCode = 200)
    {
        $response = [
            'code' => $httpCode,
            'response' => $data
        ];

        if (config('app.add_sql_debug') && isset($this->debugList)) {
            $response['_debug'] = $this->debugList;
        }

        return response()->json($response, $httpCode);
    }

    /**
     * Возвращает ошибку
     * @param $data
     * @param int|null $httpCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseError(string $message, array $errors = [], ?int $httpCode = 400)
    {
        $response = [
            'message' => $message,
            'errors' => $errors ? $errors : null,
        ];

        if (config('app.add_sql_debug') && isset($this->debugList)) {
            $response['_debug'] = $this->debugList;
        }

        return response()->json($response, $httpCode);
    }
}

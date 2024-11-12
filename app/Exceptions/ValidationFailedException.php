<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ValidationFailedException extends \Exception
{
    public function __construct(array $message = [], $code = 422, Throwable $previous = null)
    {
        parent::__construct(json_encode($message), $code, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json(
            [
                'message' => 'Ошибка валидации данных',
                'errors' => json_decode($this->getMessage(), true),
            ],
            $this->getCode()
        );
    }
}

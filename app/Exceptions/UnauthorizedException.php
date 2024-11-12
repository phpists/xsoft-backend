<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Http\Request;
use Throwable;

class UnauthorizedException extends \Exception
{
    public function __construct(string $message = null, $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(Request $request)
    {
        return response()->json(
            [
                'error' => [
                    'code' => $this->getCode(),
                    'message' => 'Unauthorized',
                    'errors' => $this->getMessage(),
                ],
            ],
            $this->getCode()
        );
    }
}

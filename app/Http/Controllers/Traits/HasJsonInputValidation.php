<?php

declare(strict_types=1);

namespace App\Http\Controllers\Traits;

use App\Exceptions\ValidationFailedException;
use Illuminate\Support\Facades\Validator;

trait HasJsonInputValidation
{
    /**
     * Validate input with rules
     * Throw BadRequest exception with error if not validated
     *
     * @param \Illuminate\Http\Request $request
     * @param array $rules
     * @return array validated data
     * @throws BadRequest
     */
    protected function validateInput($request, array $rules, $messages = [], $customAttributes = []): array
    {
        $validator = Validator::make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            throw new ValidationFailedException($validator->errors()->toArray());
        }

        return $validator->validated();
    }
}

<?php

namespace App\Exceptions;

use App\Http\Controllers\Traits\HasJsonResponses;
use App\Models\Interfaces\ModelDescriptionInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use HasJsonResponses;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        ValidationFailedException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            $previous = $e->getPrevious();
            if ($previous instanceof ModelNotFoundException) {
                $model = $previous->getModel();
                $modelObject = app()->make($model);
                if ($modelObject instanceof ModelDescriptionInterface) {
                    return $this->responseError($model::getEntityNotFoundDescription(), [], 404);
                }

                return $this->responseError('Item not found', [], 404);
            }
        });
    }
}

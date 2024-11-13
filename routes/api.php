<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

\App\Http\Controllers\Api\AuthController::routers();
\App\Http\Controllers\Api\ClientController::routers();
\App\Http\Controllers\Api\ProductController::routers();

//Route::middleware('auth:sanctum')->group(function () {
//    return 1;
//});

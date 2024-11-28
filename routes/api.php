<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

\App\Http\Controllers\Api\AuthController::routers();
\App\Http\Controllers\Api\ClientController::routers();
\App\Http\Controllers\Api\ProductController::routers();
\App\Http\Controllers\Api\ProductCategoryController::routers();
\App\Http\Controllers\Api\CompanyController::routers();
\App\Http\Controllers\Api\CompanyCategoryController::routers();
\App\Http\Controllers\Api\RoleController::routers();
\App\Http\Controllers\Api\StaffController::routers();
\App\Http\Controllers\Api\BrandController::routers();
\App\Http\Controllers\Api\SupplierController::routers();




<?php

use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::apiResource('recipes', RecipeController::class)->only(['index', 'store', 'update', 'destroy']);


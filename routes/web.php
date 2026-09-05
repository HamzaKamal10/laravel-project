<?php

declare(strict_types=1);

use App\Http\Controllers\IdeaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IdeaController::class, 'index']);
Route::post('/ideas', [IdeaController::class, 'store']);

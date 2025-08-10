<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PredictController;

Route::get('/', [PredictController::class, 'index'])->name('predict.index');

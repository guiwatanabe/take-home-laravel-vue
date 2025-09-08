<?php

use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamRequestsController;
use App\Http\Controllers\PackageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

Route::resource('exams', ExamController::class)->except(['create', 'edit']);
Route::resource('packages', PackageController::class)->except(['create', 'edit']);

Route::post('print', [ExamRequestsController::class, 'print']);

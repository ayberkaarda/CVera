<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::pattern('id', '[0-9]+');
Route::pattern('version', '[0-9]+');
Route::view('/', 'app');
Route::view('/analiz', 'app');
Route::view('/ilana-gore', 'app');
Route::view('/cv-olustur', 'app');
Route::post('/api/analyze', AnalysisController::class)->middleware('throttle:analysis');

Route::view('/hesabim', 'app')->name('login');
Route::view('/on-yazi', 'app');
Route::view('/portfolyo', 'app');
Route::view('/sablonlar', 'app');
Route::view('/rehber', 'app');
Route::view('/eklenti', 'app');
Route::get('/p/{slug}', [DocumentController::class, 'portfolio']);
Route::get('/api/account', [AccountController::class, 'state']);
Route::post('/api/register', [AccountController::class, 'register'])->middleware('throttle:account');
Route::post('/api/login', [AccountController::class, 'login'])->middleware('throttle:account');
Route::post('/api/logout', [AccountController::class, 'logout']);
Route::middleware(['auth', 'throttle:documents'])->group(function () {
    Route::get('/api/documents', [DocumentController::class, 'index']);
    Route::post('/api/documents', [DocumentController::class, 'save']);
    Route::put('/api/documents/{id}', [DocumentController::class, 'save']);
    Route::delete('/api/documents/{id}', [DocumentController::class, 'destroy']);
    Route::get('/api/documents/{id}/versions', [DocumentController::class, 'versions']);
    Route::post('/api/documents/{id}/versions/{version}', [DocumentController::class, 'restore']);
    Route::post('/api/documents/{id}/share', [DocumentController::class, 'share']);
});

Route::post('/api/ai', AiController::class)->middleware('throttle:ai');

Route::view('/asistan', 'app');

Route::view('/portfolyo-sablonlari', 'app');

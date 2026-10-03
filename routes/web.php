<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::get('/{any?}', function () {
    return view('rice-detector');
})->where('any', '^(?!api|v1|sanctum|auth/google|storage|images|css|js|build|manifest\.json|sw\.js|up).*$');


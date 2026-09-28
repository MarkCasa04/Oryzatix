<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any?}', function () {
    return view('rice-detector');
})->where('any', '^(?!api|sanctum|storage|images|css|js|build|manifest\.json|sw\.js|up).*$');


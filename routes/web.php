<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FiliereWebController;

Route::get('/', function () {
    return view('welcome');
});

// Route web pour les filières (interface web)
Route::resource("filiere", FiliereWebController::class);


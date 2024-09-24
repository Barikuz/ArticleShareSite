<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class,"setHomepage"]);

Route::get('/getUsers', [UserController::class,"getUsers"]);

Route::get('/getRoles', [UserController::class,"getRoles"]);

Route::get('/getPermissions', [UserController::class,"getPermissions"]);

Route::post('/manageRole', [UserController::class,"manageRole"]);


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

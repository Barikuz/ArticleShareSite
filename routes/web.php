<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\TextsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class,"setHomepage"]);

Route::get('/getUsers', [UserController::class,"getUsers"]);

Route::get('/getAuthorizedUserRoles', [UserController::class,"getAuthorizedUserRoles"]);

Route::get('/getAuthorizedUserPermissions', [UserController::class,"getAuthorizedUserPermissions"]);

Route::post('/manageRoles', [UserController::class,"manageRoles"]);

Route::post('/saveTexts', [TextsController::class,"saveText"]);

Route::get('/getTexts', [TextsController::class,"getTexts"]);

Route::get('/getUserTexts', [TextsController::class,"getUserTexts"]);


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

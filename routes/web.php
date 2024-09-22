<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = Auth::user();
    $permissions = null;
    $roles = null;
    if($user){
        $permissions = $user->getPermissionsViaRoles()->pluck('name');
        $roles = $user->getRoleNames();
    }
    return view('myViews.homepage',compact("user","permissions","roles"));
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

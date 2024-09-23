<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function setHomepage() {
        $user = Auth::user();
        $permissions = null;
        if($user){
            $permissions = $user->getPermissionsViaRoles()->pluck('name');
        }
        return view('myViews.homepage',compact("user","permissions"));
    }
}

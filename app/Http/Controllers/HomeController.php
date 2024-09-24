<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function setHomepage() {
        $user = Auth::user();

        return view('myViews.homepage',compact("user"));
    }
}

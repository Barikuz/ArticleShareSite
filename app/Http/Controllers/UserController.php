<?php

namespace App\Http\Controllers;

use App\Http\Requests\MyCustomRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getUsers()
    {
        return User::pluck('name',"id");
    }

    public function getAuthorizedUserPermissions()
    {
        $user = Auth::user();
        if($user){
            $permissions = $user->getPermissionsViaRoles()->pluck('name');
        }

        return $permissions;
    }
    public function getAuthorizedUserRoles()
    {
        $user = User::find(Auth::id());
        if($user->getRoleNames()){
            return $user->getRoleNames();
        }
    }

    public function manageRoles(MyCustomRequest $request)
    {
        $user = User::find($request->id);
        if($user->getRoleNames()->contains($request->type)){
            $user->removeRole($request->type);
            return "Deleted";
        }else{
            $user->assignRole($request->type);
            return "Added";
        }
    }

}

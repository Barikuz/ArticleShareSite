<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getUsers()
    {
        return User::pluck('name',"id");
    }

    public function getPermissions()
    {
        $user = Auth::user();
        if($user){
            $permissions = $user->getPermissionsViaRoles()->pluck('name');
        }

        return $permissions;
    }
    public function getRoles()
    {
        $user = User::find(Auth::id());
        if($user->getRoleNames()){
            return $user->getRoleNames();
        }
    }

    public function manageRole(Request $givenRoleAndUser)
    {
        $user = User::find($givenRoleAndUser->id);
        if($user->getRoleNames()->contains($givenRoleAndUser->type)){
            $user->removeRole($givenRoleAndUser->type);
            return "Deleted";
        }else{
            $user->assignRole($givenRoleAndUser->type);
            return "Added";
        }
    }

}

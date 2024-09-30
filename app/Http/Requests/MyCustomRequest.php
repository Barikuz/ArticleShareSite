<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class MyCustomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $requestType = $this->type;
        $user = Auth::user();

        if($requestType == "Admin" || $requestType == "Kullanıcı"){
            if($user->getPermissionsViaRoles()->contains("name","Assign Roles")){
                return true;
            }else{
                return false;
            }
        }
        else if($requestType == "saveText"){
            if($user->getPermissionsViaRoles()->contains("name","Create Texts")){
                return true;
            }else{
                return false;
            }
        }
        else if($requestType == "getTexts" || $requestType == "getUserTexts"){
            if($user->getPermissionsViaRoles()->contains("name","See Texts")){
                return true;
            }else{
                return false;
            }
        }
        else if($requestType == "editText"){
            if($user->getPermissionsViaRoles()->contains("name","Edit Texts")){
                return true;
            }else{
                return false;
            }
        }
        else if($requestType == "deleteText"){
            if($user->getPermissionsViaRoles()->contains("name","Delete Texts")){
                return true;
            }else{
                return false;
            }
        }

        abort(404);
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Bu işlemi gerçekleştirmek için yetkiniz yok.',
        ], 403));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}

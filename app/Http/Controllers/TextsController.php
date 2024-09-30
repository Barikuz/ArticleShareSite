<?php

namespace App\Http\Controllers;

use App\Http\Requests\MyCustomRequest;
use App\Models\Text;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TextsController extends Controller
{
    public function saveText(MyCustomRequest $request){
        $text = new Text();
        $text->user_id = $request->id;
        $text->text = $request->text;
        $text->save();

    }

    public function getTexts(){
        $texts = Text::with('getUser')->orderBy("created_at")->get();

        return response()->json($texts);
    }

    public function getUserTexts(){
        $texts = User::find(Auth::id())->getUserTexts()->with("getUser")->get();

        return response()->json($texts);
    }

    public function editText(MyCustomRequest $request){
        $text = Text::find($request->id);
        $text->text = $request->text;
        $text->save();
    }

    public function deleteText(MyCustomRequest $request){
        $text = Text::find($request->id);
        $text->delete();
    }


}

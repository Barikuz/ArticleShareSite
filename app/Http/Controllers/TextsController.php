<?php

namespace App\Http\Controllers;

use App\Models\Text;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TextsController extends Controller
{
    public function saveText(Request $request){
        $text = new Text();
        $text->user_id = $request->id;
        $text->text = $request->text;
        $text->save();

    }

    public function getTexts(Request $request){
        $texts = Text::with('getUser')->orderBy("id")->get();

        return response()->json($texts);
    }

    public function getUserTexts(){
        $texts = User::find(Auth::id())->getUserTexts()->with("getUser")->get();

        return response()->json($texts);
    }

    public function editText(Request $request){
        $text = Text::find($request->id);
        $text->text = $request->text;
        $text->save();
    }

    public function deleteText(Request $request){
        $text = Text::find($request->id);
        $text->delete();
    }


}

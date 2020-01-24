<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\Family;
use App\Album;
use App\Voice;
// use Validator;
use Auth;
// use DateTime;

class VoicesController extends Controller
{
  public function __construct(){
    $this->middleware('jwt.auth')->only('index', 'store','show', 'update', 'destroy');
  }

  // public function index()
  // {
  //   $user = User::find(Auth::id());
  //   if ($user->family_id){
  //     $family = Family::find($user->family_id);
  //     return Album::where('user_id', $family->user1)->orWhere('user_id', $family->user2)->orderBy('created_at', 'desc')->get();
  //   } else {
  //     return Album::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
  //   }
  // }

  public function store(Request $request)
  {
    if ( app()->isLocal() || app()->runningUnitTests() ) {
      // .env に APP_ENV=local (ローカル環境) または APP_ENV=testing (テスト環境) と書いてある場合
      // テスト環境, ローカル環境用の記述
      $file = $request->voice_name;
      if( !empty($file) ){
        $originalfilename = $file->getClientOriginalName();
        setlocale(LC_ALL, 'ja_JP.UTF-8');
        $filenamewoex = pathinfo($originalfilename, PATHINFO_FILENAME);
        $extension = pathinfo($originalfilename, PATHINFO_EXTENSION);
        $filename = date("YmdHis").$filenamewoex.md5(Auth::id()) . "." . $extension;
        
        $file->storeAs('public/uploadvoice', $filename);
        $voice                = new Voice;
        $voice->user_id       = Auth::id();
        $voice->voice_name    = $filename;
        $voice->album_name    = '';
        $voice->save(); 
        return $voice;
      } else {
        return;
      }
    } else {
      // .env に APP_ENV=production (本番環境) などと書いてあった場合
      // 本番環境用の記述
      $file = $request->voice_name;
      if( !empty($file) ){
        $originalfilename = $file->getClientOriginalName();
        setlocale(LC_ALL, 'ja_JP.UTF-8');
        $filenamewoex = pathinfo($originalfilename, PATHINFO_FILENAME);
        $extension = pathinfo($originalfilename, PATHINFO_EXTENSION);
        $filename = date("YmdHis").$filenamewoex.md5(Auth::id()) . "." . $extension;
        
        // $file->storeAs('public/uploadalbum', $filename);
        Storage::disk('s3')->putFileAs('uploadvoice', $file, $filename);
        $voice                = new Voice;
        $voice->user_id       = Auth::id();
        $voice->voice_name    = $filename;
        $voice->album_name    = '';
        $voice->save(); 
        return $voice;
      } else {
        return;
      }
    }
  }

  public function show(Photo $image)
  {
  }

  public function update(Request $request, Photo $image)
  {
  }

  public function destroy(Photo $image)
  {
  }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Photo;
use App\User;
use App\Family;
use App\Album;
// use Validator;
use Auth;
// use DateTime;



class AlbumsController extends Controller
{
  public function __construct(){
    $this->middleware('jwt.auth')->only('index', 'store','show', 'update', 'destroy');
    // $this->middleware('can:update,image')->only('update');
    // $this->middleware('can:delete,image')->only('destroy');
  }

  public function index()
  {
    return 'aaa';
  }

  public function store(Request $request)
  {
    if ( app()->isLocal() || app()->runningUnitTests() ) {
      // .env に APP_ENV=local (ローカル環境) または APP_ENV=testing (テスト環境) と書いてある場合
      // テスト環境, ローカル環境用の記述
      $file = $request->album_name;
      if( !empty($file) ){
        $originalfilename = $file->getClientOriginalName();
        setlocale(LC_ALL, 'ja_JP.UTF-8');
        $filenamewoex = pathinfo($originalfilename, PATHINFO_FILENAME);
        $extension = pathinfo($originalfilename, PATHINFO_EXTENSION);
        $filename = date("YmdHis").$filenamewoex.md5(Auth::id()) . "." . $extension;
        
        $file->storeAs('public/uploadalbum', $filename);
        $album                = new Album;
        $album->user_id       = Auth::id();
        $album->album_name    = $filename;
        $album->title         = $request->title;
        $album->cover_photo   = $request->cover_photo;
        $album->save(); 
        return $album;
      } else {
        return;
      }
    } else {
      // .env に APP_ENV=production (本番環境) などと書いてあった場合
      // 本番環境用の記述
      $file = $request->album_name;
      if( !empty($file) ){
        $originalfilename = $file->getClientOriginalName();
        setlocale(LC_ALL, 'ja_JP.UTF-8');
        $filenamewoex = pathinfo($originalfilename, PATHINFO_FILENAME);
        $extension = pathinfo($originalfilename, PATHINFO_EXTENSION);
        $filename = date("YmdHis").$filenamewoex.md5(Auth::id()) . "." . $extension;
        
        // $file->storeAs('public/uploadalbum', $filename);
        Storage::disk('s3')->put('/uploadpdf/'.$filename, $file);
        $album                = new Album;
        $album->user_id       = Auth::id();
        $album->album_name    = $filename;
        $album->title         = $request->title;
        $album->cover_photo   = $request->cover_photo;
        $album->save(); 
        return $album;
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

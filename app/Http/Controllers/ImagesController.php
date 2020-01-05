<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use App\Photo;
use App\User;
use App\Family;
// use Validator;
use Auth;
use DateTime;

class ImagesController extends Controller
{
  public function __construct(){
    $this->middleware('jwt.auth')->only('index', 'store','show', 'update', 'destroy');
    // $this->middleware('can:update,image')->only('update');
    // $this->middleware('can:delete,image')->only('destroy');
  }

  public function index()
  {
    if ( app()->isLocal() || app()->runningUnitTests() ) {
      // .env に APP_ENV=local (ローカル環境) または APP_ENV=testing (テスト環境) と書いてある場合
      // テスト環境, ローカル環境用の記述
      $user = User::find(Auth::id());
      if ($user->family_id){
        $family = Family::find($user->family_id);
        return Photo::where('user_id', $family->user1)->orWhere('user_id', $family->user2)->orderBy('created_at', 'asc')->get();
      } else {
        return Photo::where('user_id', Auth::id())->orderBy('created_at', 'asc')->get();
      }
    } else {
      // .env に APP_ENV=production (本番環境) などと書いてあった場合
      // 本番環境用の記述
    }
  }

  public function store(Request $request)
  {
    if ( app()->isLocal() || app()->runningUnitTests() ) {
      // .env に APP_ENV=local (ローカル環境) または APP_ENV=testing (テスト環境) と書いてある場合
      // テスト環境, ローカル環境用の記述
      // $file = $request->file('img_name');
      $file = $request->img_name;
      if( !empty($file) ){
        $originalfilename = $file->getClientOriginalName();
        setlocale(LC_ALL, 'ja_JP.UTF-8');
        $exif = exif_read_data($file);
        // if (isset($exif['EXIF']['DateTimeOriginal'])) {
        if (isset($exif['DateTimeOriginal'])) {
          $exifDatePattern = '/\A(?<year>\d{4}):(?<month>\d{1,2}):(?<day>\d{1,2}) (?<hour>\d{2}):(?<minute>\d{2}):(?<second>\d{2})\z/';
          // if (preg_match($exifDatePattern, $exif['EXIF']['DateTimeOriginal'], $matches)) {
          if (preg_match($exifDatePattern, $exif['DateTimeOriginal'], $matches)) {
            $dateTime = new DateTime(sprintf('%d-%d-%d %d:%d:%d',
            $matches['year'],
            $matches['month'],
            $matches['day'],
            $matches['hour'],
            $matches['minute'],
            $matches['second']
          ));
          $dateTime->format('Y-m-d H:i:s');
          }
        } else {
          $dateTime = new DateTime();
        }
        $filenamewoex = pathinfo($originalfilename, PATHINFO_FILENAME);
        $extension = pathinfo($originalfilename, PATHINFO_EXTENSION);
        $filename = date("YmdHis").$filenamewoex.md5(Auth::id()) . "." . $extension;
        $photo = \Image::make($file)
          ->resize(300, null, function ($constraint) {
          $constraint->aspectRatio();
        })->encode('jpg',80);
        // $file->storeAs('public/upload', $filename);
        Storage::disk('public')->put('/upload/'.$filename, $photo);
        $image                = new Photo;
        $image->user_id       = Auth::id();
        $image->img_name      = $filename;
        $image->img_comment_1 = $request->img_comment_1;
        $image->img_comment_2 = $request->img_comment_2;
        $image->taken         = $dateTime->format('Y-m-d H:i:s');
        $image->save(); 
        return $image;
      } else {
        return;
      }
    } else {
      // .env に APP_ENV=production (本番環境) などと書いてあった場合
      // 本番環境用の記述
      $file = $request->img_name;
      if( !empty($file) ){
        $originalfilename = $file->getClientOriginalName();
        setlocale(LC_ALL, 'ja_JP.UTF-8');
        $exif = exif_read_data($file);
        // if (isset($exif['EXIF']['DateTimeOriginal'])) {
        if (isset($exif['DateTimeOriginal'])) {
          $exifDatePattern = '/\A(?<year>\d{4}):(?<month>\d{1,2}):(?<day>\d{1,2}) (?<hour>\d{2}):(?<minute>\d{2}):(?<second>\d{2})\z/';
          // if (preg_match($exifDatePattern, $exif['EXIF']['DateTimeOriginal'], $matches)) {
          if (preg_match($exifDatePattern, $exif['DateTimeOriginal'], $matches)) {
            $dateTime = new DateTime(sprintf('%d-%d-%d %d:%d:%d',
            $matches['year'],
            $matches['month'],
            $matches['day'],
            $matches['hour'],
            $matches['minute'],
            $matches['second']
          ));
          $dateTime->format('Y-m-d H:i:s');
          }
        } else {
          $dateTime = new DateTime();
        }
        $filenamewoex = pathinfo($originalfilename, PATHINFO_FILENAME);
        $extension = pathinfo($originalfilename, PATHINFO_EXTENSION);
        $filename = date("YmdHis").$filenamewoex.md5(Auth::id()) . "." . $extension;
        // $photo = Image::make($file)
        //   ->resize(300, null, function ($constraint) {
        //   $constraint->aspectRatio();
        // });

        // $path = Storage::disk('s3')->putFileAs('/public/upload', $file, $filename ,'public');
        Storage::disk('s3')->putFileAs('/public/upload', $file, $filename ,'public');

        $image                = new Photo;
        $image->user_id       = Auth::id();
        $image->img_name      = $filename;
        $image->img_comment_1 = $request->img_comment_1;
        $image->img_comment_2 = $request->img_comment_2;
        $image->taken         = $dateTime->format('Y-m-d H:i:s');
        $image->save(); 
        return $image;
      } else {
        return;
      }
    }
  }

  public function show(Photo $image)
  {
    if ( app()->isLocal() || app()->runningUnitTests() ) {
      // .env に APP_ENV=local (ローカル環境) または APP_ENV=testing (テスト環境) と書いてある場合
      // テスト環境, ローカル環境用の記述
      $user = User::find(Auth::id());
      if ($user->family_id){
        $family = Family::find($user->family_id);
        if ($image->user_id === $family->user1 || $image->user_id === $family->user2) {
          return $image;
        }
      } else {
        if ($image->user_id === $user->id) {
          return $image;
        }
      }
    } else {
      // .env に APP_ENV=production (本番環境) などと書いてあった場合
      // 本番環境用の記述
    }

  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Photo  $image
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Photo $image)
  {
    // if ( app()->isLocal() || app()->runningUnitTests() ) {
    // .env に APP_ENV=local (ローカル環境) または APP_ENV=testing (テスト環境) と書いてある場合
    // テスト環境, ローカル環境用の記述
      $user = User::find(Auth::id());
      if ($user->family_id){
        $family = Family::find($user->family_id);
        if ($image->user_id === $family->user1 || $image->user_id === $family->user2) {
            $image->img_comment_1 = $request->img_comment_1;
            $image->img_comment_2 = $request->img_comment_2;
          $image->save();
          return $image;
        }
      } else {
        if ($image->user_id === $user->id) {
            $image->img_comment_1 = $request->img_comment_1;
            $image->img_comment_2 = $request->img_comment_2;
          $image->save();
          return $image;
        }
      }
    // } else {
      // .env に APP_ENV=production (本番環境) などと書いてあった場合
      // 本番環境用の記述
    // }

  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Photo  $image
   * @return \Illuminate\Http\Response
   */
  public function destroy(Photo $image)
  {
    if ( app()->isLocal() || app()->runningUnitTests() ) {
      $user = User::find(Auth::id());
      if ($user->family_id){
        $family = Family::find($user->family_id);
        if ($image->user_id === $family->user1 || $image->user_id === $family->user2) {
          $filename = $image->img_name;
          Storage::delete('public/upload/'.$filename);
          $image->delete();
        }
      } else {
        if ($image->user_id === $user->id) {
          $filename = $image->img_name;
          Storage::delete('public/upload/'.$filename);
          $image->delete();
        }
      }
    } else {
      $user = User::find(Auth::id());
      if ($user->family_id){
        $family = Family::find($user->family_id);
        if ($image->user_id === $family->user1 || $image->user_id === $family->user2) {
          $filename = $image->img_name;
          $disk = Storage::disk('s3');
          $disk->delete('public/upload/'.$filename);
          // Storage::delete('public/upload/'.$filename);
          $image->delete();
        }
      } else {
        if ($image->user_id === $user->id) {
          $filename = $image->img_name;
          $disk = Storage::disk('s3');
          $disk->delete('public/upload/'.$filename);
          // Storage::delete('public/upload/'.$filename);
          $image->delete();
        }
      }
    }
  }
}

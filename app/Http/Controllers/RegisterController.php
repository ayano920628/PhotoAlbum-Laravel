<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
// UUIDライブラリ
use Ramsey\Uuid\Uuid;
// Activationモデル
use App\Activation;
// Mailファサード
use Illuminate\Support\Facades\Mail;
use App\Mail\ActivationCreated;
use App\User;
use JWTAuth;
use DateTime;

class RegisterController extends Controller
{
  //
  public function register(Request $request) {
    $activation = new Activation;
    $activation->email = $request->email;
    $activation->code = Uuid::uuid4();
    $activation->save();
    Mail::to($activation->email)->send(new ActivationCreated($activation));
    return response()->json('aaa');
  }

  public function activate(Request $request) {
    $code = $request->code;
    if(!$this->checkCode($code)){
      return response()->json(
      ['errors' => ['key' => ['認証キーが無効です。']]]
    , 401);
    }
    $activation = Activation::where('code',$code)
    ->orderBy('created_at','desc')
    ->firstorFail();
    $date = new DateTime();
    $user = User::create([
      'name' => $request->name,
      'email' => $activation->email,
      'password' => bcrypt($request->password),
      'email_verified_at' => $date->format('Y-m-d H:i:s'),
    ]);
    $token = JWTAuth::fromUser($user);
    return response()->json(compact('token'));
  }

/**
* コードが有効かチェックする
*/

private function checkCode($code){
  $activation = Activation::where('code',$code)
  ->first();
  if(!$activation){
    return false;
  }
  $email = $activation->email;
  $latest = Activation::where('email',$email)
  ->orderBy('created_at', 'desc')
  ->first();
  $user = User::where('email',$email)->first();
  return $code === $latest->code && !$user;
}

}

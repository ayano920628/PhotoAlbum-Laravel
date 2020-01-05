<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// UUIDライブラリ
use Ramsey\Uuid\Uuid;
use App\Familyactivation;
// Mailファサード
use Illuminate\Support\Facades\Mail;
use App\Mail\FamilyactivationCreated;
use Auth;
use App\User;
use App\Family;
use JWTAuth;
use DateTime;

class FamilyregisterController extends Controller
{
  public function register(Request $request) {
    $activation = new Familyactivation;
    $activation->invited_by = Auth::id();
    $activation->email = $request->email;
    $activation->code = Uuid::uuid4();
    $activation->save();
    Mail::to($activation->email)->send(new FamilyactivationCreated($activation));
    // return $activation;
    return $activation;
  }


  public function activate(Request $request) {
    $code = $request->code;
    if(!$this->checkCode($code)){
      return response()->json(
      ['errors' => ['key' => ['認証キーが無効です。']]]
    , 401);
    }
    $activation = Familyactivation::where('code',$code)
    ->orderBy('created_at','desc')
    ->firstorFail();
    $date = new DateTime();
    $user = User::create([
      // 'user_type' => 2,
      'name' => $request->name,
      'email' => $activation->email,
      'password' => bcrypt($request->password),
      'email_verified_at' => $date->format('Y-m-d H:i:s'),
    ]);
    $family = Family::create([
      'user1' => $activation->invited_by,
      'user2' => $user->id,
    ]);
    $user1 = User::find($activation->invited_by);
    $user1->family_id = $family->id;
    $user1->save();

    $user2 = User::find($user->id);
    $user2->family_id = $family->id;
    $user2->user_type = 2;
    $user2->save();

    $token = JWTAuth::fromUser($user);
    return response()->json(compact('token'));
  }

  /**
  * コードが有効かチェックする
  */

  private function checkCode($code){
    $activation = Familyactivation::where('code',$code)
    ->first();
    if(!$activation){
      return false;
    }
    $email = $activation->email;
    $latest = Familyactivation::where('email',$email)
    ->orderBy('created_at', 'desc')
    ->first();
    $user = User::where('email',$email)->first();
    return $code === $latest->code && !$user;
  }
}

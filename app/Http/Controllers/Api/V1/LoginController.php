<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class LoginController extends BaseController {

  public function authenticate(Request $request) {
    $credentials = $request->validate([
      'email' => ['required', 'email'],
      'password' => ['required'],
    ]);
    if (Auth::attempt($credentials)) {
      $result = $request->session()->regenerate();
      return $result;
    } else {
      return [
        'false',
      ];
    }
  }
}
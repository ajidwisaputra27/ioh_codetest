<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
  /**
   * Get a JWT via given credentials.
   */
  public function login(Request $request)
  {
    // set validation rule
    $rules = [
      'email'    => 'required|email',
      'password' => 'required',
    ];
    // validate payload
    $validation = $request->validate($rules);

    if (!$token = auth()->attempt($validation)) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }

    return $this->generateResponse(['token' => $token], 'Login successfully', 200);
  }

  /**
   * Get the authenticated User.
   */
  public function me()
  {
    try {
      return $this->generateResponse(auth()->user(), 'Get profile user', 200);
    } catch (\Exception $e) {
      return $this->generateResponse($e->getMessage(), '', 500);
    }
  }

  /**
   * Log the user out (Invalidate the token).
   */
  public function logout()
  {
    try {
      auth()->invalidate(true);
      return $this->generateResponse('', 'Logout successfully', 200);
    } catch (\Exception $e) {
      return $this->generateResponse($e->getMessage(), '', 500);
    }
  }

  /**
   * Refresh a token.
   */
  public function refresh()
  {
    try {
      //get token
      $token = auth()->refresh(true);
      //set token new user
      auth()->setToken($token)->user();

      $response = ['token' => $token];
      return $this->generateResponse($response, 'Refresh token success', 200);
    } catch (\Exception $e) {
      $this->sendDiscord($e);
      return $this->generateResponse($e->getMessage(), '', 501);
    }
  }
}

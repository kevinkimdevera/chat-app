<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  public function registerAccount (RegisterRequest $request) {
    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password)
    ]);

    if (!!$user) {
      return [
        'created' => !!$user,
        'token' => $user->createToken('chat-app-token')->plainTextToken
      ];
    }

    return [
      'created' => false
    ];
  }

  public function attemptLogin (Request $request) {
    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      return response([
        'message' => 'These credentials do not match our records.'
      ], 404);
    }

    return response([
      'token' => $user->createToken('chat-app-token')->plainTextToken
    ], 201);
  }

  public function showUser (Request $request) {
    return new UserResource($request->user());
  }
}

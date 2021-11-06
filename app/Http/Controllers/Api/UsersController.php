<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class UsersController extends Controller
{
  public function findUser (Request $request) {
    $search = $request->search;

    $users = User::where(function ($query) use ($search) {
      return $query->orWhere('email', $search)
        ->orWhere('name', 'LIKE', "{$search}%");
    })
      ->where('id', '!=', $request->user()->id)
      ->get();

    return UserResource::collection($users);
  }
}

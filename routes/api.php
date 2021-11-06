<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatsController;
use App\Http\Controllers\Api\UsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::name('api.')->group(function () {
  Route::prefix('auth')->name('auth.')->group(function () {
    // REGISTER ACCOUNT
    Route::post('register', [AuthController::class, 'registerAccount'])->name('register');

    // LOGIN
    Route::post('login', [AuthController::class, 'attemptLogin'])->name('register');
  });

  Route::middleware(['auth:sanctum'])->group(function () {
    // USER DATA
    Route::prefix('auth')->name('auth.')->group(function () {
      Route::get('user', [AuthController::class, 'showUser'])->name('user.show');
    });

    // FIND USER
    Route::prefix('users')->name('users.')->group(function () {
      Route::get('search', [UsersController::class, 'findUser'])->name('search');
    });

    // CHAT
    Route::prefix('chats')->name('chats.')->group(function () {
      Route::get('/', [ChatsController::class, 'get'])->name('get');

      Route::get('/{code}', [ChatsController::class, 'show'])->name('show');

      Route::get('/{chat:code}/messages', [ChatsController::class, 'getMessages'])->name('messages.get');
      Route::post('/{chat:code}/message', [ChatsController::class, 'sendMessage'])->name('message.send');

      Route::post('new', [ChatsController::class, 'newChat'])->name('new');
    });
  });
});

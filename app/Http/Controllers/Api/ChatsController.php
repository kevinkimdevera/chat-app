<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChatMessageRequest;
use App\Http\Resources\ChatListResource;
use App\Http\Resources\ChatMessageResource;
use App\Http\Resources\ChatResource;

class ChatsController extends Controller
{
  public function generateCode()
  {
    $code = Str::random(14);

    if (Chat::whereCode($code)->exists()) {
      $code = $this->generateCode();
    }

    return $code;
  }

  public function get (Request $request) {
    $chats = $request->user()->chats()->with([
      'participants' => function ($query) use ($request) {
        return $query->whereNotIn('user_id', [$request->user()->id]);
      },

      'messages' => function ($query) use ($request) {
        return $query->whereHas('seen_statuses', function($_q) use ($request) {
          return $_q->where('user_id', $request->user()->id)
            ->whereNull('seen_at');
        });
      }
    ])
    ->get()
    ->sortByDesc('latest_message.created_at');

    return ChatListResource::collection($chats);
  }

  public function newChat (Request $request) {
    $chat = $request->user()
      ->chats()
      ->whereHas('participants', function($q) use ($request) {
        $q->whereIn('user_id', [
          $request->to
        ]);
      })
      ->first();

    if (!$chat) {
      $chat = Chat::create([
        'code' => $this->generateCode()
      ]);

      $chat->chat_participants()->createMany([
        [
          'user_id' => $request->user()->id,
          'accepted_at' => Carbon::now()
        ],
        [
          'user_id' => $request->to,
          'accepted_at' => null
        ]
      ]);
    }

    $chat->messages()->create([
      'sender_id' => $request->user()->id,
      'message' => $request->message
    ]);

    return [
      'code' => $chat->code,
      'sent' => true
    ];
  }

  public function show (Request $request, $code) {
    $chat = $request->user()->chats()->whereCode($code)->firstOrFail();

    return new ChatResource($chat);
  }

  public function getMessages (Request $request, Chat $chat) {
    return ChatMessageResource::collection($chat->messages);
  }

  public function sendMessage (ChatMessageRequest $request, Chat $chat) {
    $sent = $chat->messages()->create([
      'sender_id' => $request->user()->id,
      'message' => $request->message
    ]);

    return [
      'sent' => $sent
    ];
  }
}

<?php

namespace App\Observers;

use App\Events\NewChatMessage;
use App\Events\NewMessage;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use Carbon\Carbon;

class ChatMessageObserver
{
    /**
     * Handle the ChatMessage "created" event.
     *
     * @param  \App\Models\ChatMessage  $chatMessage
     * @return void
     */
    public function created(ChatMessage $chatMessage)
    {
      $participants = ChatParticipant::where('chat_id', $chatMessage->chat_id)
        ->get()
        ->pluck('user_id');

      foreach($participants as $p) {
        $chatMessage->seen_statuses()->create([
          'user_id' => $p,
          'seen_at' => ($p === $chatMessage->sender_id) ? Carbon::now() : null
        ]);

        broadcast(new NewMessage($p));
        broadcast(new NewChatMessage($chatMessage->chat->code))->toOthers();
      }
    }

    /**
     * Handle the ChatMessage "updated" event.
     *
     * @param  \App\Models\ChatMessage  $chatMessage
     * @return void
     */
    public function updated(ChatMessage $chatMessage)
    {
        //
    }

    /**
     * Handle the ChatMessage "deleted" event.
     *
     * @param  \App\Models\ChatMessage  $chatMessage
     * @return void
     */
    public function deleted(ChatMessage $chatMessage)
    {
        //
    }

    /**
     * Handle the ChatMessage "restored" event.
     *
     * @param  \App\Models\ChatMessage  $chatMessage
     * @return void
     */
    public function restored(ChatMessage $chatMessage)
    {
        //
    }

    /**
     * Handle the ChatMessage "force deleted" event.
     *
     * @param  \App\Models\ChatMessage  $chatMessage
     * @return void
     */
    public function forceDeleted(ChatMessage $chatMessage)
    {
        //
    }
}

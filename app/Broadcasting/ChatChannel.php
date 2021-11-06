<?php

namespace App\Broadcasting;

use App\Models\Chat;
use App\Models\User;

class ChatChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\User  $user
     * @return array|bool
     */
    public function join(User $user, $code)
    {
      return Chat::whereCode($code)
        ->whereHas('participants', function ($q) use ($user) {
          return $q->whereIn('user_id', [$user->id]);
        })
        ->exists();
    }
}

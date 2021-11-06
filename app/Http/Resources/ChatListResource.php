<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
      return [
        'code' => $this->code,
        'latest_message' => new ChatMessageResource($this->latest_message),
        'interlocutor' => new UserResource($this->participants[0]),
        'unseen_messages_count' => $this->messages->count()
      ];
    }
}

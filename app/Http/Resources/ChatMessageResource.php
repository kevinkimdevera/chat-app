<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
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
          'sender' => new UserResource($this->sender),
          'message' => $this->message,
          'sent' => $this->created_at->format('h:i a'),
          'seen' => $this->seen_statuses()->where('user_id', $request->user()->id)->first()->seen_at
        ];
    }
}

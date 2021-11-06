<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
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
        'participants' => UserResource::collection($this->participants),
        'accepted' => $this->chat_participants()->where('user_id', $request->user()->id)->first()->accepted
      ];
    }
}

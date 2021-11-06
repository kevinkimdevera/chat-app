<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'code',
  ];

  public function chat_participants () {
    return $this->hasMany(
      ChatParticipant::class,
      'chat_id',
      'id'
    );
  }

  public function participants () {
    return $this->hasManyThrough(
      User::class,
      ChatParticipant::class,
      'chat_id', // FK => chat_participants <- chats
      'id', // FK => chats
      'id', // LK => users
      'user_id' // LK => chat_participants -> users
    );
  }

  public function messages () {
    return $this->hasMany(
      ChatMessage::class,
      'chat_id',
      'id'
    );
  }

  public function latest_message () {
    return $this->hasOne(
      ChatMessage::class,
      'chat_id',
      'id'
    )->latestOfMany();
  }
}

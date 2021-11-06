<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
  use HasFactory;

  protected $fillable = [
    'chat_id',
    'sender_id',
    'message'
  ];

  public function chat () {
    return $this->belongsTo(Chat::class, 'chat_id', 'id');
  }

  public function sender () {
    return $this->belongsTo(User::class, 'sender_id', 'id');
  }

  public function seen_statuses() {
    return $this->hasMany(
      ChatMessageSeenStatus::class,
      'message_id',
      'id'
    );
  }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessageSeenStatus extends Model
{
  use HasFactory;

  protected $fillable = [
    'message_id',
    'user_id',
    'seen_at'
  ];

  protected $casts = [
    'seen_at' => 'datetime'
  ];

  public function getSeenAttribute () {
    return !!$this->seen_at;
  }
}

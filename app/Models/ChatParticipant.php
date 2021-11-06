<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
  use HasFactory;

  protected $fillable = [
    'chat_id',
    'user_id',
    'accepted_at'
  ];

  public function getAcceptedAttribute () {
    return !!$this->accepted_at;
  }
}

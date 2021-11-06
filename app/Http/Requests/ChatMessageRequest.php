<?php

namespace App\Http\Requests;

use App\Models\Chat;
use Illuminate\Foundation\Http\FormRequest;

class ChatMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
      $code = $this->code;
      $sender = $this->sender;

      return Chat::whereCode($code)
        ->whereHas('participants', function ($q) use ($sender) {
          return $q->whereIn('user_id', [$sender]);
        })
        ->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      return [
        'message' => [
          'required',
          'string'
        ]
      ];
    }
}

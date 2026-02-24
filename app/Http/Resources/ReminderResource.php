<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReminderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'contact_id' => $this->contact_id,
            'type' => $this->type,
            'message' => $this->message,
            'remind_at' => $this->remind_at->toIso8601String(),
            'is_sent' => $this->is_sent,
            'sent_at' => $this->sent_at?->toIso8601String(),
        ];
    }
}

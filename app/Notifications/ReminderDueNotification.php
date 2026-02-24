<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ReminderDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $contactName,
        public string $remindAt
    ) {}

    public function via(object $notifiable): array
    {
        return ['broadcast'];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'message' => "You haven't contacted {$this->contactName} in a while. Reminder: {$this->remindAt}",
        ]);
    }
}

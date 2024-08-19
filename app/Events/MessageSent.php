<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Message $message
    ) {}

    public function via($notifiable): array
    {
        return ['broadcast'];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'sender' => $this->message->sender->only(['id', 'name', 'username', 'profile_photo_url']),
            'receiver' => $this->message->receiver->only(['id', 'name', 'username', 'profile_photo_url']),
            'conversation_id' => $this->message->conversation_id,
            'receiverId' => $this->message->receiver_id,
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('messages.'.$this->message->receiver_id);
    }
}

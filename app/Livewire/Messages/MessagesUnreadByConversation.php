<?php

namespace App\Livewire\Messages;

use App\Models\Conversation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class MessagesUnreadByConversation extends Component
{
    public $conversation;

    public $unreadCount;

    public function getListeners(): array
    {
        $auth_id = auth()->user()->id;

        return [
            "echo-private:messages.{$auth_id},.message.sent" => 'notifyMessage',
        ];
    }

    public function mount(Conversation $conversation): void
    {
        $this->conversation = $conversation;
        $this->unreadCount = $this->conversation->unreadMessagesCount();
    }

    public function notifyMessage($event): void
    {
        if ($event['conversation_id'] == $this->conversation->id) {
            $this->unreadCount = $this->conversation->unreadMessagesCount();
        }

    }

    #[On('read-conversation')]
    public function updatedUnreadCount($conversationId): void
    {
        if ($conversationId == $this->conversation->id) {
            $this->unreadCount = $this->conversation->unreadMessagesCount();
        }
    }

    public function render(): View
    {
        return view('livewire.messages.messages-unread-by-conversation');
    }
}

<?php

namespace App\Livewire\Messages;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class MessagesUnread extends Component
{
    public function getListeners(): array
    {
        $auth_id = auth()->user()->id;

        return [
            "echo-private:messages.{$auth_id},.message.sent" => 'notifyNewMessage',
        ];
    }

    public function notifyNewMessage($event): void
    {
        if ($event['receiverId'] == auth()->id()) {
            $this->render();
        }
    }

    #[On('read-conversation')]
    public function updatedUnreadCount(): void
    {
        $this->render();
    }

    public function render(): View
    {
        return view('livewire.messages.messages-unread', [
            'countUnreadMessages' => auth()->user()->countUnreadMessages(),
        ]);
    }
}

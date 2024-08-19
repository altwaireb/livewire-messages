<?php

namespace App\Livewire\Messages;

use App\Models\Conversation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class MessageList extends Component
{
    public $selectedConversation;

    public $receiver;

    public function setSelectedConversation(Conversation $conversation): void
    {
        $this->selectedConversation = $conversation;
        $this->setReceiver();
        $this->dispatch('selected-conversation', $this->selectedConversation);
    }

    #[On('search-select-conversation')]
    public function searchSelectConversation(Conversation $conversation): void
    {
        $this->render();
        $this->setSelectedConversation($conversation);
    }

    public function setReceiver(): void
    {
        if (! empty($this->selectedConversation)) {
            $this->receiver = $this->selectedConversation->getReceiver();
        }
    }

    public function render(): View
    {
        return view('livewire.messages.message-list', [
            'conversations' => auth()->user()->conversations()->latest()->get(),
        ]);
    }
}

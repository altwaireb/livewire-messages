<?php

namespace App\Livewire\Messages;

use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class MessageBox extends Component
{
    public $selectedConversation;

    public $loadedMessages;

    public int $paginate_var = 10;

    #[Validate('required', message: 'Please write your message ...')]
    #[Validate('min:1', message: 'This message is too short')]
    public ?string $body = '';

    public function getListeners(): array
    {
        $auth_id = auth()->user()->id;

        return [
            "echo-private:messages.{$auth_id},.message.sent" => 'notifyMessage',
        ];
    }

    public function mount(): void
    {
        $this->loadMessages();
    }

    #[On('selected-conversation')]
    public function updateSelectedConversation(Conversation $conversation): void
    {
        $this->reset();
        $this->resetErrorBag();
        $this->selectedConversation = $conversation;
        $this->loadMessages();
    }

    public function notifyMessage($event): void
    {
        if (! empty($this->selectedConversation)) {
            if ($event['conversation_id'] == $this->selectedConversation->id) {
                if (isset($event['id'])) {
                    $newMessage = Message::find($event['id']);

                    //mark as read
                    $newMessage->read_at = now();
                    $newMessage->save();

                    MessageRead::dispatch($newMessage);

                    $this->dispatch('read-conversation', $newMessage->conversation_id);

                    //push message
                    $this->loadedMessages->push($newMessage);
                    $this->dispatch('scroll-bottom');

                }
            }
        }
    }

    public function loadMessages()
    {
        if (! empty($this->selectedConversation)) {
            $userId = auth()->id();
            //get count
            $count = Message::where('conversation_id', $this->selectedConversation->id)
                ->where(function ($query) use ($userId) {
                    $query->where('sender_id', $userId)
                        ->whereNull('sender_deleted_at');
                })->orWhere(function ($query) use ($userId) {
                    $query->where('receiver_id', $userId)
                        ->whereNull('receiver_deleted_at');
                })
                ->count();

            // mark message belonging to receiver as read
            $markMessages = Message::where('conversation_id', $this->selectedConversation->id)
                ->where('receiver_id', $userId)
                ->whereNull('read_at')
                ->get();
            if ($markMessages && $markMessages->count() > 0) {
                $markMessages->each(function ($message) {
                    $message->update(['read_at' => now()]);
                    MessageRead::dispatch($message);
                });
            }

            $this->dispatch('read-conversation', $this->selectedConversation->id);

            // get Messages
            $this->loadedMessages = Message::with('sender:id,name,username', 'receiver:id,name,username')
                ->where('conversation_id', $this->selectedConversation->id)
                ->where(function ($query) use ($userId) {
                    return $query->where('sender_id', $userId)
                        ->where('receiver_id', $this->selectedConversation->getReceiver()->id)
                        ->whereNull('sender_deleted_at');
                })->orWhere(function ($query) use ($userId) {
                    return $query->where('receiver_id', $userId)
                        ->where('sender_id', $this->selectedConversation->getReceiver()->id)
                        ->whereNull('receiver_deleted_at');
                })
                ->skip($this->paginate_var - $count)
                ->take($this->paginate_var)
                ->get();

            return $this->loadedMessages;
        }

        return $this->loadedMessages;
    }

    public function loadMore(): void
    {
        $this->paginate_var += 10;

        //call loadMessages()
        $this->loadMessages();

        //update the chat height
        $this->dispatch('update-conversation-height');
    }

    public function submit(): void
    {
        $this->validate();

        $createdMessage = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $this->selectedConversation->getReceiver()->id,
            'body' => $this->body,
        ]);
        //update Conversation model
        $this->selectedConversation->updated_at = now();
        $this->selectedConversation->save();

        $this->reset('body');
        //push the new message
        $this->loadedMessages->push($createdMessage);
        //scroll bottom
        $this->dispatch('update-conversation-height');
        $this->dispatch('scroll-bottom');

        // send Event
        MessageSent::dispatch($createdMessage);
    }

    public function render(): View
    {
        return view('livewire.messages.message-box');
    }
}

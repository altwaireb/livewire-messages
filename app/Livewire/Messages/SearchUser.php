<?php

namespace App\Livewire\Messages;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class SearchUser extends Component
{
    use WithoutUrlPagination, WithPagination;

    public $search = '';

    public int $amount = 2;

    public function selectUser($userId)
    {
        $authId = Auth::id();
        $conversation = Conversation::where('sender_id', $authId)
            ->where('receiver_id', $userId)
            ->orWhere('sender_id', $userId)
            ->where('receiver_id', $authId)->first();
        if (empty($conversation)) {
            $conversation = Conversation::create([
                'sender_id' => $authId,
                'receiver_id' => $userId,
            ]);
        }

        //        dd($conversation->id);

        $this->dispatch('search-select-conversation', $conversation->id);

    }

    public function lodeMore(): void
    {
        $this->amount += 2;
    }

    public function clean(): void
    {
        $this->reset();
    }

    public function render(): View
    {

        $users = collect();

        if (strlen($this->search > 0)) {
            $authId = Auth::id();

            $users = User::where(function ($query) {
                $query->where('name', 'like', '%'.trim($this->search).'%')
                    ->orWhere('username', 'like', '%'.trim($this->search).'%');
            })->where('id', '!=', $authId)
                ->select('id', 'name', 'username')
                ->paginate($this->amount);
        }

        return view('livewire.messages.search-user', compact('users'));
    }
}

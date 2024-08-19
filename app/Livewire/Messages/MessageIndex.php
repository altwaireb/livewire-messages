<?php

namespace App\Livewire\Messages;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class MessageIndex extends Component
{
    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.messages.message-index');
    }
}

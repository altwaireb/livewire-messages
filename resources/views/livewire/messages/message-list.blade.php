<div
        class="flex flex-col py-8 pl-6 pr-4 w-72 bg-white dark:bg-gray-800 dark:bg-gradient-to-tl dark:from-gray-700/50 dark:via-transparent flex-shrink-0">
    <div class="flex flex-row items-center justify-center h-12 w-full">
        <div
                class="flex items-center justify-center rounded-2xl text-primary-700 bg-primary-100 dark:bg-gray-900 dark:text-primary-400 h-10 w-10"
        >
            <a href="{{ route('messages') }}" class="no-underline">
                <x-icons.chat class="w-6 h-6"/>
            </a>
        </div>
        <div class="ml-2 font-bold text-2xl dark:text-gray-200">
            <a href="{{ route('messages') }}" class="no-underline lg:block hidden">{{ __('Messages') }}</a>
        </div>
    </div>

{{--    <livewire:messages.message-search />--}}
    <livewire:messages.search-user />
    <div class="h-44 block">
        @if(!empty($receiver))
            <div
                    class="flex flex-col items-center bg-indigo-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 mt-4 w-full py-6 px-4 rounded-lg"
            >
                <div class="h-20 w-20 rounded-full overflow-hidden">
                    <img
                            src="{{ $receiver->profile_photo_url }}"
                            alt="{{ $receiver->name }}"
                            class="h-full w-full object-cover"
                    />
                </div>
                <div class="text-sm font-semibold mt-2 dark:text-gray-200">{{ $receiver->name }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{'@'. $receiver->username }}</div>
            </div>
        @endif
    </div>


    <div class="flex flex-col mt-8">
        <div class="flex flex-row items-center justify-between text-xs">
            <span class="font-bold dark:text-gray-300">Active Conversations</span>
            <livewire:messages.messages-unread />
        </div>
        <div class="flex flex-col space-y-1 mt-4 -mx-2 h-48 px-3 gap-y-2 overflow-y-auto scrollbar-light">
            @forelse($conversations as $key => $conversation)
                <button
                        id="conversation-{{$conversation->id}}"
                        wire:key="'selected_conversation'.{{$conversation->id}}"
                        wire:click="setSelectedConversation({{$conversation->id}})"

                        @class([
                            'flex flex-row items-center rounded-xl p-2',
                            'hover:bg-gray-100 dark:bg-primary-700 dark:hover:bg-primary-800' => !empty($this->selectedConversation?->id) && $this->selectedConversation?->id == $conversation->id,
                            'hover:bg-gray-400 dark:bg-gray-700 dark:hover:bg-gray-900' => $this->selectedConversation?->id != $conversation->id,
                            ])
                >
                    <!-- avatar  -->
                    <img class="flex h-8 w-8 rounded-full object-cover"
                         src="{{ $conversation->getReceiver()->profile_photo_url }}"
                         alt="{{ $conversation->getReceiver()->name }}"/>
                    <span class="flex-1 text-start ms-2 text-sm font-semibold dark:text-gray-50">{{ $conversation->getReceiver()->shortName(14) }}</span>
                    <span class="flex-none">
                        <livewire:messages.messages-unread-by-conversation
                                :conversation="$conversation"
                                :key="'unread_by_conversation'.$conversation->id"
                        />
                    </span>
                </button>
            @empty
            @endforelse
        </div>
    </div>
</div>
<div x-data="{
        showSearch: false,
        handleOpen(event) {
            this.showSearch = true;
        },
        handleClose(event) {
            this.showSearch = false;
            $wire.clean();
        }
     }">
    <button
            @click="showSearch = true"
            @keydown.slash.window="handleOpen()"
            @keydown.escape.window="handleClose()"
            type="button"
            class="flex w-full space-x-3 py-2 px-4 my-2 text-gray-700 dark:text-gray-400 outline-none border rounded-lg border-primary-200 dark:border-primary-700"
    >
        <x-icons.search class="w-6 h-6"/>
        <span>{{ __('Search') }}</span>
    </button>

    <div x-show="showSearch" x-on:search-select-conversation.window="handleClose()" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex justify-center p-4 sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div
                            @click.outside="handleClose()"
                            x-trap="showSearch"
                            class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4"
                    >
                        <input
                                wire:model.live.throttle.500ms="search"
                                type="text"
                                placeholder="Search for a user..."
                                class="flex w-full border rounded-xl focus:outline-none focus:border-primary-300 dark:focus:border-primary-600 pl-4 h-10 dark:bg-gray-800 dark:text-gray-100 placeholder:text-gray-700 dark:placeholder:text-gray-400"
                        />
                        @if(!empty($search))
                            <ul
                                    class="relative z-10 mt-3 flex max-h-72 w-full flex-col overflow-hidden overflow-y-auto scrollbar-light border-primary-300 bg-primary-100 py-1.5 dark:border-primary-700 dark:bg-gray-800 rounded-xl border"
                            >
                                @forelse($users as $key => $user)
                                    <li wire:click="selectUser({{ $user->id }})" class="inline-flex cursor-pointer justify-between items-center gap-6 bg-primary-100 px-4 py-2 text-sm text-primary-700 hover:bg-primary-800/5 hover:text-black focus-visible:bg-primary-800/5 focus-visible:text-black focus-visible:outline-none dark:bg-gray-800 dark:text-primary-300 dark:hover:bg-primary-100/5 dark:hover:text-white dark:focus-visible:bg-primary-100/10 dark:focus-visible:text-white">
                                        <div class="flex items-center gap-2">
                                            <img class="size-8 rounded-full" src="{{ $user->profile_photo_url }}"
                                                 alt="" aria-hidden="true"/>
                                            <!-- Label -->
                                            <div class="flex flex-col">
                                                <span>{{ $user->name }}</span>
                                                <span class="text-xs">{{ '@'.$user->username }}</span>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li class="inline-flex cursor-pointer justify-between items-center gap-6 bg-primary-100 px-4 py-2 text-sm text-primary-700 hover:bg-primary-800/5 hover:text-black focus-visible:bg-primary-800/5 focus-visible:text-black focus-visible:outline-none dark:bg-primary-800 dark:text-primary-300 dark:hover:bg-primary-100/5 dark:hover:text-white dark:focus-visible:bg-primary-100/10 dark:focus-visible:text-white">
                                        <span>{{ __('No result ...') }}</span>
                                    </li>
                                @endforelse
                            </ul>
                            @if(!empty($users) && count($users) > 0 && $users->total() > 0 && $users->count() < $users->total())
                                <div class="pt-4 text-xs text-primary-600 dark:text-primary-400">
                                    <button
                                            type="button"
                                            wire:click="lodeMore"
                                            class="bg-transparent"
                                    >{{ __('Load More ...') }}</button>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

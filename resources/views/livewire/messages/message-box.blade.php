<div
        class="flex flex-col flex-auto flex-shrink-0 rounded-2xl bg-gray-100 dark:bg-gray-800 h-full p-4"
>
    @if(!empty($selectedConversation))
        <div

                x-data="{
                height:0,
                scroll: () => { $refs.conversation.scrollTo(0, $refs.conversation.scrollHeight);}
                }"

                x-ref="conversation"

                x-init="
                height = $refs.conversation.scrollHeight;
                scroll();
            "

                @scroll.$refs.conversation="
                scropTop = $el.scrollTop;
                if (scropTop <= 0){
                    $wire.loadMore();
                    $nextTick(() => { $refs.conversation.scrollTop = 0 });
                }
            "

                x-on:update-conversation-height.window="
                    newHeight = $refs.conversation.scrollHeight;
                    oldHeight = height;
                    $nextTick(() => { $refs.conversation.scrollTop = newHeight - oldHeight });
                    height = newHeight;
            "

                x-on:scroll-bottom.window="
                scrollable = $refs.conversation.scrollHeight;
                $nextTick(() => { $refs.conversation.scrollTop = scrollable });
                height = scrollable;
            "

                class="flex flex-col h-full overflow-x-auto mb-4 scrollbar-light"

        >
            <div class="flex w-full flex-col gap-4 pr-2">
                @forelse($loadedMessages as $key => $message)
                    @if($message->sender->id != auth()->id())
                        <!-- Receiver -->
                        <div
                                wire:key="message-{{$message->id}}"
                                class="flex items-end gap-2"
                        >
                            <img class="size-8 rounded-full object-cover"
                                 src="{{ $message->sender->profile_photo_url }}"
                                 alt="{{ $message->sender->name }} avatar"/>
                            <div class="mr-auto flex max-w-[70%] flex-col gap-2 rounded-r-xl rounded-tl-xl bg-gray-200 p-4 text-gray-700 md:max-w-[60%] dark:bg-gray-700 dark:text-gray-300">
                                <span class="font-semibold text-black dark:text-white">{{ $message->sender->name }}</span>
                                <div class="text-sm">
                                    {{ $message->body }}
                                </div>
                                <span class="ml-auto text-xs">{{ $message->created_at?->shortAbsoluteDiffForHumans() }}</span>
                            </div>
                        </div>
                    @else
                        <!-- Sender -->
                        <div
                                x-data="{markAsRead: @js($message->is_read) }"

                                x-init="
                            Echo.private('messages.{{Auth()->User()->id}}')
                            .listen('.message.read', event => {
                             if (event.id == @js($message->id)){
                                 markAsRead = true;
                             }
                            });
                            "

                                wire:key="message-{{$message->id}}"
                                class="flex items-end gap-2"
                        >
                            <div class="ml-auto flex max-w-[70%] flex-col gap-2 rounded-l-xl rounded-tr-xl bg-primary-600 p-4 text-sm text-end text-gray-100 md:max-w-[60%] dark:bg-primary-600 dark:text-gray-50">
                                {{ $message->body }}
                                <div class="flex flex-row justify-between items-center gap-x-5">
                                    <!-- icon mark Read  -->
                                    <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="currentColor" viewBox="0 0 16 16"
                                            class="w-5 h-5"
                                            :class="markAsRead ? 'text-gray-800 dark:text-gray-100' : 'text-gray-100 dark:text-gray-400'"
                                    >
                                        <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0"/>
                                        <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708"/>
                                    </svg>
                                    <span class="text-xs">{{ $message->created_at?->shortAbsoluteDiffForHumans() }}</span>
                                </div>
                            </div>
                            <img
                                    class="size-8 rounded-full object-cover"
                                    src="{{ $message->sender->profile_photo_url }}"
                                    alt="{{ $message->sender->name }} avatar"
                            />
                        </div>
                    @endif
                @empty
                @endforelse
            </div>
        </div>
        <div

        >
            <form
                    wire:submit="submit"
                    class="flex flex-row items-center h-16 rounded-xl bg-white dark:bg-gray-700 w-full px-4">
                <div>
                    <button
                            class="flex items-center justify-center text-gray-400 dark:text-gray-300 hover:text-gray-600 dark:hover:text-gray-100"
                    >
                        <x-icons.attach :key="'attach'" class="h-5 w-5"/>
                    </button>
                </div>
                <div
                        x-data="{
                        showEmojis:false,
                        searchEmoji: '',
                        itemEmojis: [],
                        mainEmojis: [],
                        resetItemsEmojis(){
                        this.itemEmojis = this.mainEmojis;
                       },
                        addEmoji(emoji){
                            $wire.body = $wire.body + emoji;
                            this.handleClose();
                        },
                        handleClose(){
                            this.showEmojis = false;
                            var bodyInput = $refs.inputBody;
                            bodyInput.focus();
                            bodyInput.setSelectionRange(bodyInput.value.length, bodyInput.value.length);
                            this.searchEmoji = '';
                            this.resetItemsEmojis();
                        },
                        async searchForEmoji(){
                           if (this.searchEmoji != '')
                           {
                               var query = this.searchEmoji.toLowerCase();
                               var url = `https://emoji-api.com/emojis?search=${query}&access_key=224706922607d922a5836615540de8f559fae716`;

                               this.itemEmojis = await (await fetch(url)).json();
                           }else {
                               this.resetItemsEmojis();
                           }
                       }

                    }"

                        x-init="
                    itemEmojis = await (await fetch('https://emoji-api.com/emojis?access_key=224706922607d922a5836615540de8f559fae716')).json();
                    mainEmojis = await itemEmojis;
                    "

                        class="flex-grow ms-4"
                >
                    <div class="relative w-full">
                        <input
                                wire:model="body"
                                x-ref="inputBody"
                                placeholder="@error('body') {{ $message }} @enderror"
                                type="text"
                                class="flex w-full border rounded-xl focus:outline-none focus:border-primary-300 dark:focus:border-primary-600 pl-4 h-10 dark:bg-gray-800 dark:text-gray-100 placeholder:text-danger-700 dark:placeholder:text-danger-300"
                        />

                        <button

                                @click="showEmojis = true"
                                type="button"
                                class="absolute flex items-center justify-center h-full w-12 right-0 top-0 text-gray-400 dark:text-gray-300 hover:text-gray-600 dark:hover:text-gray-100"
                        >
                            <x-icons.emoji :key="'emoji'" class="w-6 h-6"/>
                        </button>
                        <div
                                x-show="showEmojis"
                                @click.outside="handleClose()"
                                class="origin-bottom gap-x-1.5 absolute overflow-x-auto scrollbar-light right-0 bottom-8 z-10 mt-2 w-56  h-52 bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700">

                            <input
                                    x-model.debounce="searchEmoji"
                                    x-ref="inputSearch"
                                    @keyup="searchForEmoji()"
                                    type="text"
                                    class="flex w-52 m-auto mt-2 h-6 border rounded-lg focus:outline-none focus:border-primary-300 dark:focus:border-primary-600 dark:bg-gray-800 dark:text-gray-100"
                            />
                            <ul
                                    x-ref="emojis"

                                    class="border-none px-2 py-1.5 grid grid-cols-4 md:grid-cols-6 lg:grid-cols-6 text-2xl text-gray-700 dark:text-gray-200"
                            >
                                <template x-for="emoji in itemEmojis">
                                    <li class="flex">
                                        <button
                                                @click="addEmoji(emoji.character)"
                                                type="button"
                                        >
                                            <span x-text="emoji.character"></span>
                                        </button>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="ml-4">
                    <button
                            class="flex items-center justify-center bg-indigo-500 dark:bg-primary-700 hover:bg-primary-600 dark:hover:bg-primary-600 rounded-xl text-white px-4 py-1 flex-shrink-0"
                    >
                        <span>{{ __('Send') }}</span>
                        <span class="ms-2">
                <x-icons.send :key="'send'" class="w-4 h-4 transform rotate-45 -mt-px"/>
            </span>
                    </button>
                </div>
            </form>
        </div>

    @else
        <div class="flex flex-col items-center justify-center m-auto">
            <x-icons.chat class="h-24 w-24 text-gray-700 dark:text-gray-400"/>
            <span class="m-auto text-gray-700 dark:text-gray-50">{{ __('Please select the conversation .') }}</span>
        </div>
    @endif
</div>
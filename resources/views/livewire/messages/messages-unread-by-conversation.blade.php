<div>
    @if ($unreadCount > 0)
        <span
                class="flex items-center justify-center ml-auto text-xs text-white bg-danger-500 h-4 w-4 rounded leading-none">
                {{ $unreadCount }}
        </span>
    @endif
</div>

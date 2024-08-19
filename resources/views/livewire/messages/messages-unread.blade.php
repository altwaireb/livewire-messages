<div>
    @if($countUnreadMessages > 0)

        <span
                class="flex items-center text-xs justify-center bg-gray-300 dark:bg-danger-600 dark:text-white p-2.5 h-4 w-4 rounded-full"
        >{{ $countUnreadMessages >= 9 ? '+9' : $countUnreadMessages }}</span>
    @endif
</div>
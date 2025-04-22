<div wire:poll.5s="loadMessages" class="flex flex-col h-full">
    @if($receiver)
        <div class="flex-1 p-4 space-y-4 overflow-y-auto">
            @foreach($messages as $message)
                <div @class([
                    'flex',
                    'justify-end' => $message->sender_id === auth()->id(),
                    'justify-start' => $message->sender_id !== auth()->id(),
                ])>
                    <div @class([
                        'max-w-lg rounded-lg px-4 py-2',
                        'bg-blue-500 text-blue' => $message->sender_id === auth()->id(),
                        'bg-gray-200 text-gray-900' => $message->sender_id !== auth()->id(),
                    ])>
                        <p class="text-sm">{{ $message->message }}</p>
                        <p class="mt-1 text-xs opacity-75">
                            {{ $message->created_at->format('H:i') }}
                            @if($message->read_at && $message->sender_id === auth()->id())
                                <span class="ml-1">✓</span>
                            @endif
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="p-4 bg-white border-t">
            <form wire:submit="sendMessage" class="flex gap-2">
                <input type="text"
                       wire:model="message"
                       class="flex-1 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Type your message...">
                <button type="submit"
                        class="px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Send
                </button>
            </form>
        </div>
    @else
        <div class="flex items-center justify-center h-full">
            <p class="text-gray-500">Select a conversation to start messaging</p>
        </div>
    @endif
</div>

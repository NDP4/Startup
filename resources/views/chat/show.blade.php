<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center mb-6">
                        <div class="flex-shrink-0">
                            <img class="w-12 h-12 rounded-full"
                                 src="https://ui-avatars.com/api/?name={{ urlencode($otherUser->name) }}"
                                 alt="{{ $otherUser->name }}">
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-semibold">{{ $otherUser->name }}</h2>
                            <p class="text-sm text-gray-500">{{ $otherUser->email }}</p>
                        </div>
                    </div>

                    <div class="h-[600px] bg-gray-50 rounded-lg">
                        @livewire('chat', ['receiverId' => $otherUser->id])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

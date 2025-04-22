<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="mb-4 text-2xl font-semibold">Conversations</h2>
                    <div class="space-y-4">
                        @foreach($conversations as $user)
                            <a href="{{ route('chat.show', $user->id) }}"
                               class="flex items-center p-4 transition bg-white border rounded-lg hover:bg-gray-50">
                                <div class="flex-shrink-0">
                                    <img class="w-12 h-12 rounded-full"
                                         src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}"
                                         alt="{{ $user->name }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-lg font-medium text-gray-900">
                                        {{ $user->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $user->email }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

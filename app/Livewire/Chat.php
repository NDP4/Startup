<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Chat extends Component
{
    public $receiver_id;
    public $message = '';
    public $messages = [];
    public $receiver;

    public function mount($receiverId = null)
    {
        $this->receiver_id = $receiverId;
        if ($receiverId) {
            $this->receiver = User::find($receiverId);
            $this->loadMessages();
        }
    }

    public function getListeners()
    {
        return [
            'echo:chat,' . Auth::id() . ',MessageSent' => 'loadMessages',
            'poll.message' => 'loadMessages'
        ];
    }

    public function loadMessages()
    {
        if (!$this->receiver_id) return;

        $this->messages = Message::where(function ($query) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $this->receiver_id);
        })->orWhere(function ($query) {
            $query->where('sender_id', $this->receiver_id)
                ->where('receiver_id', Auth::id());
        })
            ->with(['sender'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('sender_id', $this->receiver_id)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function sendMessage()
    {
        $this->validate([
            'message' => 'required|string|max:1000',
            'receiver_id' => 'required|exists:users,id'
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->receiver_id,
            'message' => $this->message
        ]);

        $this->message = '';
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.chat');
    }
}

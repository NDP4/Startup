<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();

            // Get conversations based on user role
            if ($user->role === 'customer') {
                // Customers can only see their own messages with admins
                $messages = Message::with(['sender', 'receiver'])
                    ->where(function ($query) use ($user) {
                        $query->where('sender_id', $user->id)
                            ->orWhere('receiver_id', $user->id);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                // Admins can see all messages
                $messages = Message::with(['sender', 'receiver'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            return response()->json([
                'success' => true,
                'message' => 'Messages retrieved successfully',
                'data' => $messages->map(function ($message) use ($user) {
                    return [
                        'id' => $message->id,
                        'sender' => [
                            'id' => $message->sender->id,
                            'name' => $message->sender->name,
                            'role' => $message->sender->role
                        ],
                        'receiver' => [
                            'id' => $message->receiver->id,
                            'name' => $message->receiver->name,
                            'role' => $message->receiver->role
                        ],
                        'message' => $message->message,
                        'is_mine' => $message->sender_id === $user->id,
                        'read_at' => $message->read_at,
                        'created_at' => $message->created_at->format('Y-m-d H:i:s')
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getConversations()
    {
        try {
            $user = Auth::user();

            if ($user->role === 'customer') {
                // Customers only see conversations with admins
                $conversations = User::where('role', 'admin')
                    ->whereHas('receivedMessages', function ($query) use ($user) {
                        $query->where('sender_id', $user->id);
                    })
                    ->orWhereHas('sentMessages', function ($query) use ($user) {
                        $query->where('receiver_id', $user->id);
                    })
                    ->get();
            } else {
                // Admins see all customer conversations
                $conversations = User::where('role', 'customer')
                    ->whereHas('receivedMessages')
                    ->orWhereHas('sentMessages')
                    ->get();
            }

            return response()->json([
                'success' => true,
                'message' => 'Conversations retrieved successfully',
                'data' => $conversations->map(function ($conversation) use ($user) {
                    $lastMessage = Message::where(function ($query) use ($user, $conversation) {
                        $query->where('sender_id', $user->id)
                            ->where('receiver_id', $conversation->id);
                    })->orWhere(function ($query) use ($user, $conversation) {
                        $query->where('sender_id', $conversation->id)
                            ->where('receiver_id', $user->id);
                    })
                        ->latest()
                        ->first();

                    return [
                        'user' => [
                            'id' => $conversation->id,
                            'name' => $conversation->name,
                            'role' => $conversation->role
                        ],
                        'last_message' => $lastMessage ? [
                            'message' => $lastMessage->message,
                            'created_at' => $lastMessage->created_at->format('Y-m-d H:i:s'),
                            'is_read' => !is_null($lastMessage->read_at)
                        ] : null,
                        'unread_count' => Message::where('sender_id', $conversation->id)
                            ->where('receiver_id', $user->id)
                            ->whereNull('read_at')
                            ->count()
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving conversations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getConversation($userId)
    {
        try {
            $user = Auth::user();
            $otherUser = User::findOrFail($userId);

            // Validate access rights
            if ($user->role === 'customer' && $otherUser->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Customers can only message admins'
                ], 403);
            }

            // Get messages between these users
            $messages = Message::where(function ($query) use ($user, $userId) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $userId);
            })->orWhere(function ($query) use ($user, $userId) {
                $query->where('sender_id', $userId)
                    ->where('receiver_id', $user->id);
            })
                ->orderBy('created_at', 'asc')
                ->get();

            // Mark messages as read
            Message::where('sender_id', $userId)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Conversation retrieved successfully',
                'data' => [
                    'user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'role' => $otherUser->role
                    ],
                    'messages' => $messages->map(function ($message) use ($user) {
                        return [
                            'id' => $message->id,
                            'message' => $message->message,
                            'is_mine' => $message->sender_id === $user->id,
                            'read_at' => $message->read_at,
                            'created_at' => $message->created_at->format('Y-m-d H:i:s')
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving conversation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $receiver = User::find($request->receiver_id);

            // Validate that customers can only message admins
            if ($user->role === 'customer' && $receiver->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Customers can only message admins'
                ], 403);
            }

            $message = Message::create([
                'sender_id' => $user->id,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'is_mine' => true
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUnreadCount()
    {
        try {
            $count = Message::where('receiver_id', Auth::id())
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'unread_count' => $count
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting unread count',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

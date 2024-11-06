<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index()
    {
        $chats = Chat::with(['userOne', 'userTwo', 'messages' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])
            ->where('user_one', Auth::id())
            ->orWhere('user_two', Auth::id())
            ->get();
        $users = User::all(); // Adjust this query based on your needs

        return view('dashboard', compact('chats', 'users'));
    }

    // In ChatController or the relevant controller




    public function show($id)
    {
        $chat = Chat::find($id);

        if (!$chat) {
            return response()->json(['error' => 'Chat not found'], 404);
        }

        if ($chat->userOne != auth()->id() && $chat->userTwo != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $chat->messages()->get();
        return view('dashboard', compact('chat', 'messages'));
    }





    public function chats(Chat $chat)
    {
        $chats = Chat::with('userOne', 'userTwo')->get();
        return view('dashboard', compact('chat', 'chats'));

    }


    public function showMessages($id)
    {

        $chat = Chat::findOrFail($id);

        $selectedChat = null;
        $selectedChat = Chat::with('messages')->find($id);

        if (!$selectedChat) {
            return redirect()->back()->with('error', 'Chat not found');
        }

        $chats = Chat::with('userOne', 'userTwo')->get();

        $messages = Message::where('chat_id', $chat->id)->get();


        return view('Telegram', [
            'chats' => $chats,
            'messages' => $messages,
            'selectedChat' => $selectedChat,
        ]);
    }





    public function sendMessage(Request $request, $chatId)
    {
        $chat = Chat::findOrFail($chatId);

        if (auth()->id() !== $chat->user_one && auth()->id() !== $chat->user_two) {
            abort(403, 'Unauthorized to send message');
        }

        $request->validate([
            'message' => 'required_without:image_url|string',
            'image_url' => 'nullable|image|max:2048',
        ]);

        $message = new Message();
        $message->chat_id = $chatId;
        $message->sender_id = auth()->id();
        $message->message = $request->input('message');

        if ($request->hasFile('image_url')) {
            $imagePath = $request->file('image_url')->store('messages', 'public');
            $message->image_url = $imagePath;
        }

        if ($message->save()) {
            broadcast(new MessageSent($message))->toOthers();
            return redirect()->back()->with('success', 'Message sent');
        } else {
            return redirect()->back()->with('success', 'Message not sent');
        }
    }

    public function createChat(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'user_one' => 'required|exists:users,id',
            'user_two' => 'required|exists:users,id',
        ]);

        $userOne = $request->user_one;
        $userTwo = $request->user_two;

        $existingChat = Chat::where(function ($query) use ($userOne, $userTwo) {
            $query->where('user_one', $userOne)
                ->where('user_two', $userTwo);
        })
            ->orWhere(function ($query) use ($userOne, $userTwo) {
                $query->where('user_one', $userTwo)
                    ->where('user_two', $userOne);
            })
            ->first();

        if ($existingChat) {
            // Return the existing chat
            return response()->json([
                'status' => 'existing',
                'chat' => $existingChat
            ]);
        }

        // Create a new chat if no existing chat is found
        try {
            $chat = Chat::create([
                'user_one' => $userOne,
                'user_two' => $userTwo,
                // Add any additional fields as necessary
            ]);

            return response()->json([
                'status' => 'created',
                'chat' => $chat
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error creating chat: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while creating the chat.'], 500);
        }
    }





    public function store(Request $request)
    {
        Log::info('Received request to create chat', $request->all());

        try {
            // Validate incoming request data
            $request->validate([
                'user_one' => 'required|exists:users,id',
                'user_two' => 'required|exists:users,id',
                'name' => 'required|string|max:255', // Validate name
                'image_url' => 'nullable|url' // Validate image_url if provided
            ]);

            Log::info('Validated users', [
                'user_one' => $request->user_one,
                'user_two' => $request->user_two,
                'name' => $request->name,
                'image_url' => $request->image_url,
            ]);

            // Check for existing chat
            $existingChat = Chat::where(function ($query) use ($request) {
                $query->where('user_one', $request->user_one)
                    ->where('user_two', $request->user_two);
            })->orWhere(function ($query) use ($request) {
                $query->where('user_one', $request->user_two)
                    ->where('user_two', $request->user_one);
            })->first();

            if ($existingChat) {
                Log::info('Existing chat found', ['chat_id' => $existingChat->id]);
                return response()->json(['status' => 'existing', 'chat' => $existingChat]);
            }

            // Create a new chat
            $chat = Chat::create([
                'user_one' => $request->user_one,
                'user_two' => $request->user_two,
                'name' => $request->name, // Store name
                'image_url' => $request->image_url, // Store image_url
            ]);

            Log::info('Chat created successfully', ['chat_id' => $chat->id]);

            return response()->json(['status' => 'created', 'chat' => $chat]);
        } catch (\Exception $e) {
            Log::error('Error creating chat: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while creating the chat.'], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        $userId = Auth::id();

        $chatId = $request->input('chat_id');
        $active = $request->input('active');  // 1 for active, 0 for inactive

        $chat = Chat::where(function ($query) use ($userId) {
            $query->where('user_one', $userId)
                ->orWhere('user_two', $userId);
        })->findOrFail($chatId);

        $chat->active = $active;
        $chat->save();

        return response()->json([
            'message' => 'Chat active status updated successfully',
            'chat_id' => $chatId,
            'active' => $active
        ]);
    }

    public function getStatus($id)
    {
        $chat = Chat::find($id);

        if (!$chat) {
            return response()->json(['error' => 'Chat not found'], 404);
        }

        return response()->json(['active' => $chat->active]);
    }


    public function getOtherUser(Chat $chat, $currentUserId)
    {
        return $chat->user_one == $currentUserId ? $chat->userTwo : $chat->userOne;
    }


    public function fetchMessages($id)
    {
        // Find the selected chat with its messages
        $chat = Chat::with('messages')->find($id);

        // Check if the chat exists
        if (!$chat) {
            return response()->json(['error' => 'Chat not found'], 404);
        }

        // Fetch messages along with the sender information if needed
        $messages = $chat->messages()->with('sender')->get();

        return response()->json(['messages' => $messages]);
    }


    public function getUnreadMessages($id)
    {
        // Fetch new messages for the chat that are not seen by the user
        $newMessages = Message::where('chat_id', $id)
            ->where('sender_id', '!=', auth()->id()) // Get messages sent by other users
            ->where('seen', false) // Assuming you have a 'seen' column
            ->with('sender') // Include the sender relation
            ->get();

        return response()->json(['messages' => $newMessages]);
    }

    public function getUnreadCount($chatId) {
        $unreadCount = Message::where('chat_id', $chatId)
            ->where('sender_id', '!=', auth()->id())
            ->where('seen', false)
            ->count();

        return response()->json(['count' => $unreadCount]);
    }

    public function loadMessages($id)
    {
        $messages = Message::where('chat_id', $id)
            ->with('sender') // Include sender relation if needed
            ->get();

        // Mark messages as seen
        foreach ($messages as $message) {
            if ($message->sender_id != auth()->id() && !$message->seen) {
                $message->seen = true;
                $message->save();
            }
        }

        return response()->json(['messages' => $messages]);
    }


    public function markAsSeen($messageId)
    {
        $message = Message::findOrFail($messageId);
        $message->seen = 1;
        $message->save();

        return response()->json(['success' => true]);
    }

    public function markAsRead($chatId)
    {
        Message::where('chat_id', $chatId)
            ->where('receiver_id', auth()->id())
            ->where('seen', false)
            ->update(['seen' => true]);

        return response()->json(['status' => 'success']);
    }







}

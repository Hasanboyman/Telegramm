<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function index()
    {
        // Fetch all chats
        $chats = Chat::all();

        // Fetch the active chat
        $activeChat = Chat::where('active', true)->first();

        // Handle the case where no active chat is found
        if ($activeChat) {
            $messages = Message::where('chat_id', $activeChat->id)->get();
        } else {
            $messages = collect(); // Return an empty collection if no active chat is found
        }

        return view('dashboard', compact('chats', 'activeChat', 'messages'));
    }
}

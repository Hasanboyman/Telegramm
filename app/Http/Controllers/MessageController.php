<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(Request $request, Chat $chat)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = $chat->messages()->create([
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return response()->json(['message' => $message]);
    }

    public function show(Request $request, Chat $chat)
    {
        return response()->json(['message' => $chat->messages()]);
    }

    // In MessageController.php



}


<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Chat;

class CheckChatMember
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $chatId = $request->route('id');
        $chat = Chat::find($chatId);

        // Check if chat exists and if the authenticated user is part of the chat
        if (!$chat || ($chat->userOne->id != auth()->id() && $chat->userTwo->id != auth()->id())) {
            return redirect()->back()->withErrors([]);
        }

        return $next($request);
    }
}

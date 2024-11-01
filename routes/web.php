<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckChatMember;
use Illuminate\Support\Facades\Route;
use  Pusher\Pusher;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {    Route::get('/dashboard', [ChatController::class, 'index']);
})->name('dashboard');

Route::get('/chats/{id}', [ChatController::class, 'showMessages'])->name('messages.index')->middleware(CheckChatMember::class);

Route::get('/chats/{id}/messages', [ChatController::class, 'fetchMessages'])->name('messages.fetch');

Route::get('/chats/{id}/messages/unread', [ChatController::class, 'getUnreadMessages']);

Route::post('/chats/messages/{id}/seen', [ChatController::class, 'markAsSeen']);

Route::post('/chats/{id}/message', [ChatController::class, 'sendMessage'])->name('messages.send')->middleware(CheckChatMember::class);

Route::post('/chats/{id}', [ AuthenticatedSessionController::class, 'destroy'])->name('Logout')->middleware('auth:sanctum');

Route::get('/search/users', [UserController::class, 'search'])->name('users.search');

Route::post('/chats', [ChatController::class, 'store'])->name('chats.store');

Route::post('/update-user-status', [UserController::class, 'updateUserStatus']);

Route::get('/users/{id}/status', [UserController::class, 'getUserStatus']);

Route::post('/chats/create', [ChatController::class, 'createChat']);

Route::put('/profile/picture', [UserController::class, 'updatePicture'])->name('profile.picture.update');

Route::get('/chats/{id}', [ChatController::class, 'showMessages'])->name('messages.index');

Route::get('/chats/{id}/messages/unread/count', [ChatController::class, 'getUnreadCount']);


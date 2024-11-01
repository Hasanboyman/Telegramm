<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static findOrFail($chatId)
 */
class Chat extends Model
{
    use HasFactory;

    // Define the relationships
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two');
    }

    protected $fillable = [
        'name',
        'image_url',
        'user_one',
        'user_two',
    ];
}

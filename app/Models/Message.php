<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'sent_at',
    ];

    protected $fillable = [
        'sender_username',
        'recipient_username',
        'content',
    ];

    protected array $dates = [
        'sent_at',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_username');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_username');
    }
}

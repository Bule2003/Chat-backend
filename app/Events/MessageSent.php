<?php

namespace App\Events;

use App\Models\User;
use App\Models\Message;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    private array $arr;
    /*public User $user;*/
    public $message;

    public function __construct($message)
    {
        /*$this->user = $user;*/
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        logger('The message: ');
        logger($this->message);
        logger('broadcasting MessageSent');
        /*Log::debug($this->message->conversation_id);*/
        return [
            /*new Channel('chat.' .$this->message->conversation_id),*/
            new PrivateChannel('chat'),
        ];
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }
}

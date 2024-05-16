<?php

namespace App\Http\Controllers;

use App\Events\SendMessageEvent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function SendMessage(Request $request) // TODO: make SendMessageRequest
    {
        if ($request->to == auth('sanctum')->user()->name){ // does name exist?
            return response()->json(['message' => 'You cannot send a message to yourself']);
        }

        $sender = User::query()->where('username', $request->input('sender_username'))->first();
        $recipient = User::query()->where('username', $request->input('recipient_username'))->first();

        $collection = $this->IsTherePreviousConversation($recipient->username, auth('sanctum')->user()->id);

        if(!$collection){
            $conversation = Conversation::create([
                'user_id' => auth('sanctum')->user()->id
            ]);
        }



        $message = Message::create([
            'sender_username' => $sender->username,
            'recipient_username' => $recipient->username,
            'content' => $request->input('content'),
            'sent_at' => now(),
        ]);

        broadcast(new SendMessageEvent($message->toArray()))->toOthers(); // TODO: create class for sending messages
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Conversation $conversation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Conversation $conversation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Conversation $conversation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conversation $conversation)
    {
        //
    }

    private function IsTherePreviousConversation($sender_username, $recipient_username)
    {
        $collection = Message::whereHas('conversation', function ($q) use ($sender_username, $recipient_username) {
            $q->where('sender_username', $sender_username)
                ->where('recipient_username', $recipient_username);
        })->orWhere(function ($q) use ($sender_username, $recipient_username) {
            $q->where('sender_username', $recipient_username)
                ->where('recipient_username', $sender_username);
        })->get();

        if (count($collection) > 0) {
            return $collection;
        }

        return false;
    }

}

<?php

namespace App\Http\Controllers;

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

        $OtherUserId = User::where('name', $request->to)->first->id;
        $collection = $this->IsTherePreviousChat($OtherUserId, auth('sanctum')->user()->id);

        if($collection == false){
            $conversation = Conversation::create([
                'user_id' => auth('sanctum')->user()->id
            ]);
        }

        $sender = User::query()->where('username', $request->input('sender_username'))->first();
        $recipient = User::query()->where('username', $request->input('recipient_username'))->first();

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

    private function IsTherePreviousConversation($OtherUserId, $user_id) // TODO: change to usernames
    {
        $collection = Message::whereHas('conversation', function ($q) use ($OtherUserId, $user_id) { // TODO: add whereHas method
        $q->where('sender_username', $OtherUserId)
            ->where('recipient_username', $user_id);
        })->orWhere(function ($q) use ($OtherUserId, $user_id) {
            $q->where('sender_username', $user_id)
                ->where('recipient_username', $OtherUserId);
        })->get();

        if (count($collection) > 0) {
            return $collection;
        }

        return false;
    }
}

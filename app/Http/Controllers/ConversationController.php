<?php

namespace App\Http\Controllers;

use App\Events\SendMessageEvent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function SendMessage(Request $request) // TODO: make SendMessageRequest
    {
        logger($request->to);
        /*if ($request->to == auth('api')->user()->id()){
            return response()->json(['message' => 'You cannot send a message to yourself']);
        }*/

        $request->validate([
            'sender_username' => 'required|string|exists:users,username',
            'recipient_username' => 'required|string|exists:users,username',
            'content' => 'required|string|max:255',
        ]);

        $sender = User::query()->where('username', $request->input('sender_username'))->first();
        $recipient = User::query()->where('username', $request->input('recipient_username'))->first();

        if (!$sender) {
            return response()->json(['error' => 'Sender user not found'], 404);
        }

        if (!$recipient) {
            return response()->json(['error' => 'Recipient user not found'], 404);
        }

        // TODO: replace sender username back to: auth('api')->user()->id()

        //$collection = $this->IsTherePreviousConversation($recipient->username, $sender->username); // TODO: change from API to sanctum **optional**

        // TODO: replace sender username back to: auth('api')->user()->id()

        /*if(!$collection){
            $conversation = Conversation::create([
                'user_id' => $sender->username
            ]);
        }*/

        $conversation = Conversation::whereHas('users', function($q) use ($sender, $recipient) {
            $q->where('user_id', $sender->id)
                ->orWhere('user_id', $recipient->id);
        })->first();

        /*$conversation = Conversation::whereHas('users', function($query) use ($sender) {
            $query->where('user_id', $sender->id);
        })
            ->whereHas('users', function($query) use ($recipient) {
                $query->where('user_id', $recipient->id);
            })
            ->first();*/

        if (!$conversation) {
            $conversation = Conversation::create();
            $conversation->users()->attach([$sender->id, $recipient->id]);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_username' => $sender->username,
            'recipient_username' => $recipient->username,
            'content' => $request->input('content'),
            'sent_at' => now(),
        ]);

        /*return $message;*/

        broadcast(new SendMessageEvent($message->toArray()))->toOthers(); // TODO: create class for sending messages

        return response()->json(['message' => 'Message sent successfully']);
    }

    public function index()
    {
        $user = User::query()->where('id', auth()->id())->first();

        /*$conversations = Conversation::query()->where('sender_id', auth()->id())
            ->orWhere('recipient_id', auth()->id())
            ->with('messages')
            ->get();*/

        $conversations = Conversation::whereHas('users', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with('messages')->get();

        $conversations = Conversation::all();

        return response()->json($conversations);
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
        /*$this->authorize('view', $conversation);*/

        if (!$conversation) {
            return response()->json(['message' => 'This conversation doesn\'t exist'], 404);
        }

        return response()->json($conversation->load('messages'));
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

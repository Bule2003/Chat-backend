<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Laravel\Reverb\Loggers\Log;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function SendMessage(Request $request) // TODO: make SendMessageRequest
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
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

        $conversation = Conversation::whereHas('users', function($q) use ($sender, $recipient) {
            $q->where('user_id', $sender->id)
                ->orWhere('user_id', $recipient->id);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create();
            $conversation->users()->attach([$sender->id, $recipient->id]);
        }

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'sender_username' => $sender->username,
            'recipient_username' => $recipient->username,
            'content' => $request->input('content'),
            'sent_at' => now(),
        ]);

        logger($message);

        /*event(new MessageSent($message));*/

        broadcast(new MessageSent($message))->toOthers();
        /*MessageSent::dispatch($message);*/

        return response()->json(['message' => 'Message sent successfully']);
    }

    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // TODO: lazy load the conversations

        $conversations = $user->conversations()->with(['messages', 'users'])->paginate(20); // paginate or get all at once

        logger($conversations);

        return response()->json($conversations);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'recipient_username' => 'required|string'
        ]);

        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 404);
        }

        $recipient = User::where('username', $request->input('recipient_username'))->first();

        if (!$recipient) {
           return response()->json(['message' => 'User not found'], 404);
        }

        $conversation = Conversation::create([
            'title' => $request->input('title'),
            /*'title' => $request->title,*/
        ]);

        $conversation->users()->attach([$user->id, $recipient->id]);

        return response()->json(['message' => 'Conversation created successfully', 'conversation' => $conversation]);
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
    public function edit(Request $request, $id)
    {
        /*$conversation = Conversation::find($id);

        logger($conversation);

        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        $conversation->update([
            'title' => $request->title
        ]);

        return response()->json(['message' => 'Conversation updated successfully']);*/
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string'
        ]);

        $conversation = Conversation::find($id);

        logger($conversation);

        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        $conversation->update([
           'title' => $request->title
        ]);

        return response()->json(['message' => 'Conversation updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message', 'Unauthorized'], 401);
        }

        $conversation = Conversation::find($id);

        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        $conversation->users()->detach($user->id);

        if($conversation->users()->count() === 0) {
            Message::where('conversation_id', $conversation->id)->delete();
            $conversation->delete();
        }

        return response()->json(['message' => 'Conversation deleted successfully']);
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

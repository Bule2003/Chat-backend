<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\MessageRequest;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{

    public function create()
    {
        Log::debug("test");
        /*return view('/');  // TODO: make a view with an input form to send/create a message*/
    }

    public function store(MessageRequest $request)
    {
        Log::debug("test");
        /*$sender = User::where('username', $request->input('sender_username'));
        $recipient = User::where('username', $request->input('recipient_username'));*/

        $sender = User::query()->where('username', $request->input('sender_username'))->first();
        $recipient = User::query()->where('username', $request->input('recipient_username'))->first();

        if (!$sender) {
            return response()->json(['error' => 'Sender user not found'], 404);
        }

        if (!$recipient) {
            return response()->json(['error' => 'Recipient user not found'], 404);
        }

        $message = Message::create([
            //'sender_id' => $sender->id,
            //'recipient_id' => $recipient->id,
            'sender_username' => $sender->username,
            'recipient_username' => $recipient->username,
            'content' => $request->input('content'),
            'sent_at' => now(),
        ]);

        return response()->json($message, 200);
    }


    public function index()
    {
        $messages = Message::all();

        return response()->json($messages);
    }

    public function show($id)
    {
        $message = Message::find($id);

        if(!$message){
            return response()->json(['message' => 'Message not found'], 404);
        }

        return response()->json($message);
    }

    public function update(MessageRequest $request, $id)
    {
        $message = Message::find($id);

        if(!$message){
            return response()->json(['message' => 'Message not found'], 404);
        }

        $message->update([
            'content' => $request->input('content'),
            'sent_at' => now(), // optional
        ]);

        return response()->json($message);
    }

    public function destroy($id)
    {
        $message = Message::find($id);

        if(!$message){
            return response()->json(['message' => 'Message not found'], 404);
        }

        $message->delete();

        return response()->json(['message' => 'Message was deleted successfully'], 200);
    }
}

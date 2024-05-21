<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(100)->create();

        $conversations = Conversation::factory(45)->create();

        foreach ($conversations as $conversation) {
            $users = User::inRandomOrder()->take(2)->pluck('id');
            $conversation->users()->attach($users);
        }

        $conversations = Conversation::all();
        foreach ($conversations as $conversation) {
            Message::factory(ceil(300 / 45))->create([
                'conversation_id' => $conversation->id,
                'sender_username' => $conversation->users->first()->username,
                'recipient_username' => $conversation->users->last()->username,
            ]);
        }
    }
}

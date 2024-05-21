<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /*$sender = User::inRandomOrder()->first();
        $recipient = User::where('username', '!=', $sender->username)->inRandomOrder()->first();

        $conversation = Conversation::whereHas('users', function ($query) use ($sender, $recipient) {
            $query->whereIn('user_id', [$sender->id, $recipient->id]);
        })->first();*/

        /*if (!$conversation) {
            $conversation = Conversation::factory()->create();
            $conversation->users()->attach([$sender->id, $recipient->id]);
        }*/

        return [
            'content' => fake()->text(),
            'sent_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Message $message) {
            if(!$message->conversation_id) {
                $conversation = Conversation::inRandomOrder()->first();
                $message->conversation_id = $conversation->id;
            }

            if (!$message->sender_username || !$message->recipient_username) {
                $users = $conversation->users->pluck('username');
                $message->sender_username = $users[0];
                $message->recipient_username = $users[1];
            }
        });
    }
}

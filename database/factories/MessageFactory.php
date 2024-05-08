<?php

namespace Database\Factories;

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
        $sender = User::inRandomOrder()->first();
        $recipient = User::where('username', '!=', $sender->username)->inRandomOrder()->first();

        return [
            'sender_username' => $sender->username,
            'recipient_username' => $recipient->username,
            'content' => fake()->text(),
            'sent_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}

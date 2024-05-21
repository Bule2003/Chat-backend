<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conversation>
 */
class ConversationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->text()
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Conversation $conversation) {
            $users = User::inRandomOrder()->take(2)->pluck('id');
            $conversation->users()->attach($users);
        });
    }
}

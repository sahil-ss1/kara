<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Notifications\DatabaseNotification;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 *
 * To use: Database\Factories\NotificationFactory::new()->create();
 */
class DatabaseNotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DatabaseNotification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'id' => fake()->uuid,
            'type' => 'App\Notifications\SimpleUserNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => User::all()->random()->id,
            "data" => [
                "from" => User::all()->random()->id,
                "title" => fake()->realText(70),
                "message" => fake()->realText(300)
            ]
        ];
    }
}

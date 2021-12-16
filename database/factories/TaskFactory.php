<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'content' => $this->faker->content,
            'is_done' => $this->faker->is_done,
            'is_deleted' => $this->faker->is_deleted,
            'is_pin' => $this->faker->is_pin,
        ];
    }
}

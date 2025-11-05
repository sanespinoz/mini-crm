<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'          => $this->faker->name(),
            'email'         => $this->faker->unique()->safeEmail(),
            'phone'         => $this->faker->phoneNumber(),
            'score'         => $this->faker->numberBetween(0, 100),
            'processed_at'  => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CoordonateurFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'telephone' => $this->faker->phoneNumber(),
            'adresse' => $this->faker->address(),
            'photo_path' => null,
            // Add any other fields that are relevant to the Coordonateur model
        ];
    }
}

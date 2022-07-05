<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ids = $this->faker->unique()->randomElement([sha1(Str::uuid()->toString())]);

        return [
            'public_id' => $ids,
            'first_user' => 1,
            'second_user' => $this->faker->numberBetween(2,190),
            'is_spam' => false,
            'status' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

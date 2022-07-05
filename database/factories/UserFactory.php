<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $gender = $this->faker->randomElement(['male', 'female']);
        $uids1 = $this->faker->unique()->randomElement([sha1(Str::uuid()->toString())]);
        $uids2 = $this->faker->unique()->randomElement([sha1(Str::uuid()->toString())]);

        $user = [
            'uid' => $uids1,
            'public_uid' => $uids2,
            'username' => substr($uids2, 0,16),
            'fname' => $this->faker->firstName(),
            'sname' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$eLz12rQVH2Tgw1pKGLuBfODuWvNKv/ERgLcjltanlffNHziiGk7Hy', // 12345789
            'phone' => $this->faker->unique()->phoneNumber(),
            'phone_verified_at' => now(),
            'gender' => $gender,
            'bdate' => $this->faker->dateTimeBetween('1980-12-31', '2006-12-31'),
            'is_active' => true,
            'remember_token' => Str::random(100),
        ];

        Redis::set('profiles:username:'.$user['uid'], 
            json_encode([
                "user_id" => $user['public_uid'],
                "dark_mode" => 0,
                "notification_sound" => 1,
                "message_sound" => 1,
                "created_at" => now(),
                'updated_at' => now()
            ])
        );

        return $user;
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}

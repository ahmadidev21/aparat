<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type'=>User::TYPE_USER,
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => '$2y$10$xdJPZM0AE9eltzDofHkWuOdr1TLFv/Up35bRbthHH6SRecUvURWzK', // 123456
            'mobile'=> '+989'.random_int(1111, 9999).random_int(11111, 99999),
            'avatar'=>null,
            'website'=>$this->faker->url,
            'verify_code'=>null,
            'verified_at' => now(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enums\PostTypeEnum;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $index = array_rand(PostTypeEnum::cases());

        return [
            'title' => $this->faker->name,
            'type' => PostTypeEnum::getByIndex($index),
        ];
    }
}

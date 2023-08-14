<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->name(),
            'episode_id' => fake()->text(),
            'director' => fake()->text(),
            'producer' => fake()->text(),
            'release_date' => fake()->text(),
            'director' => fake()->text(),
            'created' => fake()->text(), // password
            'opening_crawl' => Str::random(1000),
            'edited' => fake()->text(),
            'url' => fake()->url(),
        ];
    }
}

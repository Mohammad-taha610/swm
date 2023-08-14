<?php

namespace Tests\Feature;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MovieUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function testUpdateMovie()
    {
        $user = \App\Models\User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Create a movie
        $movie = Movie::factory()->create();

        $updatedData = [
            'title' => 'Updated Movie Title',
            'episode_id' => 10,
            'opening_crawl' => 'Updated opening crawl.',
            'director' => 'Updated Director',
            'producer' => 'Updated Producer',
            'release_date' => '2022-01-01',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/films/modify/' . $movie->id, $updatedData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('movies', $updatedData);
        $response->assertJsonFragment(['title' => 'Updated Movie Title']);
    }

    public function testUpdateNonexistentMovie()
    {
        $user = \App\Models\User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/films/modify/id', []);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Film Not Found']);
    }
}

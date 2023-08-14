<?php

namespace Tests\Feature;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MovieDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function testDeleteMovie()
    {
        $user = \App\Models\User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Create a movie
        $movie = Movie::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/films/delete/' . $movie->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('movies', ['id' => $movie->id]);
        $response->assertJson(['message' => 'delete successful']);
    }

    public function testDeleteNonexistentMovie()
    {
        $user = \App\Models\User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/films/delete/nonexistent');

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Film Not Found']);
    }
}

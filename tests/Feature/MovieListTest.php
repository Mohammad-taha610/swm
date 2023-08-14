<?php

namespace Tests\Feature;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MovieListTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexWithSearch()
    {
        // Create some test data
        Movie::factory()->create([
            'title' => 'Test Movie 1',
        ]);
        Movie::factory()->create([
            'title' => 'Another Movie',
        ]);

        // Create a user and authenticate with Sanctum
        $user = \App\Models\User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/films/list?search=Test');


        $response->assertStatus(200);
        $response->assertJsonCount(1, 'films');
        $response->assertJsonFragment(['title' => 'Test Movie 1']);
        $response->assertJsonMissing(['title' => 'Another Movie']);
    }

    public function testIndexWithoutSearch()
    {
        // Create some test data
        Movie::factory()->count(3)->create();

        // Create a user and authenticate with Sanctum
        $user = \App\Models\User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/films/list');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'films');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MoviesController extends Controller
{
    private function getSwapiResource($resource){
        $arr = [];
        $Swapi = new SwapiController();
        foreach($resource as $res){
            $arr[] = json_decode($Swapi->get($res));
        };
        return $arr;

    }

    private function formatThisMovie($mov){
        $movie = json_decode($mov);
        $moviePeople = [];
        $moviePlanets = [];
        $movieSpecies = [];
        $movieStarships = [];
        $movieVehicles = [];

        $moviePeople = $this->getSwapiResource($movie->characters);
        $moviePlanets = $this->getSwapiResource($movie->planets);
        $movieSpecies = $this->getSwapiResource($movie->species);
        $movieStarships = $this->getSwapiResource($movie->starships);
        $movieVehicles = $this->getSwapiResource($movie->vehicles);
        $movie = [
            'title' => $movie->title,
            'episode_id' => $movie->episode_id,
            'opening_crawl' => $movie->opening_crawl,
            'director' => $movie->director,
            'producer' => $movie->producer,
            'release_date' => $movie->release_date,
            'created' => $movie->created,
            'edited' => $movie->edited,
            'people' => $moviePeople,
            'planets' => $moviePlanets,
            'species' => $movieSpecies,
            'starships' => $movieStarships,
            'vehicles' => $movieVehicles,
        ];
        return $movie;
    }
    private function formatMovies($films){
        $formattedMovies = [];
        $films = json_decode($films);
        if($films->count > 0) {
            $movies = $films->results;
            foreach ($movies as $movie){
                $formattedMovies[] = [
                    'title' => $movie->title,
                    'episode_id' => $movie->episode_id,
                    'opening_crawl' => $movie->opening_crawl,
                    'director' => $movie->director,
                    'producer' => $movie->producer,
                    'release_date' => $movie->release_date,
                    'created' => $movie->created,
                    'edited' => $movie->edited,
                    'url' => $movie->url,
                ];
            }
            return $formattedMovies;
        }
        return false;

    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = '';
        if($request->has('search')) $search = $request->get('search');
        $movie = new Movie();
        $allMovies = $movie->where('title','like','%'.$search.'%')->get([
            'id',
            'title',
            'episode_id',
            'opening_crawl',
            'director',
            'producer',
            'release_date',
        ]);
        return response()->json([ 'films'=>$allMovies ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $swapi = new SwapiController();
        $films = $swapi->getFilms();
        $formatMovies = $this->formatMovies($films);
        $movie = new Movie();
        if($formatMovies){
            $movie->Truncate();
            $movie->insert($formatMovies);
        }
        $allMovies = $movie->get([
            'title',
            'episode_id',
            'opening_crawl',
            'director',
            'producer',
            'release_date',
            'created',
            'edited',
        ]);
        return response()->json([ 'films'=>$allMovies ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $movie = new Movie();
        $DBmovie = $movie->find($id);
        $Swapi = new SwapiController();
        if ($DBmovie) {
            $movie_details = $Swapi->get($DBmovie->url);
            return response()->json(['film' => $this->formatThisMovie($movie_details)]);
        }

        return response()->json(['message' => 'Film Not Found'], 404);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $movie = new Movie();
        $DBmovie = $movie->find($id);
        if ($DBmovie) {
            if ($request->has('title')) $DBmovie->title = $request->input('title');
            if ($request->has('episode_id')) $DBmovie->episode_id = $request->input('episode_id');
            if ($request->has('opening_crawl')) $DBmovie->opening_crawl = $request->input('opening_crawl');
            if ($request->has('director')) $DBmovie->director = $request->input('director');
            if ($request->has('producer')) $DBmovie->producer = $request->input('producer');
            if ($request->has('release_date')) $DBmovie->release_date = $request->input('release_date');
            $DBmovie->update();

            $allMovies = $movie->get([
                'title',
                'episode_id',
                'opening_crawl',
                'director',
                'producer',
                'release_date',
            ]);

            return response()->json(['films' => $allMovies]);
        }
        return response()->json(['message' => 'Film Not Found'], 404);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $movie = new Movie();
        $DBmovie = $movie->find($id);
        if($DBmovie){
            $DBmovie->delete();
            return response()->json([ 'message'=> 'delete successful' ]);
        }
        return response()->json([ 'message'=> 'Film Not Found' ], 404);


    }
}

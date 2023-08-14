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
     /**
     * @OA\Get(
     *      path="/films/list",
     *      operationId="list",
     *      tags={"Films"},
     *      summary="list all stored data for sw films",
     *      description="list all stored data for sw films. further films can be searched using there title as search param in query",
     *      @OA\SecurityScheme(
     *          type="apiKey",
     *          in="header",
     *          securityScheme="token",
     *          name="Authorization"
     *      ),
     *      @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="search any title or part of title to filter results",
     *         required=false,
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="films", type="object", example="")
     *          )
     *       )
     *  )
     *  )
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
     * Store a newly created resource in storage.
     */
     /**
     * @OA\Get(
     *      path="/films/store",
     *      operationId="store",
     *      tags={"Films"},
     *      summary="store and refresh all films from api of sw films",
     *      description="it refreshes and gets all the latest films from sw api and store in database for further modification and deletion and listing",
     *      @OA\SecurityScheme(
     *          type="apiKey",
     *          in="header",
     *          securityScheme="token",
     *          name="Authorization"
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="films", type="object", example="")
     *          )
     *       )
     *  )
     *  )
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
            'id',
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
     /**
     * @OA\Get(
     *      path="/films/list/{id}",
     *      operationId="listOne",
     *      tags={"Films"},
     *      summary="List required film using id having all details of that film from sw apis",
     *      description="it fetches all relevant resources for film from sw API and stores in cache and return response the response for a purticular film would take time in the initial request after that it returns response faster",
     *      @OA\SecurityScheme(
     *          type="apiKey",
     *          in="header",
     *          securityScheme="token",
     *          name="Authorization"
     *      ),
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="search any id",
     *         required=true,
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="film", type="object", example="")
     *          )
     *       )
     *  )
     *  )
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
     * Update the specified resource in storage.
     */
    /**
     * @OA\Post(
     *      path="/films/modify/{id}",
     *      operationId="updateOne",
     *      tags={"Films"},
     *      summary="List required film using id having all details of that film from sw apis",
     *      description="it fetches all relevant resources for film from sw API and stores in cache and return response the response for a purticular film would take time in the initial request after that it returns response faster",
     *      @OA\SecurityScheme(
     *          type="apiKey",
     *          in="header",
     *          securityScheme="token",
     *          name="Authorization"
     *      ),
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="update any id",
     *         required=true,
     *      ),
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            @OA\Property(property="title",    type="string", format="string", example="Revenge of the Sith"),
     *            @OA\Property(property="episode_id", type="string", format="string", example="6"),
     *            @OA\Property(property="opening_crawl", type="string", format="string", example="War! The Republic is crumbling\r\nunder attacks by the ruthless\r\nSith Lord, Count Dooku.\r\nThere are heroes on both sides.\r\nEvil is everywhere.\r\n\r\nIn a stunning move, the\r\nfiendish droid leader, General\r\nGrievous, has swept into the\r\nRepublic capital and kidnapped\r\nChancellor Palpatine, leader of\r\nthe Galactic Senate.\r\n\r\nAs the Separatist Droid Army\r\nattempts to flee the besieged\r\ncapital with their valuable\r\nhostage, two Jedi Knights lead a\r\ndesperate mission to rescue the\r\ncaptive Chancellor...."),
     *            @OA\Property(property="director", type="string", format="string", example="Howard G. Kazanjian"),
     *            @OA\Property(property="producer", type="string", format="string", example="George Lucas"),
     *            @OA\Property(property="release_date", type="string", format="string", example="2005-05-19"),
     *         ),
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="films", type="object", example="")
     *          )
     *       )
     *  )
     *  )
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
                'id',
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
    /**
     * @OA\Get(
     *      path="/films/delete/{id}",
     *      operationId="DeleteOne",
     *      tags={"Films"},
     *      summary="Delete required film using id having all details of that film from sw apis",
     *      description="It Deletes required film using id from database",
     *      @OA\SecurityScheme(
     *          type="apiKey",
     *          in="header",
     *          securityScheme="token",
     *          name="Authorization"
     *      ),
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="delete any film using id",
     *         required=true,
     *      ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="delete successful")
     *          )
     *       )
     *  )
     *  )
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

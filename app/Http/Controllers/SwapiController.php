<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SwapiController extends Controller
{
    private $SwapiBaseURL = 'https://swapi.dev/api/';
    private $SwapiFilmURL = 'films/';
    private $cacheTTL = '120';

    public function getFilmURL(){
        return $this->SwapiBaseURL.$this->SwapiFilmURL;
    }

    private function storeFilmsInCache($films){
        $redis = Redis::connection();
        $redis->setex('films', $this->cacheTTL, json_encode($films));
    }
    private function getFilmsInCache(){
        $redis = Redis::connection();
        $response = $redis->get('films');
        return json_decode($response);
    }
    private function getFilmsFromSWAPI(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->getFilmURL(),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }
    public function getFilms()
    {
        $response = $this->getFilmsInCache();
        if(!$response){
            $response = $this->getFilmsFromSWAPI();
            $this->storeFilmsInCache($response);
            $response = $this->getFilmsInCache();
        }
        return $response;
    }
}

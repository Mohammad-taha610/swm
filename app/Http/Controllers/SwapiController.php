<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SwapiController extends Controller
{
    private $SwapiBaseURL = 'https://swapi.dev/api/';
    private $SwapiFilmURL = 'films/';
    private $cacheTTL = '86400';

    public function getFilmURL(){
        return $this->SwapiBaseURL.$this->SwapiFilmURL;
    }

    public function getResourceURL($res){
        return $this->SwapiBaseURL.$res;
    }


    private function storeFilmsInCache($films){
        $redis = Redis::connection();
        $redis->setex('films', $this->cacheTTL, json_encode($films));
    }

    private function storeResourceInCache($res, $response){
        $redis = Redis::connection();
        $redis->setex($res, $this->cacheTTL, json_encode($response));
    }

    private function getFilmsInCache(){
        $redis = Redis::connection();
        $response = $redis->get('films');
        return json_decode($response);
    }
    private function getInCache($res){
        $redis = Redis::connection();
        $response = $redis->get($res);
        return json_decode($response);
    }


    private function getResourceFromSWAPI($url){
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
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

    private function getFilmsFromSWAPI(){
        $url = $this->getFilmURL();
        return $this->getResourceFromSWAPI($url);

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

    private function formatResourceUrl ($url) {
        $uriSegments = explode("/", parse_url($url, PHP_URL_PATH));
        $end0 = array_pop($uriSegments);
        $end1 = array_pop($uriSegments);
        $end2 = array_pop($uriSegments);
        if($end0 === ""){
            return $end2.'/'.$end1;
        }
        return $end1.'/'.$end0;
    }
    public function get($resourceUrl){
        $res = $this->formatResourceUrl($resourceUrl);
        $response = $this->getInCache($res);
        if(!$response){
            $url = $this->getResourceURL($res);
            $response = $this->getResourceFromSWAPI($url);
            $this->storeResourceInCache($res, $response);
            $response = $this->getInCache($res);
        }
        return $response;
    }
}

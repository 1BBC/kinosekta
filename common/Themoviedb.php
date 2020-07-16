<?php

namespace app\common;

use Exception;
use yii\httpclient\Client;
use Yii;
use yii\base\BaseObject;

class Themoviedb extends BaseObject
{
    private $api_key;
    private $base_url;

    private $client;

    private static $instances = [];

    public static $GENRES = [
        28      =>  'is_action',
        12      =>  'is_adventure',
        16      =>  'is_animation',
        35      =>  'is_comedy',
        80      =>  'is_crime',
        99      =>  'is_documentary',
        18      =>  'is_drama',
        10751   =>  'is_family',
        14      =>  'is_fantasy',
        36      =>  'is_history',
        27      =>  'is_horror',         
        10402   =>  'is_music',
        9648    =>  'is_mystery',
        10749   =>  'is_romance',
        878     =>  'is_science_fiction',
        10770   =>  'is_tv_movie',
        53      =>  'is_thriller',
        10752   =>  'is_war',
        37      =>  'is_western',
    ];

    public function __construct($params = [], $config = [])
    {
        $this->api_key = $params['api_key'] ?? Yii::$app->params['themoviedb']['api_key'];
        $this->base_url = $params['base_url'] ?? Yii::$app->params['themoviedb']['base_url'];

        $this->client = new Client();

        parent::__construct($config);
    }

    public function init()
    {
        parent::init();

        // ... инициализация происходит после того, как была применена конфигурация.
    }

    public static function getInstance(): Themoviedb
    {
        $cls = static::class;
        if (!isset(static::$instances[$cls])) {
            static::$instances[$cls] = new static;
        }

        return static::$instances[$cls];
    }

    public function methodMovieDetails($movie_id, array $params = null)
    {
        $params['api_key'] = $this->api_key;

        $url = $this->base_url . '/' . 'movie' . '/' .$movie_id;
        $response = $this->client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->setData($params)
            ->send();
        if ($response->isOk) {
            $obj_movies = json_decode($response->getContent());
            if (isset($obj_movies) && is_object($obj_movies)) {
                return $obj_movies;
            }
        }
        throw new Exception('TMDb (methodMovieDetails) dont find tv by id [' . $movie_id .']');
    }

    public function methodTvDetails($tmd_id, array $params = null)
    {
        $params['api_key'] = $this->api_key;

        $url = $this->base_url . '/' . 'tv' . '/' .$tmd_id;
        $response = $this->client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->setData($params)
            ->send();
        if ($response->isOk) {
            $obj_movies = json_decode($response->getContent());
            if (isset($obj_movies) && is_object($obj_movies)) {
                return $obj_movies;
            }
        }
        throw new Exception('TMDb (methodTvDetails) dont find tv by tmdb [' . $tmd_id .']');
    }

    public function getTvId($imdb_id)
    {
        $params['api_key'] = $this->api_key;
        $params['external_source'] = 'imdb_id';

        $url = $this->base_url . '/' . 'find' . '/' .$imdb_id;
        $response = $this->client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->setData($params)
            ->send();
        if ($response->isOk) {
            $obj_movies = json_decode($response->getContent());
            if (isset($obj_movies) && is_object($obj_movies) && isset($obj_movies->tv_results[0])) {
                return $obj_movies->tv_results[0]->id;
            }
        }
        return null;
    }

    public function methodMovieImages($movie_id)
    {
        $url = $this->base_url . '/movie/' . $movie_id . '/images';
        $response = $this->client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->setData(['api_key' => $this->api_key])
            ->send();
        if ($response->isOk) {
            $obj_movies = json_decode($response->getContent());
            if (isset($obj_movies) && is_object($obj_movies) && isset($obj_movies->backdrops)) {
                return $obj_movies->backdrops;
            }
        }
        return null;
    }

    public function methodMovieExternalIds($movie_id)
    {
        $url = $this->base_url . '/movie/' . $movie_id . '/external_ids';
        $response = $this->client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->setData(['api_key' => $this->api_key])
            ->send();
        if ($response->isOk) {
            $obj_movies = json_decode($response->getContent());
            if (isset($obj_movies) && is_object($obj_movies)) {
                return $obj_movies;
            }
        }
        return null;
    }

    public function methodMovieCredits($movie_id)
    {
        $url = $this->base_url . '/movie/' . $movie_id . '/credits';
        $response = $this->client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->setData(['api_key' => $this->api_key])
            ->send();
        if ($response->isOk) {
            $obj_movies = json_decode($response->getContent());
            if (isset($obj_movies) && is_object($obj_movies)) {
                return $obj_movies;
            }
        }
        return null;
    }

    public function methodMovieSimilar($movie_id)
    {
        $url = $this->base_url . '/movie/' . $movie_id . '/similar';
        $response = $this->client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->setData(['api_key' => $this->api_key])
            ->send();
        if ($response->isOk) {
            $obj_movies = json_decode($response->getContent());
            if (isset($obj_movies) && is_object($obj_movies) && isset($obj_movies->results)) {
                return $obj_movies->results;
            }
        }
        return null;
    }

    public function methodPeopleDetails($person_id, $params = null)
    {
        $params['api_key'] = $this->api_key;

        $url = $this->base_url . '/' . 'person' . '/' .$person_id;
        $response = $this->client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->setData($params)
            ->send();
        if ($response->isOk) {
            $obj_movies = json_decode($response->getContent());
            if (isset($obj_movies) && is_object($obj_movies)) {
                return $obj_movies;
            }
        }
        return null;
    }

    public function getTmdIdBymethodFindTv($imdb)
    {
        $url = $this->base_url . '/find/' . $imdb;
        $response = $this->client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->setData(['api_key' => $this->api_key, 'external_source' => 'imdb_id'])
            ->send();
        if ($response->isOk) {
            $result = json_decode($response->getContent());
            if (isset($result) && is_object($result) && isset($result->tv_results) && isset($result->tv_results[0])) {
                if (!empty($result->tv_results[0]->id))
                    return $result->tv_results[0]->id;
            }
        }
        throw new Exception('TMDb (getTmdIdBymethodFindTv) dont find tv by imdb [' . $imdb .']');
    }
}
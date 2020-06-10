<?php

namespace app\common;

use yii\httpclient\Client;
use Yii;
use yii\base\BaseObject;

class Videocdn extends BaseObject
{
    private $api_token;
    private $method_movies;
    private $method_short;
    private $client;

    private static $instances = [];

    public function __construct($params = [], $config = [])
    {
        $this->api_token     = $params['api_token'] ?? Yii::$app->params['videocdn']['api_token'];
        $this->method_movies = $params['method_movies'] ?? Yii::$app->params['videocdn']['method_movies'];
        $this->method_short  = $params['method_short'] ?? Yii::$app->params['videocdn']['method_short'];

        $this->client = new Client();

        parent::__construct($config);
    }

    public function init()
    {
        parent::init();

        // ... инициализация происходит после того, как была применена конфигурация.
    }

    public static function getInstance(): Videocdn
    {
        $cls = static::class;
        if (!isset(static::$instances[$cls])) {
            static::$instances[$cls] = new static;
        }

        return static::$instances[$cls];
    }

    public function getMovies()
    {
        $response = $this->client->createRequest()
            ->setMethod('GET')
            ->setUrl($this->method_movies)
            ->setData(['api_token' => $this->api_token, 'limit' => 2])
            ->send();
        if ($response->isOk) {
            return $response->getContent();
        }
        return null;
    }

    public function getByKpId($kp_id)
    {
        $response = $this->client->createRequest()
            ->setMethod('GET')
            ->setUrl($this->method_short)
            ->setData(['api_token' => $this->api_token, 'kinopoisk_id' => $kp_id])
            ->send();
        if ($response->isOk) {
            $obj_movies = json_decode($response->getContent());
            if (isset($obj_movies->result) && $obj_movies->result == true && isset($obj_movies->data[0])) {
                return $obj_movies->data[0];
            }
        }
        return null;
    }

    public function getByImdbId($imdb_id)
    {
        $response = $this->client->createRequest()
            ->setMethod('GET')
            ->setUrl($this->method_short)
            ->setData(['api_token' => $this->api_token, 'imdb_id' => $imdb_id])
            ->send();
        if ($response->isOk) {
            $obj_movies = json_decode($response->getContent());
            if (isset($obj_movies->result) && $obj_movies->result == true) {
                return $obj_movies->data[0];
            }
        }
        return null;
    }
}
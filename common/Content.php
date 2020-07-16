<?php

namespace app\common;

use Exception;
use Yii;
use yii\base\BaseObject;
use yii\helpers\Console;

abstract class Content extends BaseObject
{
    public $img_limit;
    public $poster_quality;
    public $people_poster_quality;
    public $actors_limit;
    public $first_img_quality;
    public $img_quality;
    public $folder;

    public $stdout = [];

    public $content;

    public $kp_id;
    public $imdb_id;
    public $tmdb_id;
    public $title;

    public $videocdn;
    public $themoviedb;

    public $tmdbData;
    public $tmdbDataRU;

    public $video;
    public $externalIds;
    public $poster;
    public $kp_rating;
    public $imdb_rating;
    public $overview;
    public $imagesCount;

    abstract public function isDuplicate();
    abstract public function setTmdbData();
    abstract public function setIds();
    abstract public function saveContent();
    abstract public function saveActors();

    public function __construct($params = [], $config = [])
    {
        $this->kp_id    = $params['kp_id']    ?? null;
        $this->imdb_id  = $params['imdb_id']  ?? null;
        $this->tmdb_id  = $params['tmdb_id']  ?? null;
        $this->title    = $params['title'] ?? null;

        if (!$this->kp_id && !$this->imdb_id && !$this->tmdb_id && !$this->title) {
            throw new Exception('Not found ids for Content. Need one of this params: imdb, kp, tmdb, title');
        }

        $this->themoviedb = Themoviedb::getInstance();
        $this->videocdn = Videocdn::getInstance();

        parent::__construct($config);
    }

    public function saveImages()
    {
        $count = 0;

        foreach ($this->tmdbData->images->backdrops as $image){
            if ($count > ($this->img_limit-1)){
                break;
            }

            preg_match('/.(\w*)$/', $image->file_path, $matches);
            if (!isset($matches[0]))
            {
                $this->setStdout("Warning(parsing img): Image#" . ($count+1) . "not load: " . $image->file_path . "\n", Console::FG_YELLOW);
                continue;
            }

            if ($count == 0) {
                $imgSize = $this->first_img_quality;
            } else {
                $imgSize = $this->img_quality;
            }

            $folder = (int) ($this->content->id / 1000);
            $path = Yii::$app->basePath . '/web/i/' . $this->folder . '/s/' . $folder . '/';
            $url = 'https://image.tmdb.org/t/p/w' . $imgSize . $image->file_path;
            $status = file_put_contents($path . $this->content->id . '-' . ($count+1) . $matches[0], file_get_contents($url));

            if (empty($status)) {
                $this->setStdout("Warning(save img): Image#" . ($count+1) . "not load: " . $image->file_path . "\n", Console::FG_YELLOW);
                continue;
            }

            $count++;
        }

        return $count;
    }

    public function setImagesCount()
    {
        $total = count($this->tmdbData->images->backdrops);

        if ($total < $this->img_limit) {
            $this->imagesCount = $total;
        } else {
            $this->imagesCount = $this->img_limit;
        }
    }

    public function saveImagesCount()
    {
        $this->content->images = $this->imagesCount;
        if (!$this->content->save()) {
            throw new Exception('Save ImagesCount error' . print_r($this->content->errors));
        }
    }

    public function setVideo()
    {
        $videoRU = self::video($this->tmdbDataRU->videos->results);
        $this->video = ($videoRU) ? $videoRU : self::video($this->tmdbData->videos->results);
    }

    protected static function video(array $videos)
    {
        foreach ($videos as $video)
        {
            if ($video->site == "YouTube") {
                return $video->key;
            }
        }

        return null;
    }

    public function setStdout($str, $color)
    {
        array_push($this->stdout, [$str, $color]);
    }

    public function setOverview()
    {
        if (!empty($this->tmdbDataRU->overview)) {
            $this->overview = $this->tmdbDataRU->overview;
            return true;
        }

        if (!empty($this->tmdbData->overview)) {
            $this->overview = GoogleTranslate::translate($this->tmdbData->overview);
            return true;
        }

        $this->overview = null;

        $this->setStdout('Overview is empty', Console::FG_YELLOW);

        return false;
    }

    public function setPoster()
    {
        if ($this->tmdbDataRU->poster_path) {
            $this->poster = $this->tmdbDataRU->poster_path;
            return true;
        }

        if ($this->tmdbData->poster_path) {
            $this->poster = $this->tmdbDatmdbDatataRU->poster_path;
            return true;
        }

        throw new Exception('TMDb dont find poster for movie');
    }

    public function setExternalIds()
    {
        $tmdExternal = $this->tmdbData->external_ids;
        $externalArr = array($tmdExternal->facebook_id,$tmdExternal->instagram_id,$tmdExternal->twitter_id);
        $this->externalIds = ($externalArr) ? implode(",", $externalArr) : null;

        if ($externalArr) {
            $this->externalIds = implode(",", $externalArr);
        } else {
            $this->setStdout('externalIds is empty', Console::FG_YELLOW);
            $this->externalIds = null;
        }
    }

    public function setRating()
    {
        $names = ['kp_rating', 'imdb_rating'];

        try {
            $rating = [];
            if (!empty($this->kp_id)) {
                $xml = simplexml_load_file('https://rating.kinopoisk.ru/' . $this->kp_id . '.xml');
                foreach ($names as $name) {
                    $rating[$name] = dom_import_simplexml($xml->$name)->nodeValue;
                    $rating[$name] = sprintf("%01.1f", $rating[$name]) * 10;
                }
            }
        } catch (Exception $e) {
            foreach ($names as $name) {
                $rating[$name] = null;
            }
        }

        foreach ($names as $name)
        {
            if (!empty($rating[$name])) {
                $this->$name = $rating[$name];
            } else {
                $this->setStdout($name . ' is empty', Console::FG_YELLOW);
                $this->$name = null;
            }
        }
    }

    public function setIdsByKp()
    {
        $vcData = $this->videocdn->getByKpId($this->kp_id);

        $this->imdb_id = $vcData->imdb_id ?? null;
        $this->title = ($this->title) ? $this->title : ($vcData->title ?? null);
    }

    public function savePoster()
    {
        preg_match('/.(\w*)$/', $this->poster, $matches);
        if (!isset($matches[0]))
        {
            throw new Exception("Warning: Poster not load: " . $this->poster . "\n");
        }

        $folder = (int) ($this->content->id / 1000);
        $path = Yii::$app->basePath . '/web/i/' . $this->folder .'/p/' . $folder . '/';
        $poster_url = 'https://image.tmdb.org/t/p/w' . $this->poster_quality . $this->poster;

        $status = file_put_contents($path . $this->content->id . $matches[0], file_get_contents($poster_url));

        if (empty($status)) {
            throw new Exception("Warning: Poster not load: " . $this->poster . "\n");
        }

        return true;
    }

    public function saveActorPoster($id, $url)
    {
        preg_match('/.(\w*)$/', $url, $matches);
        if (!isset($matches[0]))
        {
            throw new Exception("Actor poster not load: " . $url . "\n");
        }

        $folder = (int) ($id / 1000);
        $path = Yii::$app->basePath . '/web/i/a/' . $folder . '/';
//        print_r(Yii::$app->basePath . '/web/media');die();
        $url = 'https://image.tmdb.org/t/p/w' . $this->people_poster_quality . $url;
        $status = file_put_contents($path . $id . $matches[0], file_get_contents($url));

        if (empty($status)) {
            throw new Exception("Actor poster not load: " . $url . "\n");
        }

        return true;
    }
}
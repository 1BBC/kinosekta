<?php

namespace app\common;

use app\models\Network;
use app\models\People;
use app\models\Tv;
use app\models\TvNetwork;
use app\models\TvPeople;
use Exception;
use Yii;
use yii\db\Query;
use yii\helpers\Console;

class AddTv extends Content
{
    static $IMG_LIMIT = 0;
    static $POSTER_QUALITY = 300;
    static $PEOPLE_POSTER_QUALITY = 200;
    static $ACTORS_LIMIT = 10;
    static $FIRST_IMG_QUALITY = 500;
    static $IMG_QUALITY = 300;
    static $FOLDER = 's';

    public function init()
    {
        parent::init();

        // ... инициализация происходит после того, как была применена конфигурация.
    }

    public function __construct($params = [], $config = [])
    {
        $this->img_limit              = self::$IMG_LIMIT;
        $this->poster_quality         = self::$POSTER_QUALITY;
        $this->people_poster_quality  = self::$PEOPLE_POSTER_QUALITY;
        $this->actors_limit           = self::$ACTORS_LIMIT;
        $this->first_img_quality      = self::$FIRST_IMG_QUALITY;
        $this->img_quality            = self::$IMG_QUALITY;
        $this->folder                 = self::$FOLDER;

        parent::__construct($params, $config);
    }

    public function isDuplicate()
    {
        return Tv::find()->where(['or', ['tmd_id' => $this->tmdb_id], ['imdb_id' => substr($this->imdb_id, 2)], ['kp_id' => $this->kp_id]])->exists();
    }

    public function add()
    {
        if ($this->isDuplicate()) {
            $this->setStdout("Tv exist\n", Console::FG_GREEN);
            return true;
        }

        if ($this->isBlocked()) {
            $this->setStdout("Tv blocked\n", Console::FG_GREEN);
            return true;
        }

        //Ids
        $this->setIds();

        //External Ids
        $this->setExternalIds();

        //YouTube Video
        $this->setVideo();

        //Images
        $this->setImagesCount();

        //Overview
        $this->setOverview();

        //Rating
        $this->setRating();

        //Save tv in DB
        $this->saveContent();

        try {
            $this->setPoster();

            //Poster
            $this->savePoster();

            $saveImagesCount = $this->saveImages();

            if ($saveImagesCount != $this->imagesCount) {
                $this->imagesCount = $saveImagesCount;
                $this->saveImagesCount();
            }

            //Save networks in DB
            $this->saveNetwork();

            //Save actors in DB
            $this->saveActors();
        } catch (Exception $e) {
            $this->content->delete();

            throw $e;
        }

        return true;
    }

    public function saveNetwork()
    {
        $networksArr = $this->tmdbData->networks;

        $networks = (new Query())
            ->select(['id', 'tmd_id'])
            ->from('network')
            ->where(['tmd_id' => array_column($networksArr, 'id')])
            ->indexBy('tmd_id')
            ->all();

        foreach ($networksArr as $network) {

            if(isset($networks[$network->id])) {
                $network_id = $networks[$network->id]['id'];
            } else {
                if (empty($network->logo_path || empty($network->name) || empty($network->id))) {
                    continue;
                }

                $value = [
                    'tmd_id'          => $network->id,
                    'name'            => $network->name,
                ];

                $obj_network = new Network();
                $obj_network->attributes = $value;

                if (!$obj_network->save()) {
                    $this->setStdout("Can`t Save network tmd: " .$network->id . ". M: " . print_r($obj_network->errors), Console::FG_YELLOW);
                    continue;
                }

                try {
                    self::saveNetworkPoster($obj_network->id, $network->logo_path);
                } catch (Exception $e) {
                    $this->setStdout("Can`t Save network tmd: " .$network->id . ". M: " . $e->getMessage(), Console::FG_YELLOW);
                    $obj_network->delete();
                    continue;
                }

                $network_id = $obj_network->id;
            }

            $tvNetwork = new TvNetwork();
            $tvNetwork->tv_id       = $this->content->id;
            $tvNetwork->network_id  = $network_id;

            if (!$tvNetwork->save()) {
                $this->setStdout('Save tvNetwork #' . $network->id .  ' error: ' . print_r($tvNetwork->errors), Console::FG_YELLOW);
            }
        }
    }

    private function saveNetworkPoster($id, $url)
    {
        preg_match('/.(\w*)$/', $url, $matches);
        if (!isset($matches[0]))
        {
            $this->setStdout("Warning: Actor network not load: " . $url . "\n", Console::FG_YELLOW);
            return;
        }

        $path = Yii::$app->basePath . '/web/i/n/';
//        print_r(Yii::$app->basePath . '/web/media');die();
        $url = 'https://image.tmdb.org/t/p/w300' . $url;
        $status = file_put_contents($path . $id . $matches[0], file_get_contents($url));

        if (empty($status)) {
            $this->setStdout("Warning: network poster not load: " . $url . "\n", Console::FG_YELLOW);
            return;
        }
    }

    public function setTmdbData()
    {
        $this->tmdb_id = $this->themoviedb->getTmdIdBymethodFindTv($this->imdb_id);

        $this->tmdbData = $this->themoviedb->methodTvDetails($this->tmdb_id, ['append_to_response' => 'videos,images,credits,external_ids']);
        $this->tmdbDataRU = $this->themoviedb->methodTvDetails($this->tmdb_id, ['language' => 'ru-RU', 'append_to_response' => 'videos']);
    }

    public function setIds()
    {
        if (!$this->tmdb_id) {
            if (!$this->imdb_id) {
                $this->setIdsByKp();
            }
        }

        $this->setTmdbData();
    }

    public function saveContent()
    {
        $attributes = [
            'tmd_id'                => $this->tmdb_id,
            'kp_id'                 => $this->kp_id ?? '',
            'imdb_id'               => substr($this->imdb_id, 2),
            'r_kp'                  => $this->kp_rating,
            'r_imdb'                => $this->imdb_rating,
            'first_air_date'        => $this->tmdbData->first_air_date,
            'title'                 => $this->title ?? $this->tmdbDataRU->name,
            'orig_title'            => $this->tmdbData->name,
            'overview'              => $this->overview,
            'external_ids'          => $this->externalIds,
            'episode_run_time'      => (!empty($this->tmdbData->episode_run_time[0])) ? $this->tmdbData->episode_run_time[0] : 0,
            'popularity'            => sprintf("%01.2f", $this->tmdbData->popularity) * 100,
            'images'                => $this->imagesCount,
            'video'                 => $this->video,
        ];

        $this->content = new Tv();
        $this->content->attributes = $attributes;
        $this->content->setGenres($this->tmdbData->genres);

        if (!$this->content->save()) {
            throw new Exception('Save tv in DB error' . print_r($this->content->errors));
        }

        return $this->content->id;
    }

    public function saveActors()
    {
        $tmdCast = $this->tmdbData->credits->cast;
        $castArr = self::castByTmdCast($tmdCast);

        $tmdCreators = $this->tmdbData->created_by;
        $creatorsArr = self::creatorsByTmdCreatedBy($tmdCreators);

        $creditsArr = array_merge($castArr,$creatorsArr);

        $peoples = (new Query())
            ->select(['id', 'tmd_id'])
            ->from('people')
            ->where(['tmd_id' => array_column($creditsArr, 'id')])
            ->indexBy('tmd_id')
            ->all();

        $newPeople = [];
        $addedPeople = [];

        $nameForTranslate = [];
        $biographyForTranslate = [];

        foreach ($creditsArr as $credit) {

            if(isset($peoples[$credit['id']])) {
                array_push($addedPeople,
                    [
                        'id' => $peoples[$credit['id']]['id'],
                        'role' => $credit['role'],
                    ]
                );
                continue;
            }

            $tmdbPeople = $this->themoviedb->methodPeopleDetails($credit['id'], ['language' => 'en-US', 'append_to_response' => 'translations']);

            if (empty($tmdbPeople->profile_path)
                || empty($tmdbPeople->gender)
                || empty($tmdbPeople->popularity)
                || empty($tmdbPeople->imdb_id)){
                continue;
            }

            $value = [
                'tmd_id'          => $tmdbPeople->id,
                'imdb_id'         => substr($tmdbPeople->imdb_id, 2),
                'name'            => $tmdbPeople->name,
                'birthday'        => $tmdbPeople->birthday,
                'deathday'        => $tmdbPeople->deathday,
                'place_of_birth'  => $tmdbPeople->place_of_birth,
                'popularity'      => sprintf("%01.2f", $tmdbPeople->popularity) * 100,
                'gender'          => $tmdbPeople->gender,
                //For MoviePeople
                'role'            => $credit['role'],
                'profile_path'    => $tmdbPeople->profile_path,
            ];

            //orig_name
            if(!empty($tmdbPeople->also_known_as)) {
                foreach ($tmdbPeople->also_known_as as $aka) {
                    if (preg_match('/[\p{Cyrillic}]/u', $aka)) {
                        $value['orig_name'] = $aka;
                        break;
                    }
                }
            }

            if (empty($value['orig_name'])) {
                $nameForTranslate[$tmdbPeople->id] = $tmdbPeople->name;
            }

            //biography
            if(!empty($tmdbPeople->translations->translations)) {
                foreach ($tmdbPeople->translations->translations as $translations) {
                    if ($translations->iso_3166_1 == 'RU') {
                        $value['biography'] = $translations->data->biography;
                    }
                }
            }

            if (empty($value['biography']) && !empty($tmdbPeople->biography))
            {
                $biographyForTranslate[$tmdbPeople->id] = $tmdbPeople->biography;
            }

            array_push($newPeople, $value);
        }

        $translateName = GoogleTranslate::translateKeyArr($nameForTranslate);
        $translateBiography = GoogleTranslate::translateKeyArr($biographyForTranslate);

        foreach ($addedPeople as $person) {
            $this->saveTvPeople($person['id'], $person['role']);
        }

        foreach ($newPeople as $person) {
            $people = new People();

            if (isset($translateName[$person['tmd_id']])) {
                $person['orig_name'] = $translateName[$person['tmd_id']];
            }

            if (isset($translateBiography[$person['tmd_id']])) {
                $person['biography'] = $translateBiography[$person['tmd_id']];
            }

            $people->attributes = $person;

            if (!$people->save()) {
                $this->setStdout("Can`t Save people tmd: " .$person['tmd_id'] . "\n", Console::FG_YELLOW);
                continue;
            }

            try {
                $this->saveActorPoster($people->id, $person['profile_path']);
            } catch (Exception $e) {
                $this->setStdout("Can`t Save actor poster by tmd: " . $people->tmd_id . ". M: " . $e->getMessage(), Console::FG_YELLOW);
                $people->delete();
                continue;
            }

            $this->saveTvPeople($people->id, $person['role']);
        }
    }

    private function saveTvPeople($id, $role)
    {
        $tv_people = new TvPeople();
        $tv_people->tv_id      = $this->content->id;
        $tv_people->people_id  = $id;
        $tv_people->role       = $role ?? '1';

        if (!$tv_people->save()) {
            $this->setStdout('Save movie_people #' . $id .  ' error: ' . print_r($tv_people->errors), Console::FG_YELLOW);
        }
    }

    private static function creatorsByTmdCreatedBy($tmdCreators)
    {
        $arr = array();
        $count = 0;

        foreach ($tmdCreators as $creator) {

            if (empty($creator->profile_path) || empty($creator->name)) {
                continue;
            }

            array_push($arr, ['id' => $creator->id, 'role' => null]);

            if ($count > 9) {
                break;
            }

            $count++;
        }

        return $arr;
    }

    private static function castByTmdCast(array $tmdCast)
    {
        $arr = array();
        $count = 0;

        foreach ($tmdCast as $actor) {

            if (empty($actor->profile_path) || empty($actor->character) || empty($actor->name)) {
                continue;
            }

            array_push($arr, ['id' => $actor->id, 'role' => $actor->character]);

            $count++;

            if ($count > self::$ACTORS_LIMIT) {
                break;
            }
        }

        return $arr;
    }
}
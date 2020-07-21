<?php

namespace app\common;

use app\models\Movie;
use app\models\MoviePeople;
use app\models\People;
use Exception;
use yii\db\Query;
use yii\helpers\Console;
use Yii;

class AddMovie extends Content
{
    static $IMG_LIMIT = 0;
    static $POSTER_QUALITY = 300;
    static $PEOPLE_POSTER_QUALITY = 200;
    static $ACTORS_LIMIT = 7;
    static $FIRST_IMG_QUALITY = 500;
    static $IMG_QUALITY = 300;
    static $FOLDER = 'f';

    public $tagline;

    public function init()
    {
        parent::init();

        // ... инициализация происходит после того, как была применена конфигурация.
    }

    public function isDuplicate()
    {
        return Movie::find()->where(['or', ['tmd_id' => $this->tmdb_id], ['imdb_id' => $this->imdb_id], ['kp_id' => $this->kp_id]])->exists();
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

    public function add()
    {
        if ($this->isDuplicate()) {
            $this->setStdout("Movie exist\n", Console::FG_GREEN);
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

        //Overview
        $this->setTagline();

        //Rating
        $this->setRating();

        //Save movie in DB
        $this->saveContent();

        try {
            //Poster
            $this->setPoster();
            //Poster
            $this->savePoster();

            $saveImagesCount = $this->saveImages();

            if ($saveImagesCount != $this->imagesCount) {
                $this->imagesCount = $saveImagesCount;
                $this->saveImagesCount();
            }

            //Save countries in DB
            $this->saveCountries();

            //Save actors in DB
            $this->saveActors();
        } catch (Exception $e) {
            $this->content->delete();
            throw $e;
        }

        return true;
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

    public function saveActors()
    {
        $creditsArr = self::arrPeoplesByMethodMovieCredits($this->tmdbData->credits);

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
                        'department' => $credit['department'],
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
                'department'      => $credit['department'],
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
            $this->saveMoviePeople($person['id'], $person['department'], $person['role']);
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

            $this->saveMoviePeople($people->id, $person['department'], $person['role']);
        }
    }

    public function saveMoviePeople($id, $department, $role)
    {
        $movie_people = new MoviePeople();
        $movie_people->movie_id   = $this->content->id;
        $movie_people->people_id  = $id;
        $movie_people->department = $department;
        $movie_people->role       = $role;

        if (!$movie_people->save()) {
            $this->setStdout('Save movie_people #' . $id .  ' error: ' . print_r($movie_people->errors), Console::FG_YELLOW);
        }
    }

    public function saveCountries()
    {
        $rows = array();
        $productionCountriesArr = array_column($this->tmdbData->production_countries, 'iso_3166_1');

        foreach ($productionCountriesArr as $country)
        {
            array_push($rows, [$this->content->id, $country]);
        }

        $count = Yii::$app->db->createCommand()->batchInsert('movie_country',
            ['movie_id', 'country'], $rows)->execute();

        return $count;
    }


    public function saveContent()
    {
        $attributes = [
            'tmd_id'                => $this->tmdb_id,
            'kp_id'                 => $this->kp_id ?? '',
            'imdb_id'               => substr($this->imdb_id, 2),
            'r_kp'                  => $this->kp_rating,
            'r_imdb'                => $this->imdb_rating,
            'release_date'          => $this->tmdbData->release_date,
            'runtime'               => $this->tmdbData->runtime,
            'title'                 => $this->title ?? $this->tmdbDataRU->title,
            'orig_title'            => $this->tmdbData->title,
            'tagline'               => $this->tagline,
            'overview'              => $this->overview,
            'external_ids'          => $this->externalIds,
            'popularity'            => sprintf("%01.2f", $this->tmdbData->popularity) * 100,
            'images'                => $this->imagesCount,
            'video'                 => $this->video,
        ];

        $this->content = new Movie();
        $this->content->attributes = $attributes;
        $this->content->setGenres($this->tmdbData->genres);

        if (!$this->content->save()) {
            throw new Exception('Save movie in DB error' . print_r($this->content->errors));
        }

        return $this->content->id;
    }

    public function setTagline()
    {
        if ($this->tmdbDataRU->tagline) {
            $this->tagline = $this->tmdbDataRU->tagline;

            return true;
        }

        if ($this->tmdbData->tagline) {
            $this->tagline = $this->tmdbData->tagline;

            return true;
        }

        $this->setStdout('Tagline is empty', Console::FG_YELLOW);

        return false;
    }

    public function setTmdbData()
    {
        $id = $this->tmdb_id ?? $this->imdb_id;
        $this->tmdbData = $this->themoviedb->methodMovieDetails($id, ['append_to_response' => 'videos,images,credits,external_ids']);
        $this->tmdbDataRU = $this->themoviedb->methodMovieDetails($id, ['language' => 'ru-RU', 'append_to_response' => 'videos']);

        $this->tmdb_id = $this->tmdbData->id;
    }

    private static function arrPeoplesByMethodMovieCredits($tmdCredits = null, array $cast = null, array $crew = null): array
    {
        $people = array();

        if (isset($tmdCredits)) {
            $cast = $tmdCredits->cast ?? null;
            $crew = $tmdCredits->crew ?? null;
        }

        if (isset($cast)) {
            $item = 0;

            while ($item < self::$ACTORS_LIMIT && $item < count($cast)-1) {
                array_push($people, ['id' => $cast[$item]->id, 'department' => 1, 'role' => $cast[$item]->character]);
                $item++;
            }
        }

        if (isset($crew)) {
            foreach ($crew as $item) {
                $job = self::isPeopleImportantJob($item);
                if ($job) {
                    array_push($people, ['id' => $item->id, 'department' => $job, 'role' => null]);
                }
            }
        }

        return $people;
    }

    private static function isPeopleImportantJob($people)
    {
        if (!isset($people->department) || !isset($people->job)) {
            return false;
        }

        if ($people->department == 'Directing' && $people->job == 'Director') {
            return 2;
            //return 'director';
        }

        if ($people->department == 'Production' && $people->job == 'Producer') {
            return 3;
            //return 'producer';
        }

        if ($people->department == 'Writing' && ($people->job == 'Story' || $people->job == 'Screenplay')) {
            return 4;
            //return 'writer';
        }

        if ($people->department == 'Camera' && $people->job == 'Director of Photography') {
            return 5;
            //return 'operator';
        }

        if ($people->department == 'Sound' && $people->job == 'Original Music Composer') {
            return 6;
            //return 'composer';
        }

        return false;
    }
}
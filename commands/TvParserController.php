<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Movie;
use app\models\MoviePeople;
use app\models\Network;
use app\models\People;
use app\common\Csv;
use app\common\Themoviedb;
use app\common\Videocdn;
use app\models\Tv;
use app\models\TvNetwork;
use app\models\TvPeople;
use Dejurin\GoogleTranslateForFree;
use Exception;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Query;
use yii\helpers\Console;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class TvParserController extends Controller
{
    public $debug;
    public $log;
    public $tmd;
    public $kp;
    public $imdb;

    public function options($actionID)
    {
        return ['debug', 'log', 'tmd', 'kp', 'imdb'];
    }

    public function optionAliases()
    {
        return ['d' => 'debug', 'l' => 'log', 'tmd' => 'tmd', 'kp' => 'kp', 'imdb' => 'imdb'];
    }

    public function actionTry()
    {
        print_r($this->getRating(1231231231)) ;
    }

    public function getRating( $id = null ) {
        $names = ['kp_rating', 'imdb_rating'];

        try {
            $rating = [];
            if ( ! empty( $id ) ) {
                $xml   = simplexml_load_file( 'https://rating.kinopoisk.ru/' . $id . '.xml' );
                foreach ( $names as $name ) {
                    $new_name            = str_replace( '_rating', '', $name );
                    $rating[ $new_name ] = dom_import_simplexml( $xml->$name )->nodeValue;
                    $rating[ $new_name ] = sprintf("%01.1f", $rating[ $new_name ]) * 10;
                }
            }
        } catch (Exception $e) {
            foreach ( $names as $name ) {
                $new_name            = str_replace( '_rating', '', $name );
                $rating[ $new_name ] = null;
            }
        }

        return $rating;
    }

    public function actionIndex($ids)
    {
        $ids = explode(',', $ids);

        $arrAdded    = array();
        $arrNotAdded = array();

        for ($i = 0; $i < count($ids); $i++)
        {
            $this->stdout("\n\n\n======= #" . $i . ' '. $ids[$i] . " =======\n", Console::FG_CYAN);
            try {
                $this->tvById($ids[$i]);
                $this->stdout("======= ADDED =======", Console::FG_GREEN);

            } catch (Exception $e) {
                $this->stdout("======= NOT ADDED =======\n", Console::FG_RED);
                $this->stdout($ids[$i] . ': ' .  $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(), Console::FG_RED);

//                echo $ids[$i] . ': ',  $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(), "\n";
                array_push($arrNotAdded, [$i, $ids[$i], $e->getMessage(), $e->getLine(), $e->getFile()]);
                continue;
            }

            array_push($arrAdded, [$i, $ids[$i]]);
        }

        if ($this->log)
        {
            $dir = Yii::getAlias('@runtime/parser/tv' . date('Y-m-d H'));
            if (!file_exists($dir)) {
                mkdir($dir);
            }

            if ($this->log > 1) {
                $added    = new Csv($dir . '/added.csv');

                if ($arrAdded)
                    $added->setCSV($arrAdded);
            }

            $notAdded = new Csv($dir . '/notAdded.csv');

            if ($arrNotAdded)
                $notAdded->setCSV($arrNotAdded);
        }

        return ExitCode::OK;
    }

    public function TvById($id)
    {
        if ($this->tmd) {
            $movie_exist = Tv::find()->where(['tmd_id' => $id])->exists();
        } elseif ($this->imdb) {;
            $movie_exist = Tv::find()->where(['imdb_id' => substr($id, 2)])->exists();
        } elseif ($this->kp) {
            $movie_exist = Tv::find()->where(['kp_id' => $id])->exists();
        }

        if ($movie_exist)
        {
            return true;
        }

        $videoCdn = Videocdn::getInstance();
        $theMovieDb = Themoviedb::getInstance();
        $vcData = null;

        if ($this->tmd) {
            $tmdId = $this->tmd;
        } else {
            if ($this->kp) {
                $vcData = $videoCdn->getByKpId($id);
            } elseif ($this->imdb) {
                $vcData = $videoCdn->getByImdbId($id);
            } else {
                throw new Exception('Dont set type of id: ' . $id);
            }

            if ($vcData == null) {
                throw new Exception('Videocdn dont find tv by id: ' . $id);
            }

            if ($vcData->type != 'serial') {
                throw new Exception('Videocdn find, but it`s not serial: ' . $id);
            }

            if (!isset($vcData->imdb_id) || $vcData->imdb_id == '') {
                throw new Exception('Videocdn imdb not set : ' . $id);
            }

            $imdbId = $vcData->imdb_id;
            $tmdId = $theMovieDb->getTmdIdBymethodFindTv($imdbId);
        }

        if (empty($tmdId)) {
            throw new Exception('TMdb dont find TV by imdb[' . $imdbId . ']: ' . $id);
        }

        $tmdData = $theMovieDb->methodTvDetails($tmdId, ['append_to_response' => 'videos,images,credits,similar,external_ids']);
        if ($tmdData == null){
            throw new Exception('TMDb dont find movie by tmd[' . $tmdId . ']: ' . $id);
        }

        $tmdDataRu = $theMovieDb->methodTvDetails($tmdId, ['language' => 'ru-RU', 'append_to_response' => 'videos']);
        if ($tmdDataRu == null){
            throw new Exception('TMDb-RU dont find movie by tmd[' .  $tmdId . ']: ' . $id);
        }

        $tmdExternalIds = $tmdData->external_ids;
//        $tmdSimilar = $tmdData->similar->results;
        $poster = self::notEmpty([$tmdDataRu->poster_path, $tmdData->poster_path]);
        if (empty($poster)) {
            throw new Exception('TMDb dont find poster for movie, id[' . $tmdId .']: ' . $id);
        }


        $images = self::imagesStrByMDD($tmdData->images->backdrops);
        $video = self::video($tmdData->videos->results);
        $externalIdsArr = array($tmdExternalIds->facebook_id,$tmdExternalIds->instagram_id,$tmdExternalIds->twitter_id);
        $externalIds = ($externalIdsArr) ? implode(",", $externalIdsArr) : null;

        $episode_run_time = (!empty($tmdData->episode_run_time[0])) ? $tmdData->episode_run_time[0] : 0;

        $tmdCast = $tmdData->credits->cast;
        $castArr = self::castByTmdCast($tmdCast);

        $tmdCreators = $tmdData->created_by;
        $creatorsArr = self::creatorsByTmdCreatedBy($tmdCreators);

        $creditsArr = array_merge($castArr,$creatorsArr);
        $networksArr = $tmdData->networks;

        $rating = array();
        if (isset($vcData->kp_id)) {
            $rating = $this->getRating($vcData->kp_id);
        }

//        $productionCountriesArr = array_column($tmdData->production_countries, 'iso_3166_1');

        $values = [
            'tmd_id'                  => $tmdData->id,
            'kp_id'                   => $vcData->kp_id ?? '',
            'imdb_id'                 => substr($vcData->imdb_id, 2),
            'r_kp'                    => $rating['kp'] ?? null,
            'r_imdb'                  => $rating['imdb'] ?? null,
            'first_air_date'          => $tmdData->first_air_date,
            'title'                   => $tmdDataRu->name,
            'orig_title'              => $tmdData->name,
            'overview'                => self::originalOrTranslate($tmdDataRu->overview, $tmdData->overview),
            'external_ids'            => $externalIds,
//            'similar_tv'              => self::strSimilarByMethodMovieSimilar($tmdSimilar),
            'episode_run_time'        => $episode_run_time,
            'poster'                  => substr(self::notEmpty([$tmdDataRu->poster_path, $tmdData->poster_path]), 1, 27),
            'popularity'              => sprintf("%01.2f", $tmdData->popularity) * 100,
            'images'                  => $images,
            'video'                   => $video,
        ];

        $start = microtime(true);
        $tv = new Tv();
        $tv->attributes = $values;
        $tv->setGenres($tmdData->genres);

        if (!$tv->save()) {
            throw new Exception('Save tv error' . print_r($tv->errors));
        }

        $this->stdout("Tv time : " . (microtime(true) - $start) . "\n", Console::FG_BLUE);

        $start = microtime(true);
        self::savePoster($tv->id, $poster);
        $this->stdout("Poster time : " . (microtime(true) - $start) . "\n", Console::FG_BLUE);

        $start = microtime(true);
        $imgCount = self::saveImages($tv->id, $tmdData->images->backdrops);
        $tv->images = $imgCount;
        if (!$tv->save()) {
            throw new Exception('Save movie images error' . print_r($tv->errors));
        }
        $this->stdout("Images count : " . $imgCount . "\n", Console::FG_GREY);
        $this->stdout("Images time : " . (microtime(true) - $start) . "\n", Console::FG_BLUE);

//        $start = microtime(true);
//        self::countriesByArr($movie->id, $productionCountriesArr);
//        $this->stdout("Country time : " . (microtime(true) - $start) . "\n", Console::FG_BLUE);
        self::addCredits($tv->id, $creditsArr);
        self::addNetworks($tv->id, $networksArr);
    }

    private function saveImages($id, $tmdImages)
    {
        $count = 0;

        foreach ($tmdImages as $image){
            if ($count > 2){
                break;
            }

            preg_match('/.(\w*)$/', $image->file_path, $matches);
            if (!isset($matches[0]))
            {
                $this->stdout("Warning(parsing img): Image#" . ($count+1) . "not load: " . $image->file_path . "\n", Console::FG_YELLOW);
                continue;
            }

            if ($count == 0) {
                $imgSize = 500;
            } else {
                $imgSize = 300;
            }

            $folder = (int) ($id / 1000);
            $path = Yii::$app->basePath . '/web/i/s/s/' . $folder . '/';
            $url = 'https://image.tmdb.org/t/p/w' . $imgSize . $image->file_path;
            $status = file_put_contents($path . $id . '-' . ($count+1) . $matches[0], file_get_contents($url));

            if (empty($status)) {
                $this->stdout("Warning(save img): Image#" . ($count+1) . "not load: " . $image->file_path . "\n", Console::FG_YELLOW);
                continue;
            }

            $count++;
        }

        return $count;
    }

    private function saveActorPoster($id, $url)
    {
        preg_match('/.(\w*)$/', $url, $matches);
        if (!isset($matches[0]))
        {
            $this->stdout("Warning: Actor poster not load: " . $url . "\n", Console::FG_YELLOW);
            return;
        }

        $folder = (int) ($id / 1000);
        $path = Yii::$app->basePath . '/web/i/a/' . $folder . '/';
//        print_r(Yii::$app->basePath . '/web/media');die();
        $url = 'https://image.tmdb.org/t/p/w200' . $url;
        $status = file_put_contents($path . $id . $matches[0], file_get_contents($url));

        if (empty($status)) {
            $this->stdout("Warning:  Actor poster not load: " . $url . "\n", Console::FG_YELLOW);
            return;
        }
    }

    private function saveNetworkPoster($id, $url)
    {
        preg_match('/.(\w*)$/', $url, $matches);
        if (!isset($matches[0]))
        {
            $this->stdout("Warning: Actor network not load: " . $url . "\n", Console::FG_YELLOW);
            return;
        }

        $path = Yii::$app->basePath . '/web/i/n/';
//        print_r(Yii::$app->basePath . '/web/media');die();
        $url = 'https://image.tmdb.org/t/p/w300' . $url;
        $status = file_put_contents($path . $id . $matches[0], file_get_contents($url));

        if (empty($status)) {
            $this->stdout("Warning: network poster not load: " . $url . "\n", Console::FG_YELLOW);
            return;
        }
    }
    
    private function savePoster($id, $url)
    {
        preg_match('/.(\w*)$/', $url, $matches);
        if (!isset($matches[0]))
        {
            $this->stdout("Warning: Poster not load: " . $url . "\n", Console::FG_YELLOW);
            return;
        }

        $folder = (int) ($id / 1000);
        $path = Yii::$app->basePath . '/web/i/s/p/' . $folder . '/';
//        print_r(Yii::$app->basePath . '/web/media');die();
        $url = 'https://image.tmdb.org/t/p/w300' . $url;
        $status = file_put_contents($path . $id . $matches[0], file_get_contents($url));

        if (empty($status)) {
            $this->stdout("Warning: Poster not load: " . $url . "\n", Console::FG_YELLOW);
            return;
        }
    }

    private function addCredits(int $tv_id, array $creditsArr)
    {
        $theMovieDb = Themoviedb::getInstance();
        $count = 0;

        $peoples = (new Query())
            ->select(['id', 'tmd_id'])
            ->from('people')
            ->where(['tmd_id' => array_column($creditsArr, 'tmd_id')])
            ->indexBy('tmd_id')
            ->all();

        foreach ($creditsArr as $credit) {

            if(!isset($peoples[$credit['tmd_id']])) {
                $tmdPeople = $theMovieDb->methodPeopleDetails($credit['tmd_id']);
                $tmdPeopleRU = $theMovieDb->methodPeopleDetails($credit['tmd_id'], ['language' => 'ru-RU']);

                $valuesPeople = [
                    'tmd_id'          => $tmdPeople->id,
                    'imdb_id'         => substr($tmdPeople->imdb_id, 2),
                    'name'            => $tmdPeople->name,
                    'orig_name'       => self::translate($tmdPeople->name),
                    'birthday'        => $tmdPeople->birthday,
                    'deathday'        => $tmdPeople->deathday,
                    'place_of_birth'  => $tmdPeople->place_of_birth,
                    'popularity'      => sprintf("%01.2f", $tmdPeople->popularity) * 100,
                    'biography'       => self::originalOrTranslate($tmdPeopleRU->biography, $tmdPeople->biography) ?? 'будет',
                    'gender'          => $tmdPeople->gender,
                ];

                $people = new People();
                $people->attributes = $valuesPeople;

                if (!$people->save()) {
                    $this->stdout("Can`t Save people tmd: " .$tmdPeople->id . "\n", Console::FG_YELLOW);
                    continue;
                }

                self::saveActorPoster($people->id, $tmdPeople->profile_path);

                $people_id = $people->id;
            } else {
                $people_id = $peoples[$credit['tmd_id']]['id'];
            }

            $tv_prople = new TvPeople();
            $tv_prople->tv_id      = $tv_id;
            $tv_prople->people_id  = $people_id;
            $tv_prople->role       = (empty($credit['role'])) ? '1' : $credit['role'];

            if (!$tv_prople->save()) {
//                $this->stdout("Can`t Save people tmd: " .$tmdPeople->id . "\n", Console::FG_YELLOW);
                throw new Exception('Save tv_people #' . $credit['tmd_id'] .  ' error: ' . print_r($tv_prople->errors)  . '\n' . 'try: ' . print_r($tv_prople->attributes));
            }

            $count++;
        }

        $this->stdout("Credit count : " . $count . "\n", Console::FG_GREY);
    }

    private function addNetworks(int $tvId, $networksArr)
    {
        $count = 0;

        $networks = (new Query())
            ->select(['id', 'tmd_id'])
            ->from('network')
            ->where(['tmd_id' => array_column($networksArr, 'id')])
            ->indexBy('tmd_id')
            ->all();

        foreach ($networksArr as $network) {

            if(!isset($networks[$network->id])) {
                if (empty($network->logo_path || empty($network->name) || empty($network->id))) {
                    continue;
                }

                $value = [
                    'tmd_id'          => $network->id,
                    'name'            => $network->name,
//                    'logo_path'       => substr($network->logo_path, 1, 27),
                ];

                $obj_network = new Network();
                $obj_network->attributes = $value;

                if (!$obj_network->save()) {
//                    $this->stdout("Can`t Save network tmd: " .$network->id . "\n", Console::FG_YELLOW);
                    echo 'Warning: Can`t Save network #' . $network->id . ' : ' . print_r($obj_network->errors);
                    //throw new Exception('Save people #' . $credit['id'] .  ' error: ' . print_r($people->errors));
                    continue;
                }

                self::saveNetworkPoster($obj_network->id, $network->logo_path);

                $network_id = $obj_network->id;
            } else {
                $network_id = $networks[$network->id]['id'];
            }

            $tvNetwork = new TvNetwork();
            $tvNetwork->tv_id       = $tvId;
            $tvNetwork->network_id  = $network_id;

            if (!$tvNetwork->save()) {
//                $this->stdout("Can`t Save people tmd: " .$tmdPeople->id . "\n", Console::FG_YELLOW);
                throw new Exception('Save tvNetwork #' . $network->id .  ' error: ' . print_r($tvNetwork->errors)  . '\n' . 'try: ' . print_r($tvNetwork->attributes));
            }

            $count++;
        }

        $this->stdout("Network count : " . $count . "\n", Console::FG_GREY);
    }

    private static function translate($text, $source = 'en', $target = 'ru', $attempts = 3)
    {
        $tr = new GoogleTranslateForFree();
        $result = $tr->translate($source, $target, $text, $attempts);

        return $result;
    }
    private static function notEmpty(array $arr)
    {
        foreach ($arr as $item){
            if (!empty($item))
                return $item;
        }

        return null;
    }
    private static function originalOrTranslate($orThis, $orThisTranslate)
    {
        if (!empty($orThis))
            return $orThis;

        if (empty($orThisTranslate))
            return null;

        return self::translate($orThisTranslate);
    }
    private static function imagesStrByMDD(array $tmdImages)
    {
        $images = array();
        $count = 0;

        foreach ($tmdImages as $image){
            if ($count > 5){
                break;
            }
            array_push($images, substr($image->file_path, 1, 27));
            $count++;
        }

        return implode(",", $images);
    }
    private static function video(array $videos)
    {
        foreach ($videos as $video)
        {
            if ($video->site == "YouTube") {
                return $video->key;
            }
        }

        return null;
    }
    private static function strSimilarByMethodMovieSimilar(array $similar)
    {
        $item = 0;
        $arr = array();

        while ($item < 8 && $item < count($similar)-1) {
            array_push($arr, $similar[$item]->id);
            $item++;
        }

        return implode(",", $arr);
    }
    private static function castByTmdCast(array $tmdCast)
    {
        $arr = array();
        $count = 0;

        foreach ($tmdCast as $actor) {

            if (empty($actor->profile_path) || empty($actor->character) || empty($actor->name)) {
                continue;
            }

            array_push($arr, ['tmd_id' => $actor->id, 'role' => $actor->character]);

            if ($count > 9) {
                break;
            }

            $count++;
        }

        return $arr;
    }

    private static function creatorsByTmdCreatedBy($tmdCreators)
    {
        $arr = array();
        $count = 0;

        foreach ($tmdCreators as $creator) {

            if (empty($creator->profile_path) || empty($creator->name)) {
                continue;
            }

            array_push($arr, ['tmd_id' => $creator->id, 'role' => null]);

            if ($count > 9) {
                break;
            }

            $count++;
        }

        return $arr;
    }
}

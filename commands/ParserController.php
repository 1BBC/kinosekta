<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Movie;
use app\models\MoviePeople;
use app\models\People;
use app\common\Csv;
use app\common\Themoviedb;
use app\common\Videocdn;
use Dejurin\GoogleTranslateForFree;
use Exception;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Query;
use yii\helpers\Console;
use yii\web\UploadedFile;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ParserController extends Controller
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

    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionTry()
    {
       print_r($this->getRating(1043758));
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

    public function actionTest()
    {
        sleep(50);
    }

    public function actionImg()
    {
        $p = (int) (6 / 1000);
        print_r($p);
    }
    //php yii parser/index `php yii parser/get` -kp=1
    public function actionGet()
    {
        $arr = array();
        for ($i = 1050; $i < 1150; $i++) {
            array_push($arr, $i);
        }

        echo implode(',', $arr);
    }

    public function actionEcho($ids)
    {
        echo '111';
        $ids = explode(',', $ids);

        $by100 = array_chunk($ids, 10);

        foreach ($by100 as $arr100) {
            $by10 = array_chunk($arr100, 2);

            $commands = array();

            foreach ($by10 as $arr10) {
                echo getmypid() . "\n";
                echo exec('ps ax -Ao ppid | grep ' . getmypid());
                `ps ax -Ao ppid | grep `;
                array_push($commands, 'php yii parser/test');
//                array_push($commands, 'php yii parser/index ' . implode(",", $arr10) . ' -l=2');
            }
//            echo implode(" & ", $commands);
            exec(implode(" & ", $commands));
            sleep(40);
        }

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
                $this->movieById($ids[$i]);
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
            $dir = Yii::getAlias('@runtime/parser/' . date('Y-m-d H'));
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

    public function movieById($id)
    {
        if ($this->tmd) {
            $movie_exist = Movie::find()->where(['tmd_id' => $id])->exists();
        } elseif ($this->imdb) {;
            $movie_exist = Movie::find()->where(['imdb_id' => substr($id, 2)])->exists();
        } elseif ($this->kp) {
            $movie_exist = Movie::find()->where(['kp_id' => $id])->exists();
        }

        if ($movie_exist)
        {
            return true;
        }

        $videoCdn = Videocdn::getInstance();
        $theMovieDb = Themoviedb::getInstance();
        $vcData = null;

        if ($this->tmd) {
            $idForTmd = $id;
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

            if ($vcData->type != 'movie') {
                throw new Exception('Videocdn find, but it`s not movie: ' . $id);
            }

            if (!isset($vcData->imdb_id) || $vcData->imdb_id == '') {
                throw new Exception('Videocdn imdb not set : ' . $id);
            }

            $idForTmd = $vcData->imdb_id;
        }

        $tmdData = $theMovieDb->methodMovieDetails($idForTmd, ['append_to_response' => 'videos,images,credits,releases,external_ids']);
        if ($tmdData == null){
            throw new Exception('TMDb dont find movie by id[' . $idForTmd .']: ' . $id);
        }

        $tmdDataRu = $theMovieDb->methodMovieDetails($idForTmd, ['language' => 'ru-RU', 'append_to_response' => 'videos']);
        if ($tmdDataRu == null){
            throw new Exception('TMDb-RU dont find movie by id[' . $idForTmd . ']: ' . $id);
        }

        $tmdExternalIds = $tmdData->external_ids;
//        $tmdSimilar = $tmdData->similar->results;

        $productionCountriesArr = array_column($tmdData->production_countries, 'iso_3166_1');
        $images = self::imagesStrByMDD($tmdData->images->backdrops);
        $video = self::video($tmdData->videos->results);

        $externalIdsArr = array($tmdExternalIds->facebook_id,$tmdExternalIds->instagram_id,$tmdExternalIds->twitter_id);
        $externalIds = ($externalIdsArr) ? implode(",", $externalIdsArr) : null;

        $tmdCredits = $tmdData->credits;
        $creditsArr = self::arrPeoplesByMethodMovieCredits($tmdCredits);

        $rating = array();

        $poster = self::notEmpty([$tmdDataRu->poster_path, $tmdData->poster_path]);
        if (empty($poster)) {
            throw new Exception('TMDb dont find poster for movie, id[' . $idForTmd .']: ' . $id);
        }

        if (!empty($vcData->kp_id)) {
            $rating = $this->getRating($vcData->kp_id);
        }

        $values = [
            'tmd_id'                => $tmdData->id,
            'kp_id'                 => $vcData->kp_id ?? '',
            'imdb_id'               => substr($tmdData->imdb_id, 2),
            'r_kp'                  => $rating['kp'] ?? null,
            'r_imdb'                => $rating['imdb'] ?? null,
            'release_date'          => $tmdData->release_date,
            'runtime'               => $tmdData->runtime,
            'title'                 => $tmdDataRu->title,
            'orig_title'            => $tmdDataRu->original_title,
            'tagline'               => self::notEmpty([$tmdDataRu->tagline, $tmdData->tagline]),
            'overview'              => self::originalOrTranslate($tmdDataRu->overview, $tmdData->overview),
//            'budget'                => $tmdData->budget,
//            'revenue'               => $tmdData->revenue,
            'external_ids'          => $externalIds,
//            'similar_movies'        => self::strSimilarByMethodMovieSimilar($tmdSimilar),
//            'poster'                => substr(self::notEmpty([$tmdDataRu->poster_path, $tmdData->poster_path]), 1, 27),
            'popularity'            => sprintf("%01.2f", $tmdData->popularity) * 100,
            'images'                => $images,
            'video'                 => $video,
        ];

//        if (empty())

//        print_r($values);die();

        $start = microtime(true);
        $movie = new Movie();
        $movie->attributes = $values;
        $movie->setGenres($tmdData->genres);

        if (!$movie->save()) {
            throw new Exception('Save movie error' . print_r($movie->errors));
        }

        $this->stdout("Movie time : " . (microtime(true) - $start) . "\n", Console::FG_BLUE);

        $start = microtime(true);
        self::savePoster($movie->id, $poster);
        $this->stdout("Poster time : " . (microtime(true) - $start) . "\n", Console::FG_BLUE);

        $start = microtime(true);
        $imgCount = self::saveImages($movie->id, $tmdData->images->backdrops);
        $movie->images = $imgCount;
        if (!$movie->save()) {
            throw new Exception('Save movie images error' . print_r($movie->errors));
        }
        $this->stdout("Images count : " . $imgCount . "\n", Console::FG_GREY);
        $this->stdout("Images time : " . (microtime(true) - $start) . "\n", Console::FG_BLUE);

        $start = microtime(true);
        self::countriesByArr($movie->id, $productionCountriesArr);
        $this->stdout("Country time : " . (microtime(true) - $start) . "\n", Console::FG_BLUE);

        $start = microtime(true);
        self::creditByImdbID($movie->id, $creditsArr);
        $this->stdout("Credit time : " . (microtime(true) - $start) . "\n", Console::FG_BLUE);
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

    private function savePoster($id, $url)
    {
        preg_match('/.(\w*)$/', $url, $matches);
        if (!isset($matches[0]))
        {
            $this->stdout("Warning: Poster not load: " . $url . "\n", Console::FG_YELLOW);
            return;
        }

        $folder = (int) ($id / 1000);
        $path = Yii::$app->basePath . '/web/i/f/p/' . $folder . '/';
//        print_r(Yii::$app->basePath . '/web/media');die();
        $url = 'https://image.tmdb.org/t/p/w300' . $url;
        $status = file_put_contents($path . $id . $matches[0], file_get_contents($url));

        if (empty($status)) {
            $this->stdout("Warning: Poster not load: " . $url . "\n", Console::FG_YELLOW);
            return;
        }
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
            $path = Yii::$app->basePath . '/web/i/f/s/' . $folder . '/';
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

    private function countriesByArr(int $movie_id, array $productionCountriesArr)
    {
        $rows = array();

        foreach ($productionCountriesArr as $country)
        {
            array_push($rows, [$movie_id, $country]);
        }

        $count = Yii::$app->db->createCommand()->batchInsert('movie_country',
            ['movie_id', 'country'], $rows)->execute();

        $this->stdout("Country count : " . $count . "\n", Console::FG_GREY);
    }

    public function creditByImdbID(int $movie_id, array $creditsArr)
    {
        $theMovieDb = Themoviedb::getInstance();
        $count = 0;

        $peoples = (new Query())
            ->select(['id', 'tmd_id'])
            ->from('people')
            ->where(['tmd_id' => array_column($creditsArr, 'id')])
            ->indexBy('tmd_id')
            ->all();

        foreach ($creditsArr as $credit) {

            if(!isset($peoples[$credit['id']])) {
                $tmdPeople = $theMovieDb->methodPeopleDetails($credit['id']);
                $tmdPeopleRU = $theMovieDb->methodPeopleDetails($credit['id'], ['language' => 'ru-RU']);

                if (empty($tmdPeople->profile_path)){
                    continue;
                }

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
//                    echo 'Warning: Can`t Save people #' . $credit['id'] . ' : ' . print_r($people->errors);
                    //throw new Exception('Save people #' . $credit['id'] .  ' error: ' . print_r($people->errors));
                    continue;
                }

                self::saveActorPoster($people->id, $tmdPeople->profile_path);

                $people_id = $people->id;
            } else {
                $people_id = $peoples[$credit['id']]['id'];
            }



            $movie_people = new MoviePeople();
            $movie_people->movie_id   = $movie_id;
            $movie_people->people_id  = $people_id;
            $movie_people->department = $credit['department'];
            $movie_people->role = $credit['role'];

            if (!$movie_people->save()) {
                $this->stdout('Save movie_people #' . $credit['id'] .  ' error: ' . print_r($movie_people->errors)  . '\n' . 'try: ' . print_r($movie_people->attributes), Console::FG_YELLOW);
//                throw new Exception('Save movie_people #' . $credit['id'] .  ' error: ' . print_r($movie_people->errors)  . '\n' . 'try: ' . print_r($movie_people->attributes));
            }

            $count++;
        }

        $this->stdout("Credit count : " . $count . "\n", Console::FG_GREY);
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

    private static function arrColumnImplode(array $arr, string $column, int $offset = null): string
    {
        if (isset($offset)) {
            $arr = array_splice($arr, $offset);
        }

        return implode(",", array_column($arr, $column));
    }

    private static function arrPeoplesByMethodMovieCredits(object $tmdCredits = null, array $cast = null, array $crew = null): array
    {
        $people = array();

        if (isset($tmdCredits)) {
            $cast = $tmdCredits->cast ?? null;
            $crew = $tmdCredits->crew ?? null;
        }

        if (isset($cast)) {
            $item = 0;

            while ($item < 10 && $item < count($cast)-1) {
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

    private static function isPeopleImportantJob(object $people)
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
}

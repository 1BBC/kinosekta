<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\common\AddMovie;
use app\common\AddTv;
use app\common\Csv;
use app\common\GoogleTranslate;
use app\common\Themoviedb;
use app\common\Videocdn;
use app\models\Movie;
use Exception;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class MainParserController extends Controller {
    
    private $videoCdn;
    
    public function __construct($id, $module, $config = [])
    {
        $this->videoCdn = Videocdn::getInstance();
        
        parent::__construct($id, $module, $config);
    }

    public function actionCdn($movie, $first, $last = null, $year=2019, $total = false)
    {
        if (empty($last)) $last=$first;

        $count = 0;

        for ($page = $first; $page <= $last; $page++)
        {
            $this->stdout("======================================================================\n", Console::FG_PURPLE);
            $this->stdout("========================= Page:  {$page} ===================================\n", Console::FG_PURPLE);


            try {
                if ($movie == 1) {
                    $vcData = $this->videoCdn->getMovies(['page' => $page, 'year' => $year]);
                } else {
                    $vcData = $this->videoCdn->getTvs(['page' => $page, 'year' => $year]);
                }
            } catch (Exception $e) {
                $this->stdout("ERR: " . $e->getMessage()  ."\n", Console::FG_RED);
                break;
            }

            if ($total) {
                $last = $vcData->last_page;
            }

            $this->stdout("========================= Total:  {$vcData->last_page} =================================\n", Console::FG_PURPLE);

            foreach ($vcData->data as $content) {
                $count++;

                $params['kp_id']   = $content->kinopoisk_id ?? null;
                $params['imdb_id'] = $content->imdb_id      ?? null;
                $params['title']   = $content->ru_title     ?? null;
                $params['type']    = ($movie == 1) ? 'movie' : 'tv_series';

                $this->tryWrap($count, 'one', $params);
            }
        }
    }

    public function actionKp($ids)
    {
        $ids = explode(',', $ids);

        for ($i = 0; $i < count($ids); $i++)
        {
            try {
                $vcData = (array) $this->videoCdn->getByKpId($ids[$i]);
            } catch (Exception $e) {
                $this->stdout("Don`t find (videoCdn->getByKpId) by kp " . $ids[$i] ."\n", Console::FG_RED);
                continue;
            }

            $this->tryWrap($i+1, 'one', $vcData);
        }

        return ExitCode::OK;
    }

    public function actionImdb($ids)
    {
        $ids = explode(',', $ids);

        for ($i = 0; $i < count($ids); $i++)
        {
            try {
                $vcData = (array) $this->videoCdn->getByImdbId($ids[$i]);
            } catch (Exception $e) {
                $this->stdout("Don`t find (videoCdn->getByImdbId) by imdb " . $ids[$i] ."\n", Console::FG_RED);
                continue;
            }


            $this->tryWrap($i+1, 'one', $vcData);
        }

        return ExitCode::OK;
    }

    public function tryWrap($count, $functionName, $params)
    {
        $startStr = $this->startStr($params);

        $this->stdout("\n\n\n======= #" . $count . ' '. $startStr . " =======\n", Console::FG_CYAN);
        try {
            $this->$functionName($params);
            $this->stdout("======= ADDED =======", Console::FG_GREEN);

        } catch (Exception $e) {
            $this->stdout('M: ' . $e->getMessage() . ' L:' . $e->getLine() . ' F:' . $e->getFile() . "\n", Console::FG_RED);
            $this->stdout("\n======= NOT ADDED =======\n", Console::FG_RED);
        }
    }

    public function one($params)
    {
        if ($params['type'] == 'movie') {
            $content = new AddMovie(
                $params
            );
        } else {
            $content = new AddTv(
                $params
            );
        }

        $content->add();

        foreach ($content->stdout as $std) {
            $this->stdout($std[0] . "\n", $std[1]);
        }
    }

    public function startStr($params)
    {
        $startStr = '';

        if (!empty($params['kp_id'])){
            $startStr = ' kp_id: ' . $params['kp_id'];
        }

        if (!empty($params['imdb_id'])){
            $startStr .= ' imdb_id: ' . $params['imdb_id'];
        }

        if (!empty($params['tmdb_id'])){
            $startStr .= ' tmdb_id: ' . $params['tmdb_id'];
        }

        return $startStr;
    }
}
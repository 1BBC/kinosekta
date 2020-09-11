<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\common\AddMovie;
use app\models\Movie;
use DateTime;
use Exception;
use yii\console\Controller;
use yii\db\Query;
use yii\helpers\Console;

class UpdateController extends Controller {

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
    }

    public function actionMRating($year = null)
    {
        $movies = (new Query())->select(['id', 'r_kp', 'r_imdb', 'kp_id'])->from('movie');

        if (!empty($year)) {
            $movies
                ->where(['>', 'release_date', $year . '-01-01'])
                ->andWhere(['<', 'release_date', $year + 1 . '-01-01']);
        } else {
            $date = (new DateTime())->modify('-12 month')->format('Y-m-d');
            $movies->where(['>', 'release_date', $date]);
        }

        $movies
            ->andWhere(['r_kp' => null])
            ->orderBy(['id' => SORT_DESC]);

        foreach ($movies->each() as $content) {
            $this->stdout("\n\n\n======= #" . $content['id'] . " =======\n", Console::FG_CYAN);

            try {
                $addMovie = new AddMovie(['kp_id' => $content['kp_id']]);
                $addMovie->setRating();

                if (empty($addMovie->kp_rating)) continue;

                $movie = Movie::findOne($content['id']);
                $movie->r_kp = $addMovie->imdb_rating;
                $movie->r_kp = $addMovie->kp_rating;

//                $movie->save();
                $this->stdout("======= SET NEW =======", Console::FG_GREEN);
            } catch (Exception $e) {
                $this->stdout('M: ' . $e->getMessage() . ' L:' . $e->getLine() . ' F:' . $e->getFile() . "\n", Console::FG_RED);
                $this->stdout("\n======= NOT SET =======\n", Console::FG_RED);
            }
        }
    }
}
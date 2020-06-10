<?php

namespace app\modules\frontend\controllers;

use app\modules\frontend\common\Videocdn;
use Dejurin\GoogleTranslateForFree;
use Yii;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\web\Controller;
use yii\web\HttpException;

/**
 * Default controller for the `frontend` module
 */
class DefaultController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // путь к директории layouts модуля
        \Yii::$app->setLayoutPath('@app/modules/frontend/views/layouts');
        \Yii::$app->layout = 'main';
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionMovies()
    {
        $json_movies = Videocdn::getInstance()->getMovies();
        $obj_movies  = json_decode($json_movies);
        var_dump($obj_movies->data[0]->media);


        return $this->render('movies', ['json_movies' => $json_movies]);
    }

    public function actionKp()
    {
        $movie = Videocdn::getInstance()->getByKpId(718222);

        var_dump($movie);
        return;
    }

    public function actionTranslate()
    {
        $source = 'en';
        $target = 'ru';
        $attempts = 5;
        $text = 'A ticking-time-bomb insomniac and a slippery soap salesman channel primal male aggression into a shocking new form of therapy. Their concept catches on, with underground \"fight clubs\" forming in every town, until an eccentric gets in the way and ignites an out-of-control spiral toward oblivion.';

        $tr = new GoogleTranslateForFree();
        $result = $tr->translate($source, $target, $text, $attempts);
        var_dump($result);
        return $result;
    }
}

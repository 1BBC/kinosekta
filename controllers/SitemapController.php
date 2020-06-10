<?php

namespace app\controllers;

use app\models\Movie;
use app\models\People;
use Yii;
use yii\helpers\Inflector;
use yii\helpers\Url;

class SitemapController extends \yii\web\Controller
{
    public function actionSitemap()
    {
        $xml_sitemap = $this->renderPartial('sitemap_main');

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/xml');

        return $xml_sitemap;
    }

    public function actionSitemap1()
    {
        $xml_sitemap = $this->renderPartial('sitemap1');

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/xml');

        return $xml_sitemap;
    }

    public function actionSitemap2()
    {
        $cache = Yii::$app->cache;

        $urls = $cache->get('sitemap2');

        if ($urls === false) {
            $movies = Movie::find()->all();
            $date = date_create();

            foreach ($movies as $movie){
                date_timestamp_set($date, $movie->t_updated);
                $urls[] = array(
                    'loc' => Url::to(['filmy/view', 'id' => $movie['id'], 'title' => Inflector::slug($movie['title'])]),
                    'lastmod' => date_format($date, 'Y-m-d'),
                    'priority' => 0.6
                );
            }

            $cache->set('sitemap2', $urls,60*60*6);
        }


        $xml_sitemap = $this->renderPartial('index', array( // записываем view на переменную для последующего кэширования
            'host' => Yii::$app->request->hostInfo,              // текущий домен сайта
            'urls' => $urls,                                     // с генерированные ссылки для sitemap
        ));

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/xml');

        return $xml_sitemap;
    }

    //robots.txt
    public function actionSitemap3()
    {
        $urls = array();

        $cache = Yii::$app->cache;

        $urls = $cache->get('sitemap3');

        if ($urls === false) {
            $peoples = People::find()->all();

            foreach ($peoples as $people){
                $urls[] = array(
                    'loc' => Url::to(['aktery/view', 'id' => $people['id'], 'title' => Inflector::slug($people['name'])]),
                    'priority' => 0.6
                );
            }

            $cache->set('sitemap3', $urls,60*60*6);
        }


        $xml_sitemap = $this->renderPartial('index', array(
            'host' => Yii::$app->request->hostInfo,
            'urls' => $urls,
        ));

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/xml');

        return $xml_sitemap;
    }

    public function actionRobots()
    {
        $this->layout = false;
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'text/plain');

        return $this->render('robots');
    }
}

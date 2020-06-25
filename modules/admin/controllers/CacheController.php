<?php

namespace app\modules\admin\controllers;

use Yii;

class CacheController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @return mixed
     */
    public function actionMain()
    {
        Yii::$app->cache->delete('main');
        Yii::$app->session->setFlash('flash', "main: Главная страница обновлена");
        return $this->redirect(['index']);
    }

    public function actionSimilarMovies()
    {
        Yii::$app->cache->delete('similar_movies');
        Yii::$app->session->setFlash('flash', "similar_movies: Похожие фильмы обновлены");
        return $this->redirect(['index']);
    }

    public function actionSimilarTvs()
    {
        Yii::$app->cache->delete('similar_tvs');
        Yii::$app->session->setFlash('flash', "similar_tvs: Похожие сериалы обновлены");
        return $this->redirect(['index']);
    }

    public function actionNetworks()
    {
        Yii::$app->cache->delete('networks');
        Yii::$app->session->setFlash('flash', "networks: Телесети обновлены");
        return $this->redirect(['index']);
    }

    public function actionAll()
    {
        Yii::$app->cache->flush();
        Yii::$app->session->setFlash('flash', "all: Весь кэш обновлен");
        return $this->redirect(['index']);
    }
}

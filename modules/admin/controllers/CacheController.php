<?php

namespace app\modules\admin\controllers;

use Yii;

class CacheController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $movieCount = Yii::$app->db->createCommand('SELECT MAX(id) FROM movie')
            ->queryScalar();

        $movieCacheCount = 0;

        for ($i = $movieCount; $i >= 1; $i--) {
            if (Yii::$app->cache->exists('movie' . $i) == 1) {
                $movieCacheCount++;
            }
        }

        $tvCount = Yii::$app->db->createCommand('SELECT MAX(id) FROM tv')
            ->queryScalar();

        $tvCacheCount = 0;

        for ($i = $tvCount; $i >= 1; $i--) {
            if (Yii::$app->cache->exists('tv' . $i) == 1) {
                $tvCacheCount++;
            }
        }

        return $this->render('index', ['tv' => $tvCacheCount, 'movie' => $movieCacheCount]);
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

    public function actionMovies()
    {
        $count = Yii::$app->db->createCommand('SELECT MAX(id) FROM movie')
            ->queryScalar();

        $delCount =0;

        for ($i = $count; $i >= 1; $i--) {
            if (Yii::$app->cache->delete('movie' . $i) == 1) {
                $delCount++;
            }
        }

        Yii::$app->session->setFlash('flash', "movies: удалено " . $delCount . " фильма");
        return $this->redirect(['index']);
    }

    public function actionTvs()
    {
        $count = Yii::$app->db->createCommand('SELECT MAX(id) FROM tv')
            ->queryScalar();

        $delCount =0;

        for ($i = $count; $i >= 1; $i--) {
            if (Yii::$app->cache->delete('tv' . $i) == 1) {
                $delCount++;
            }
        }

        Yii::$app->session->setFlash('flash', "tvs: удалено " . $delCount . " сериала");
        return $this->redirect(['index']);
    }

    public function actionNetworks()
    {
        Yii::$app->cache->delete('networks');
        Yii::$app->session->setFlash('flash', "networks: Телесети обновлены");
        return $this->redirect(['index']);
    }

    public function actionSitemap($id)
    {
        Yii::$app->cache->delete('sitemap' . $id);
        Yii::$app->session->setFlash('flash', "Sitemap " . $id  . ": xml обновлен");
        return $this->redirect(['index']);
    }

    public function actionAll()
    {
        Yii::$app->cache->flush();
        Yii::$app->session->setFlash('flash', "all: Весь кэш обновлен");
        return $this->redirect(['index']);
    }
}

<?php

namespace app\controllers;

use Yii;
use yii\helpers\Inflector;
use yii\web\NotFoundHttpException;

class NetworkController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $cache = Yii::$app->cache;

        $networks = $cache->getOrSet('networks', function () {
            return Yii::$app->db->createCommand('SELECT n.*, count(*) as count FROM network as n join tv_network as tn on n.id=tn.network_id GROUP BY n.id order by count desc LIMIT 30;')
                ->queryAll();
        }, 60*60*24);

        return $this->render('index', ['networks' => $networks]);
    }

    public function actionView($id, $name=null)
    {
        $network = Yii::$app->db->createCommand('SELECT * FROM network WHERE id=:id')
            ->bindValue(':id', $id)
            ->queryOne();

        if (!$network) {
            throw new NotFoundHttpException('Network not found',404);
        }

        $network['transliterate'] = Inflector::slug($network['name']);

        if ($name != $network['transliterate']) {
            Yii::$app->getResponse()->redirect(['network/view', 'id' => $id, 'name' => $network['transliterate']], 301);
        }

        $tvs = Yii::$app->db->createCommand('SELECT tv.id, tv.title, tv.r_kp, tv.r_imdb, tv.first_air_date FROM tv join tv_network as tn ON tv.id=tn.tv_id WHERE network_id=:id ORDER BY tv.id DESC LIMIT 30;')
            ->bindValue(':id', $id)
            ->queryAll();

        $count = Yii::$app->db->createCommand('SELECT count(*) FROM tv join tv_network as tn ON tv.id=tn.tv_id WHERE network_id=:id')
            ->bindValue(':id', $id)
            ->queryColumn()[0];

        $lastPage = (int) ($count / 30);
        $lastPage = ($lastPage < ($count / 30) || $lastPage == 0) ? ($lastPage+1) : $lastPage;

        return $this->render('view', ['tvs' => $tvs, 'network' => $network,'lastPage' => $lastPage]);
    }

    public function actionPage()
    {
        $this->layout = false;
        $request = Yii::$app->request;

        if (!$request->isAjax) {
            return null;
        }

        $page = $request->post('page');
        $network = $request->post('s_network');
//        print_r($page);die();

        $tvs = Yii::$app->db->createCommand('SELECT tv.id, tv.title, tv.r_kp, tv.r_imdb, tv.first_air_date FROM tv join tv_network as tn ON tv.id=tn.tv_id WHERE network_id=:id ORDER BY tv.id LIMIT :limit,30')
            ->bindValue(':id', (int) $network)
            ->bindValue(':limit', (int) ($page-1)*30)
            ->queryAll();

//        return $this->asJson(array($page => $movies));
        return $this->render('/serialy/page', ['tvs' => $tvs]);
    }

}

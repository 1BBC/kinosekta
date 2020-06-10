<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Inflector;
use yii\httpclient\Client;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SerialyController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        print_r('index');
    }

    public function actionView($id, $title=null)
    {
        $movie = Yii::$app->db->createCommand('SELECT * FROM tv WHERE id=:id LIMIT 1')
            ->bindValue(':id', $id)
            ->queryOne();

        if (empty($movie)) {
            throw new NotFoundHttpException('ID=' . $id . ' T= ' . $title);
        }

        $transliterate = Inflector::slug($movie['title']);

        if ($title != $transliterate) {
            Yii::$app->getResponse()->redirect(['site/movie', 'id' => $id, 'title' => $transliterate], 301);
        }

        return $this->render('view', ['movie' => $movie]);
    }
}

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

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

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
        $cache = Yii::$app->cache;

        $cacheMain = $cache->getOrSet('main', function () {
            $arr['movies'] = Yii::$app->db->createCommand('SELECT  id, title, r_kp, r_imdb, release_date FROM (SELECT id, title, r_kp, r_imdb, release_date, popularity FROM movie ORDER BY id DESC LIMIT 100) AS s ORDER BY s.popularity DESC LIMIT 6')
                ->queryAll();

            $arr['tvs'] = Yii::$app->db->createCommand('SELECT  id, title, r_kp, r_imdb, first_air_date FROM (SELECT id, title, r_kp, r_imdb, first_air_date, popularity FROM tv ORDER BY id DESC LIMIT 100) AS s ORDER BY s.popularity DESC LIMIT 6')
                ->queryAll();

            $arr['peoples'] = Yii::$app->db->createCommand('SELECT id, name, orig_name FROM people ORDER BY popularity DESC LIMIT 6')
                ->queryAll();

            return $arr;
        },60*60*24);

        return $this->render('index', ['movies' => $cacheMain['movies'], 'tvs' => $cacheMain['tvs'], 'peoples' => $cacheMain['peoples']]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

}

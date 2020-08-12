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
    static $LIMIT_CONTENT = 50;
    static $LIMIT_PAGE_CONTENT = 6;
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

        $arr = $cache->getOrSet('main', function () {
            $arr['movies'] = Yii::$app->db->createCommand('SELECT  id, title, r_kp, r_imdb, release_date FROM (SELECT id, title, r_kp, r_imdb, release_date, popularity FROM movie ORDER BY id DESC LIMIT 100) AS s ORDER BY s.popularity DESC LIMIT ' . self::$LIMIT_CONTENT)
                ->queryAll();

            $arr['tvs'] = Yii::$app->db->createCommand('SELECT  id, title, r_kp, r_imdb, first_air_date FROM (SELECT id, title, r_kp, r_imdb, first_air_date, popularity FROM tv ORDER BY id DESC LIMIT 100) AS s ORDER BY s.popularity DESC LIMIT ' . self::$LIMIT_CONTENT)
                ->queryAll();

            $arr['cartoons'] = Yii::$app->db->createCommand('SELECT  id, title, r_kp, r_imdb, release_date FROM (SELECT id, title, r_kp, r_imdb, release_date, popularity FROM movie WHERE is_animation=1 ORDER BY id DESC LIMIT 100) AS s ORDER BY s.popularity DESC LIMIT ' . self::$LIMIT_CONTENT)
                ->queryAll();

            $arr['tv_cartoons'] = Yii::$app->db->createCommand('SELECT  id, title, r_kp, r_imdb, first_air_date FROM (SELECT id, title, r_kp, r_imdb, first_air_date, popularity FROM tv WHERE is_animation=1 ORDER BY id DESC LIMIT 100) AS s ORDER BY s.popularity DESC LIMIT ' . self::$LIMIT_CONTENT)
                ->queryAll();

            $arr['peoples'] = Yii::$app->db->createCommand('SELECT id, name, orig_name FROM people ORDER BY popularity DESC LIMIT ' . self::$LIMIT_CONTENT)
                ->queryAll();

            return $arr;
        },60*60*24);

        foreach ($arr as $type => $content) {
            $params[$type] = $this->sliceContent($content);
        }

        return $this->render('index', $params);
    }

    public function sliceContent($content)
    {
        shuffle($content);
        return array_slice($content, 0, self::$LIMIT_PAGE_CONTENT);
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

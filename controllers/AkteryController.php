<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class AkteryController extends Controller
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
        $peoples = Yii::$app->db->createCommand('SELECT id, name, orig_name FROM people ORDER BY popularity DESC LIMIT 30')
            ->queryAll();

        $count = Yii::$app->db->createCommand('SELECT count(*) FROM people')
            ->queryColumn()[0];

        $lastPage = (int) ($count / 30);
        $lastPage = ($lastPage < ($count / 30) || $lastPage == 0) ? ($lastPage+1) : $lastPage;

        return $this->render('index', ['peoples' => $peoples, 'lastPage' => $lastPage]);
    }

    public function actionPage()
    {
        $this->layout = false;
        $request = Yii::$app->request;

        if (!$request->isAjax) {
            return null;
        }
        $order = 'ORDER BY popularity DESC';
        $where = array();
        $join = '';

        $page = (int) $request->post('page');
        $s_type = (int) $request->post('s_type');
        $s_year = (int) $request->post('s_year');
        $s_gender = (int) $request->post('s_gender');

        if ($s_type) {
            switch ($s_type) {
                case 1:
                    $order = 'ORDER BY id DESC';
                    break;
                case 2:
                    $order = 'ORDER BY YEAR(birthday) DESC';
                    $where[] = 'birthday IS NOT NULL';
                    break;
                case 3:
                    $order = 'ORDER BY YEAR(birthday) ASC';
                    $where[] = 'birthday IS NOT NULL';
                    break;
            }
        }

        if ($s_gender == 1) {
            $where[] = 'gender=1';
        } elseif ($s_gender == 2) {
            $where[] = 'gender=2';
        }

        if ($s_year > 1899 && $s_year < 2021) {
            $where[] = 'YEAR(birthday)=' . $s_year;
        }

        if ($where !== array()) {
            $where = 'WHERE ' . implode(' AND ', $where);
        } else {
            $where = '';
        }

        if ($page && $page > 0) {
            $page = ($page-1)*30;
        } else {
            $page = 0;
        }

//        print_r('SELECT * FROM movie ' . $join  . ' ' . $where . ' ' . $order . ' LIMIT ' . $page . ',20');
        $peoples = Yii::$app->db->createCommand('SELECT * FROM people ' . $join  . ' ' . $where . ' ' . $order . ' LIMIT ' . $page . ',30')
            ->queryAll();

//        return $this->asJson(array($page => $movies));
        return $this->render('page', ['peoples' => $peoples]);
    }

    public function actionPageCount()
    {
        $this->layout = false;
        $request = Yii::$app->request;

        if (!$request->isAjax) {
            return null;
        }

        $where = array();

        $page = (int) $request->post('page');
        $s_type = (int) $request->post('s_type');
        $s_gender = (int) $request->post('s_gender');
        $s_year = (int) $request->post('s_year');

        if ($s_type == 2 || $s_type == 3) {
            $where[] = 'birthday IS NOT NULL';
        }

        if ($s_gender == 1) {
            $where[] = 'gender=1';
        } elseif ($s_gender == 2) {
            $where[] = 'gender=2';
        }

        if ($s_year > 1899 && $s_year < 2021) {
            $where[] = 'YEAR(birthday)=' . $s_year;
        }

        if ($where !== array()) {
            $where = 'WHERE ' . implode(' AND ', $where);
        } else {
            $where = '';
        }

        if ($page && $page > 0) {
            $page = ($page-1)*30;
        } else {
            $page = 0;
        }

//        print_r('SELECT * FROM movie ' . $join  . ' ' . $where . ' ' . $order . ' LIMIT ' . $page . ',20');
        $count = Yii::$app->db->createCommand('SELECT count(*) FROM people ' . $where . ' LIMIT ' . $page . ',30')
            ->queryColumn()[0];

        $lastPage = (int) ($count / 30);
        $lastPage = ($lastPage < ($count / 30) || $lastPage == 0) ? ($lastPage+1) : $lastPage;

//        return $this->asJson(array($page => $movies));
        return $this->asJson($lastPage);
    }

    public function actionView($id, $title=null)
    {
        $people = Yii::$app->db->createCommand('SELECT * FROM people WHERE id=:id LIMIT 1')
            ->bindValue(':id', $id)
            ->queryOne();

        if (!$people) {
            throw new NotFoundHttpException('People not found',404);
        }

        $people['transliterate'] = Inflector::slug($people['name']);

        if ($title != $people['transliterate']) {
            Yii::$app->getResponse()->redirect(['people/view', 'id' => $id, 'title' => $people['transliterate']], 301);
        }

        if (empty($people)) {
            throw new NotFoundHttpException('ID=' . $id . ' T= ' . $title);
        }

        $movies = Yii::$app->db->createCommand('SELECT m.id, m.title, mp.department, mp.role FROM movie m LEFT JOIN movie_people mp ON m.id=mp.movie_id LEFT JOIN people p ON p.id=mp.people_id WHERE p.id=:id ORDER BY id DESC')
            ->bindValue(':id', $id)
            ->queryAll();

        $tvs = Yii::$app->db->createCommand('SELECT m.id, m.title, mp.role FROM tv m LEFT JOIN tv_people mp ON m.id=mp.tv_id LEFT JOIN people p ON p.id=mp.people_id WHERE p.id=:id ORDER BY id DESC')
            ->bindValue(':id', $id)
            ->queryAll();

        $dl = self::getDepartmentList();

        $people['movies'] = $people['tvs'] = array();

        foreach ($movies as $movie) {
            $people['movies'][$movie['id']]['title'] = $movie['title'];
            if ($movie['department'] !=1) {
                $people['movies'][$movie['id']]['description'][] = $dl[$movie['department']];
            } else {
                $people['movies'][$movie['id']]['description'][] = $movie['role'];
            }

        }

        foreach ($tvs as $tv) {
            $people['tvs'][$tv['id']]['title'] = $tv['title'];
            if ($tv['role'] == 1) {
                $people['tvs'][$tv['id']]['description'][] = $dl[4];
            } else {
                $people['tvs'][$tv['id']]['description'][] = $tv['role'];
            }
        }

        $transliterate = Inflector::slug($people['name']);

        if ($title != $transliterate) {
            Yii::$app->getResponse()->redirect(['aktery/view', 'id' => $id, 'title' => $transliterate], 301);
        }

        return $this->render('view', ['people' => $people]);
    }

    public static function getDepartmentList()
    {
        return [
            1 => 'Актер',
            2 => 'Режжисер',
            3 => 'Продюсер',
            4 => 'Сценарий',
            5 => 'Оператор',
            6 => 'Композитор',
        ];
    }
}

<?php

namespace app\controllers;

use Yii;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
        $genreList = self::getGenresList();

        $tvs = Yii::$app->db->createCommand('SELECT id, title, r_kp, r_imdb, first_air_date FROM tv ORDER BY id DESC LIMIT 30')
            ->queryAll();

        $count = Yii::$app->db->createCommand('SELECT count(*) FROM tv')
            ->queryColumn()[0];

        $lastPage = (int) ($count / 30);
        $lastPage = ($lastPage < ($count / 30) || $lastPage == 0) ? ($lastPage+1) : $lastPage;

        return $this->render('index',
            [
                'tvs' => $tvs,
//                'countryList' =>  $countryList,
                'genreList' => $genreList,
                'lastPage' => $lastPage]);
    }

    public function actionPage()
    {
        $this->layout = false;
        $request = Yii::$app->request;

        if (!$request->isAjax) {
            return null;
        }
        $order = 'ORDER BY id DESC';
        $where = array();
        $join = '';

        $page = (int) $request->post('page');
        $s_type = (int) $request->post('s_type');
        $s_year = (int) $request->post('s_year');
        $s_genre = $request->post('s_genre');

        if ($s_type) {
            switch ($s_type) {
                case 1:
                    $order = 'ORDER BY first_air_date DESC';
                    break;
                case 2:
                    $order = 'ORDER BY r_kp DESC';
                    break;
            }
        }

        if ($s_year > 2014 && $s_year < 2021) {
            $where[] = 'YEAR(first_air_date)=' . $s_year;
        }

        if (is_array($s_genre)) {

            foreach ($s_genre as $genre) {
                if (array_key_exists($genre, self::getGenresList())) {
                    $where[] = $genre . '=1';
                }
            }
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
        $tvs = Yii::$app->db->createCommand('SELECT id, title, r_kp, r_imdb, first_air_date FROM tv ' . $join  . ' ' . $where . ' ' . $order . ' LIMIT ' . $page . ',30')
            ->queryAll();

//        return $this->asJson(array($page => $tvs));
        return $this->render('page', ['tvs' => $tvs]);
    }

    public function actionPageCount()
    {
        $this->layout = false;
        $request = Yii::$app->request;

        if (!$request->isAjax) {
            return null;
        }
        $order = 'ORDER BY id DESC';
        $where = array();
        $join = '';

        $page = (int) $request->post('page');
        $s_type = (int) $request->post('s_type');
        $s_year = (int) $request->post('s_year');
        $s_genre = $request->post('s_genre');

        if ($s_year > 2014 && $s_year < 2021) {
            $where[] = 'YEAR(first_air_date)=' . $s_year;
        }

        if (is_array($s_genre)) {

            foreach ($s_genre as $genre) {
                if (array_key_exists($genre, self::getGenresList())) {
                    $where[] = $genre . '=1';
                }
            }
        }


        if ($where !== array()) {
            $where = 'WHERE ' . implode(' AND ', $where);
        } else {
            $where = '';
        }

        $count = Yii::$app->db->createCommand('SELECT count(*) FROM tv ' . $join  . ' ' . $where)
            ->queryColumn()[0];

        $lastPage = (int) ($count / 30);
        $lastPage = ($lastPage < ($count / 30) || $lastPage == 0) ? ($lastPage+1) : $lastPage;

        return $this->asJson($lastPage);
    }


    public function actionView($id, $title=null)
    {
        $cache = Yii::$app->cache;

        $tv = $cache->get('tv' . $id);

        if ($tv === false) {
            $tv = $this->calculateTv($id);

            if (!$tv) {
                throw new NotFoundHttpException('Tv not found',404);
            }

            $cache->set('tv' . $id, $tv,60*60);
        }

        $tv['transliterate'] = Inflector::slug($tv['title']);

        if ($title != $tv['transliterate']) {
            Yii::$app->getResponse()->redirect(['serialy/view', 'id' => $id, 'title' => $tv['transliterate']], 301);
        }

        $similar_tvs = $cache->getOrSet('similar_tvs', function () {
            return Yii::$app->db->createCommand('SELECT id, title FROM (SELECT id, title, popularity FROM tv WHERE images>1 ORDER BY id DESC LIMIT 100) AS s ORDER BY s.popularity DESC LIMIT 20;')
                ->queryAll();
        },60*60*24);

        return $this->render('view', ['tv' => $tv, 'similar_tvs' => $similar_tvs]);
    }

    public function calculateTv($id)
    {
        $tv = Yii::$app->db->createCommand('SELECT * FROM tv WHERE id=:id LIMIT 1')
            ->bindValue(':id', $id)
            ->queryOne();

        if (!$tv) return null;

        $peoples = Yii::$app->db->createCommand('SELECT p.id, mp.role, p.name, p.orig_name FROM tv_people as mp LEFT JOIN people as p ON mp.people_id=p.id where mp.tv_id=:tv_id')
            ->bindValue(':tv_id', $id)
            ->queryAll();

        $tv['actors'] = $tv['story'] = array();

        foreach ($peoples as $people) {
            $people['url_name'] = Inflector::slug($people['name']);
            if ($people['role'] == 1) {
                array_push($tv['story'], $people);
            } else {
                array_push($tv['actors'], $people);
            }
        }

        $tv['netwoks'] = Yii::$app->db->createCommand('SELECT n.id, n.name FROM network as n join tv_network as tn on n.id=tn.network_id where tn.tv_id=:tv_id')
            ->bindValue(':tv_id', $id)
            ->queryAll();

        foreach ($tv['netwoks'] as &$netwok) {
            $netwok['url_name'] = Inflector::slug($netwok['name']);
        }

        $genresList = self::getGenresList();
        $tv['genres'] = array();

        foreach ($genresList as $genre => $value) {
            if ($tv[$genre]==1) {
                array_push($tv['genres'], $value);
            }
        }

        return $tv;
    }

    public function actionYear($year)
    {
        if ($year > 2020 || $year < 1920) {
            throw new NotFoundHttpException('Tv not found',404);
        }

        $tvs = Yii::$app->db->createCommand('SELECT * FROM tv WHERE YEAR(first_air_date)=:year ORDER BY id DESC LIMIT 30')
            ->bindValue(':year', $year)
            ->queryAll();

        $count = Yii::$app->db->createCommand('SELECT count(*) FROM tv WHERE YEAR(first_air_date)=:year')
            ->bindValue(':year', $year)
            ->queryColumn()[0];

        $lastPage = (int) ($count / 30);
        $lastPage = ($lastPage < ($count / 30) || $lastPage == 0) ? ($lastPage+1) : $lastPage;

        return $this->render('year', ['tvs' => $tvs, 'year' => $year, 'lastPage' => $lastPage]);
    }

    public function actionPageYear()
    {
        $this->layout = false;
        $request = Yii::$app->request;

        if (!$request->isAjax) {
            return null;
        }

        $page = $request->post('page');
        $year = $request->post('s_year');

        $tvs = Yii::$app->db->createCommand('SELECT id, title, r_kp, r_imdb, first_air_date FROM tv WHERE YEAR(first_air_date)=:year ORDER BY id DESC LIMIT :limit,30')
            ->bindValue(':year', (int) $year)
            ->bindValue(':limit', (int) ($page-1)*30)
            ->queryAll();

//        return $this->asJson(array($page => $movies));
        return $this->render('page-year', ['tvs' => $tvs]);
    }

    public function actionGenre($genre)
    {
        if (!in_array($genre, ['ekshn','multfilmi','komedii','kriminal','kriminal','dokumentalnye','dramy',
            'semejnye','detskie','misticheskie','na_realnykh_sobytiyakh','nauchnoe_fentezi',
            'melodrama ','melodramy','voennyj', 'vesterny', 'razgovornyj'])) {
            throw new NotFoundHttpException('Movie not found',404);
        }

        $genre = Yii::$app->db->createCommand('SELECT * FROM genre WHERE name=:name')
            ->bindValue(':name', $genre)
            ->queryOne();

        if (!$genre) {
            throw new NotFoundHttpException('Tv not found',404);
        }

        $db_name = $genre['db_name'] ?? 'is_action';

        $tvs = Yii::$app->db->createCommand('SELECT id, title, r_kp, r_imdb, first_air_date FROM tv WHERE ' . $db_name . '=1 ORDER BY id DESC LIMIT 30')
            ->queryAll();

        $count = Yii::$app->db->createCommand('SELECT count(*) FROM tv WHERE ' . $db_name . '=1')
            ->queryColumn()[0];

        $lastPage = (int) ($count / 30);
        $lastPage = ($lastPage < ($count / 30) || $lastPage == 0) ? ($lastPage+1) : $lastPage;

//        print_r($movies);die();

        return $this->render('genre', ['tvs' => $tvs, 'genre' => $genre, 'lastPage' => $lastPage]);
    }

    public function actionPageGenre()
    {
        $this->layout = false;
        $request = Yii::$app->request;

        if (!$request->isAjax) {
            return null;
        }

        $page = (int) $request->post('page');
        $s_genre = $request->post('s_genre');

        if (!in_array($s_genre, ['ekshn','multfilmi','komedii','kriminal','kriminal','dokumentalnye','dramy',
            'semejnye','detskie','misticheskie','na_realnykh_sobytiyakh','nauchnoe_fentezi',
            'melodrama ','melodramy','voennyj', 'vesterny', 'razgovornyj'])) {
            throw new NotFoundHttpException('Tv not found',404);
        }

        $genre = Yii::$app->db->createCommand('SELECT db_name FROM genre WHERE name=:name')
            ->bindValue(':name', $s_genre)
            ->queryOne();

        if (!$genre) {
            throw new NotFoundHttpException('Tv not found',404);
        }

        if ($page && $page > 0) {
            $page = ($page-1)*30;
        } else {
            $page = 0;
        }

        $tv = Yii::$app->db->createCommand('SELECT id, title, r_kp, r_imdb, first_air_date FROM tv WHERE ' . $genre['db_name'] . '=1' . ' ORDER BY id DESC LIMIT ' . $page . ',30')
            ->queryAll();


        return $this->render('page', ['tvs' => $tvs]);
    }

    public static function getGenresList()
    {
        return array (
            'is_action_adventure' => [
                'name' => 'Экшн',
                'url' => 'ekshn',
            ],
            'is_animation' => [
                'name' => 'Мультфильм',
                'url' => 'multfilmi',
            ],
            'is_comedy' => [
                'name' => 'Комедия',
                'url' => 'komedii',
            ],
            'is_crime' => [
                'name' => 'Криминал',
                'url' => 'kriminal',
            ],
            'is_documentary' => [
                'name' => 'Документальный',
                'url' => 'dokumentalnye',
            ],
            'is_drama' => [
                'name' => 'Драма',
                'url' => 'dramy',
            ],
            'is_family' => [
                'name' => 'Семейный',
                'url' => 'semejnye',
            ],
            'is_kids' => [
                'name' => 'Детские',
                'url' => 'detskie',
            ],
            'is_mystery' => [
                'name' => 'Мистика',
                'url' => 'misticheskie',
            ],
            'is_reality' => [
                'name' => 'На реальных событиях',
                'url' => 'na_realnykh_sobytiyakh  ',
            ],
            'is_science_fiction_fantasy' => [
                'name' => 'Научное фэнтези',
                'url' => 'nauchnoe_fentezi',
            ],
            'is_soap' => [
                'name' => 'Мелодрама',
                'url' => 'melodrama',
            ],
            'is_talk' => [
                'name' => 'Разговорный',
                'url' => 'razgovornyj',
            ],
            'is_war_politics' => [
                'name' => 'Военный',
                'url' => 'voennyj',
            ],
            'is_western' => [
                'name' => 'Вестерны',
                'url' => 'vesterny',
            ],
        );
    }
}

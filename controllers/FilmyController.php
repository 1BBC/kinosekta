<?php

namespace app\controllers;

use Yii;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class FilmyController extends Controller
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
        $countryList = self::getCountryList();
        $genreList = self::getGenresList();

        $movies = Yii::$app->db->createCommand('SELECT id, title, r_kp, r_imdb, release_date FROM movie ORDER BY id DESC LIMIT 30')
            ->queryAll();

        $count = Yii::$app->db->createCommand('SELECT count(*) FROM movie')
            ->queryColumn()[0];

        $lastPage = (int) ($count / 30);
        $lastPage = ($lastPage < ($count / 30) || $lastPage == 0) ? ($lastPage+1) : $lastPage;


        return $this->render('index', ['movies' => $movies, 'countryList' =>  $countryList, 'genreList' => $genreList, 'lastPage' => $lastPage]);
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
        $s_country = $request->post('s_country');

        if ($s_type) {
            switch ($s_type) {
                case 1:
                    $order = 'ORDER BY release_date DESC';
                    break;
                case 2:
                    $order = 'ORDER BY r_kp DESC';
                    break;
            }
        }

        if ($s_year > 2014 && $s_year < 2021) {
            $where[] = 'YEAR(release_date)=' . $s_year;
        }

        if (is_array($s_genre)) {

            foreach ($s_genre as $genre) {
                if (array_key_exists($genre, self::getGenresList())) {
                    $where[] = $genre . '=1';
                }
            }
        }

        if (ctype_alpha($s_country) && iconv_strlen($s_country)==2) {
            $join = 'left join movie_country mc on id=mc.movie_id';
            $where[] = "mc.country='" . $s_country . "'";
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
        $movies = Yii::$app->db->createCommand('SELECT id, title, r_kp, r_imdb, release_date FROM movie ' . $join  . ' ' . $where . ' ' . $order . ' LIMIT ' . $page . ',30')
            ->queryAll();

//        return $this->asJson(array($page => $movies));
        return $this->render('page', ['movies' => $movies]);
    }

    public function actionPageCount()
    {
        $this->layout = false;
        $request = Yii::$app->request;

        if (!$request->isAjax) {
            return null;
        }
        $where = array();
        $join = '';

        $s_year = $request->post('s_year');
        $s_genre = $request->post('s_genre');
        $s_country = $request->post('s_country');

        if ($s_year > 2014 && $s_year < 2021) {
            $where[] = 'YEAR(release_date)=' . $s_year;
        }

        if (is_array($s_genre)) {

            foreach ($s_genre as $genre) {
                if (array_key_exists($genre, self::getGenresList())) {
                    $where[] = $genre . '=1';
                }
            }
        }

        if (is_string($s_country) && iconv_strlen($s_country)==2) {
            $join = 'left join movie_country mc on id=mc.movie_id';
            $where[] = "mc.country='" . $s_country . "'";
        }


        if ($where !== array()) {
            $where = 'WHERE ' . implode(' AND ', $where);
        } else {
            $where = '';
        }

        $count = Yii::$app->db->createCommand('SELECT count(*) FROM movie ' . $join  . ' ' . $where)
            ->queryColumn()[0];

        $lastPage = (int) ($count / 30);
        $lastPage = ($lastPage < ($count / 30) || $lastPage == 0) ? ($lastPage+1) : $lastPage;

        return $this->asJson($lastPage);
    }

    public function actionCountry($iso, $title=null)
    {
        $countries = self::getCountryList();

        if (!array_key_exists(mb_strtoupper($iso), $countries)) {
            throw new NotFoundHttpException('Movies not found',404);
        }

        $country = $countries[mb_strtoupper($iso)];

        if (!is_array($country)) {
            throw new NotFoundHttpException('Movies 2not found',404);
        }

        if ($title != $country['url'] || $iso != mb_strtolower($iso)) {
            $this->redirect('/filmy/' . mb_strtolower($iso) . '-' . $country['url'] . '/', 301);
        }

        $movies = Yii::$app->db->createCommand('SELECT id, title, r_kp, r_imdb, release_date FROM movie LEFT JOIN movie_country mp ON id=mp.movie_id WHERE mp.country=:country ORDER BY id DESC LIMIT 30')
            ->bindValue(':country', $iso)
            ->queryAll();

        $count = Yii::$app->db->createCommand('SELECT count(*) FROM movie LEFT JOIN movie_country mp ON id=mp.movie_id WHERE mp.country=:country')
            ->bindValue(':country', $iso)
            ->queryColumn()[0];

        $lastPage = (int) ($count / 30);
        $lastPage = ($lastPage < ($count / 30) || $lastPage == 0) ? ($lastPage+1) : $lastPage;

        return $this->render('country', ['movies' => $movies, 'iso' => $iso, 'country' => $country, 'lastPage' => $lastPage]);
    }

    public function actionPageCountry()
    {
        $this->layout = false;
        $request = Yii::$app->request;

        if (!$request->isAjax) {
            return null;
        }

        $page = (int) $request->post('page');
        $s_country = $request->post('s_country');

        if (!$s_country) {
            throw new NotFoundHttpException('Movie not found',404);
        }

        if ($page && $page > 0) {
            $page = ($page-1)*30;
        } else {
            $page = 0;
        }

        $movies = Yii::$app->db->createCommand('SELECT id, title, r_kp, r_imdb, release_date FROM movie LEFT JOIN movie_country mp ON id=mp.movie_id WHERE mp.country=:country ORDER BY id DESC LIMIT ' . $page . ',30')
            ->bindValue(':country', $s_country)
            ->queryAll();


        return $this->render('page', ['movies' => $movies]);
    }

    public function actionGenre($genre)
    {
        if (!in_array($genre, ['boeviki','priklyucheniya','multfilmi','komedii','kriminal','dokumentalnye','dramy',
            'semejnye','fentezi','istoricheskie','uzhasy','muzykalnye','misticheskie','melodramy', 'multfilmi', 'fantastika',
            'peredachi','trillery','voennye','vesterny'])) {
            throw new NotFoundHttpException('Movie not found',404);
        }

        $genre = Yii::$app->db->createCommand('SELECT * FROM genre WHERE name=:name')
            ->bindValue(':name', $genre)
            ->queryOne();

        if (!$genre) {
            throw new NotFoundHttpException('Movie not found',404);
        }

        $db_name = $genre['db_name'] ?? 'is_action';

        $movies = Yii::$app->db->createCommand('SELECT id, title, r_kp, r_imdb, release_date FROM movie WHERE ' . $db_name . '=1 ORDER BY id DESC LIMIT 30')
            ->queryAll();

        $count = Yii::$app->db->createCommand('SELECT count(*) FROM movie WHERE ' . $db_name . '=1')
            ->queryColumn()[0];

        $lastPage = (int) ($count / 30);
        $lastPage = ($lastPage < ($count / 30) || $lastPage == 0) ? ($lastPage+1) : $lastPage;

//        print_r($movies);die();

        return $this->render('genre', ['movies' => $movies, 'genre' => $genre, 'lastPage' => $lastPage]);
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

        if (!in_array($s_genre, ['boeviki','priklyucheniya','multfilmi','komedii','kriminal','dokumentalnye','dramy',
            'semejnye','fentezi','istoricheskie','uzhasy','muzykalnye','misticheskie ','melodramy', 'multfilmi', 'fantastika',
            'peredachi','trillery','voennye','vesterny'])) {
            throw new NotFoundHttpException('Movie not found',404);
        }

        $genre = Yii::$app->db->createCommand('SELECT db_name FROM genre WHERE name=:name')
            ->bindValue(':name', $s_genre)
            ->queryOne();

        if (!$genre) {
            throw new NotFoundHttpException('Movie not found',404);
        }

        if ($page && $page > 0) {
            $page = ($page-1)*30;
        } else {
            $page = 0;
        }

        $movies = Yii::$app->db->createCommand('SELECT id, title, r_kp, r_imdb, release_date FROM movie WHERE ' . $genre['db_name'] . '=1' . ' ORDER BY id DESC LIMIT ' . $page . ',30')
            ->queryAll();


        return $this->render('page', ['movies' => $movies]);
    }

    public function actionYear($year)
    {
        if ($year > 2020 || $year < 1920) {
            throw new NotFoundHttpException('Movie not found',404);
        }

        $movies = Yii::$app->db->createCommand('SELECT * FROM movie WHERE YEAR(release_date)=:year ORDER BY id DESC LIMIT 30')
            ->bindValue(':year', $year)
            ->queryAll();

        $count = Yii::$app->db->createCommand('SELECT count(*) FROM movie WHERE YEAR(release_date)=:year')
            ->bindValue(':year', $year)
            ->queryColumn()[0];

        $lastPage = (int) ($count / 30);
        $lastPage = ($lastPage < ($count / 30) || $lastPage == 0) ? ($lastPage+1) : $lastPage;

        return $this->render('year', ['movies' => $movies, 'year' => $year, 'lastPage' => $lastPage]);
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

        $movies = Yii::$app->db->createCommand('SELECT id, title, r_kp, r_imdb, release_date FROM movie WHERE YEAR(release_date)=:year ORDER BY id DESC LIMIT :limit,30')
            ->bindValue(':year', (int) $year)
            ->bindValue(':limit', (int) ($page-1)*30)
            ->queryAll();

//        return $this->asJson(array($page => $movies));
        return $this->render('page-year', ['movies' => $movies]);
    }

    public function actionView($id, $title=null)
    {
        $cache = Yii::$app->cache;

        $movie = $cache->get('movie' . $id);
//        $movie = false;
        if ($movie === false) {

            $movie = $this->calculateMovie($id);

            if (!$movie) {
                throw new NotFoundHttpException('Movie not found',404);
            }

            $cache->set('movie' . $id, $movie);
        }

        $movie['transliterate'] = Inflector::slug($movie['title']);

        if ($title != $movie['transliterate']) {
            Yii::$app->getResponse()->redirect(['filmy/view', 'id' => $id, 'title' => $movie['transliterate']], 301);
        }

        $similar_movies = $cache->getOrSet('similar_movies', function () {
            return Yii::$app->db->createCommand('SELECT id, title FROM (SELECT id, title, popularity FROM movie WHERE images>1 ORDER BY id DESC LIMIT 100) AS s ORDER BY s.popularity DESC LIMIT 20;')
                ->queryAll();
        },60*60*24);

        return $this->render('view', ['movie' => $movie, 'similar_movies' => $similar_movies]);
    }

    public function calculateMovie($id)
    {
        $movie = Yii::$app->db->createCommand('SELECT * FROM movie WHERE id=:id LIMIT 1')
            ->bindValue(':id', $id)
            ->queryOne();

        if (!$movie) return null;

        $movie['country'] = Yii::$app->db->createCommand('SELECT country FROM movie_country WHERE movie_id=:id')
            ->bindValue(':id', $id)
            ->queryColumn();

        $peoples = Yii::$app->db->createCommand('SELECT p.id, mp.department, mp.role, p.name, p.orig_name FROM movie_people as mp LEFT JOIN people as p ON mp.people_id=p.id where mp.movie_id=:movie_id')
            ->bindValue(':movie_id', $id)
            ->queryAll();


        $movie['actors'] = $movie['director'] = $movie['producer'] = $movie['story'] = $movie['camera'] = $movie['sound'] = array();

        foreach ($peoples as $people) {
            $people['url_name'] = Inflector::slug($people['name']);
            if ($people['department'] ==1) {
                array_push($movie['actors'], $people);
            } elseif ($people['department'] == 2) {
                array_push($movie['director'], $people);
            } elseif ($people['department'] == 3) {
                array_push($movie['producer'], $people);
            } elseif ($people['department'] == 4) {
                array_push($movie['story'], $people);
            } elseif ($people['department'] == 5) {
                array_push($movie['camera'], $people);
            } elseif ($people['department'] == 6) {
                array_push($movie['sound'], $people);
            }
        }

        $genresList = self::getGenresList();
        $movie['genres'] = array();

        foreach ($genresList as $genre => $value) {
            if ($movie[$genre]==1) {
                array_push($movie['genres'], $value);
            }
        }

        $countrysList = self::getCountryList();
        $movie['countries'] = array();

        foreach ($movie['country'] as $country) {
            $arr = array();
            $arr['iso'] = mb_strtolower($country);

            if (empty($countrysList[$country])) continue;

            if (is_array($countrysList[$country])) {
                $arr['name'] = $countrysList[$country]['name'];
                $arr['url'] = $countrysList[$country]['url'];
            }  else {
                $arr['name'] = $countrysList[$country];
            }

            array_push($movie['countries'], $arr);
        }

        return $movie;
    }

    public static function getGenresList()
    {
        return array (
            'is_action' => [
                'name' => 'Боевик',
                'url' => 'boeviki',
            ],
            'is_adventure' => [
                'name' => 'Приключения',
                'url' => 'priklyucheniya',
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
            'is_fantasy' => [
                'name' => 'Фэнтези',
                'url' => 'fentezi',
            ],
            'is_history' => [
                'name' => 'Исторический',
                'url' => 'istoricheskie',
            ],
            'is_horror' => [
                'name' => 'Ужасы',
                'url' => 'uzhasy',
            ],
            'is_music' => [
                'name' => 'Музыка',
                'url' => 'muzykalnye',
            ],
            'is_mystery' => [
                'name' => 'Мистика',
                'url' => 'misticheskie ',
            ],
            'is_romance' => [
                'name' => 'Мелодрама',
                'url' => 'melodramy',
            ],
            'is_science_fiction' => [
                'name' => 'Фантастика',
                'url' => 'fantastika',
            ],
            'is_tv_movie' => [
                'name' => 'Передачи',
                'url' => 'peredachi',
            ],
            'is_thriller' => [
                'name' => 'Триллер',
                'url' => 'trillery',
            ],
            'is_war' => [
                'name' => 'Военные',
                'url' => 'voennye',
            ],
            'is_western' => [
                'name' => 'Вестерны',
                'url' => 'vesterny',
            ],
        );
    }

    public static function getCountryList()
    {
        return array (
            'AC' => 'остров Вознесения',
            'AD' => 'Андорра',
            'AE' => 'Объединенные Арабские Эмираты',
            'AF' => 'Афганистан',
            'AG' => 'Антигуа и Барбуда',
            'AI' => 'Ангилья',
            'AL' => 'Албания',
            'AM' => 'Армения',
            'AN' => 'Голланские Антильские острова',
            'AO' => 'Ангола',
            'AQ' => 'Антарктика',
            'AR' => 'Аргентина',
            'AS' => 'Американское Самоа',
            'AT' => 'Австрия',
            'AU' => 'Австралия',
            'AW' => 'Аруба',
            'AX' => 'Аландские острова',
            'AZ' => 'Азербайджан',
            'BA' => 'Босния и Герцеговина',
            'BB' => 'Барбадос',
            'BD' => 'Бангладеш',
            'BE' => 'Бельгия',
            'BF' => 'Буркина-Фасо',
            'BG' => 'Болгария',
            'BH' => 'Бахрейн',
            'BI' => 'Бурунди',
            'BJ' => 'Бенин',
            'BM' => 'Бермудские острова',
            'BN' => 'Бруней',
            'BO' => 'Боливия',
            'BR' => 'Бразилия',
            'BS' => 'Багамские острова',
            'BT' => 'Бутан',
            'BV' => 'остров Буве',
            'BW' => 'Ботсвана',
            'BY' => 'Беларусь',
            'BZ' => 'Белиз',
            'CA' => 'Канада',
            'CC' => 'Кокосовые острова',
            'CD' => 'Конго',
            'CF' => 'Центральноафриканская Республика',
            'CG' => 'Конго',
            'CH' => 'Швейцария',
            'CI' => 'Кот-дИвуар',
            'CK' => 'острова Кука',
            'CL' => 'Чили',
            'CM' => 'Камерун',
            'CN' => [
                'name' => 'Китай',
                'url'  => 'kitajskie',
            ],
            'CO' => 'Колумбия',
            'CR' => 'Коста-Рика',
            'CS' => 'Сербия и Черногория',
            'CU' => 'Куба',
            'CV' => 'Кабо-Верде',
            'CX' => 'остров Рождества',
            'CY' => 'Кипр',
            'CZ' => [
                'name' => 'Чехия',
                'url'  => 'сheshskie',
            ],
            'DE' => [
                'name' => 'Германия',
                'url'  => 'nemeczkie',
            ],
            'DJ' => 'Джибути',
            'DK' => 'Дания',
            'DM' => 'Доминика',
            'DO' => 'Доминиканская Республика',
            'DZ' => 'Алжир',
            'EC' => 'Эквадор',
            'EE' => 'Эстония',
            'EG' => 'Египет',
            'EH' => 'Западная Сахара',
            'ER' => 'Эритрея',
            'ES' => [
                'name' => 'Испания',
                'url'  => 'ispanskie',
            ],
            'ET' => 'Эфиопия',
            'FI' => 'Финляндия',
            'FJ' => 'Фиджи',
            'FK' => 'Фолклендские острова',
            'FM' => 'Микронезия',
            'FO' => 'Фарерские острова',
            'FR' => [
                'name' => 'Франция',
                'url'  => 'franczuzskie',
            ],
            'GA' => 'Габон',
            'GB' => [
                'name' => 'Великобритания',
                'url'  => 'britanskie',
            ],
            'GD' => 'Гренада',
            'GE' => 'Грузия',
            'GF' => 'Французская Гвиана',
            'GG' => 'остров Гернси',
            'GH' => 'Гана',
            'GI' => 'Гибралтар',
            'GL' => 'Гренландия',
            'GM' => 'Гамбия',
            'GN' => 'Гвинея',
            'GP' => 'Гваделупа',
            'GQ' => 'Экваториальная Гвинея',
            'GR' => 'Греция',
            'GS' => 'Южная Джорджия и Южные Сандвичевы острова',
            'GT' => 'Гватемала',
            'GU' => 'Гуам',
            'GW' => 'Гвинея-Бисау',
            'GY' => 'Гайана',
            'HK' => 'Гонконг',
            'HM' => 'Остров Херд и острова Макдональд',
            'HN' => 'Гондурас',
            'HR' => 'Хорватия',
            'HT' => 'Гаити',
            'HU' => 'Венгрия',
            'ID' => 'Индонезия',
            'IE' => 'Ирландия',
            'IL' => 'Израиль',
            'IM' => 'остров Мэн',
            'IN' => [
                'name' => 'Индия',
                'url'  => 'indijskie',
            ],
            'IO' => 'Британская территория в Индийском океане',
            'IQ' => 'Ирак',
            'IR' => 'Иран',
            'IS' => 'Исландия',
            'IT' => 'Италия',
            'JE' => 'остров Джерси',
            'JM' => 'Ямайка',
            'JO' => 'Иордания',
            'JP' => [
                'name' => 'Япония',
                'url'  => 'yaponskie',
            ],
            'KE' => 'Кения',
            'KG' => 'Кыргызстан',
            'KH' => 'Камбоджа',
            'KI' => 'Кирибати',
            'KM' => 'Коморские острова',
            'KN' => 'Сент-Китс и Невис',
            'KP' => 'Северная Корея',
            'KR' => [
                'name' => 'Южная Корея',
                'url'  => 'korejskie',
            ],
            'KW' => 'Кувейт',
            'KY' => 'Каймановы острова',
            'KZ' => 'Казахстан',
            'LA' => 'Лаос',
            'LB' => 'Ливан',
            'LC' => 'Сент-Люсия',
            'LI' => 'Лихтенштейн',
            'LK' => 'Шри-Ланка',
            'LR' => 'Либерия',
            'LS' => 'Лесото',
            'LT' => 'Литва',
            'LU' => 'Люксембург',
            'LV' => 'Латвия',
            'LY' => 'Ливия',
            'MA' => 'Марокко',
            'MC' => 'Монако',
            'ME' => 'Монтенегро',
            'MD' => 'Молдова',
            'MG' => 'Мадагаскар',
            'MH' => 'Маршалловы острова',
            'MK' => 'Македония',
            'ML' => 'Мали',
            'MM' => 'Мьянма',
            'MN' => 'Монголия',
            'MO' => 'Макао',
            'MP' => 'Mariana  Северные Марианские острова',
            'MQ' => 'Мартиника',
            'MR' => 'Мавритания',
            'MS' => 'Монтсеррат',
            'MT' => 'Мальта',
            'MU' => 'Маврикий',
            'MV' => 'Мальдивы',
            'MW' => 'Малави',
            'MX' => 'Мексика',
            'MY' => 'Малайзия',
            'MZ' => 'Мозамбик',
            'NA' => 'Намибия',
            'NC' => 'Новая Каледония',
            'NE' => 'Нигер',
            'NF' => 'Норфолк',
            'NG' => 'Нигерия',
            'NI' => 'Никарагуа',
            'NL' => 'Нидерланды',
            'NO' => 'Норвегия',
            'NP' => 'Непал',
            'NR' => 'Науру',
            'NU' => 'Ниуэ',
            'NZ' => 'Новая Зеландия',
            'OM' => 'Оман',
            'PA' => 'Панама',
            'PE' => 'Перу',
            'PF' => 'Французская Полинезия',
            'PG' => 'Папуа - Новая Гвинея',
            'PH' => 'Филиппины',
            'PK' => 'Пакистан',
            'PL' => 'Польша',
            'PM' => 'Сен-Пьер и Микелон',
            'PN' => 'остров Питкэрн',
            'PR' => 'Пуэрто-Рико',
            'PS' => 'Палестина',
            'PT' => 'Португалия',
            'PW' => 'Палау',
            'PY' => 'Парагвай',
            'QA' => 'Катар',
            'RE' => 'остров Реюньон',
            'RO' => 'Румыния',
            'RU' => [
                'name' => 'Россия',
                'url'  => 'rossijskie',
            ],
            'RW' => 'Руанда',
            'SA' => 'Саудовская Аравия',
            'SB' => 'Соломоновы Острова',
            'SC' => 'Сейшельские Острова',
            'SD' => 'Судан',
            'SE' => 'Швеция',
            'SG' => 'Сингапур',
            'SH' => 'остров Святой Елены',
            'SI' => 'Словения',
            'SJ' => 'Шпицберген и Ян-Майен',
            'SK' => 'Словакия',
            'SL' => 'Сьерра-Леоне',
            'SM' => 'Сан-Марино',
            'SN' => 'Сенегал',
            'SO' => 'Сомали',
            'SR' => 'Суринам',
            'ST' => 'Сан-Томе и Принсипи',
            'SU' => [
                'name' => 'СССР',
                'url'  => 'sovetskie',
            ],
            'SV' => 'Сальвадор',
            'SY' => 'Сирия',
            'SZ' => 'Свазиленд',
            'TC' => 'Острова Тёркс и Кайкос',
            'TD' => 'Чад',
            'TF' => 'Французские Южные и Антарктические Территории',
            'TG' => 'Того',
            'TH' => 'Таиланд',
            'TJ' => 'Таджикистан',
            'TK' => 'Токелау',
            'TL' => 'Тимор-Лешти',
            'TM' => 'Туркменистан',
            'TN' => 'Тунис',
            'TO' => 'Тонга',
            'TP' => 'Восточный Тимор',
            'TR' => 'Турция',
            'TT' => 'Тринидад и Тобаго',
            'TV' => 'Тувалу',
            'TW' => 'Тайвань',
            'TZ' => 'Танзания',
            'UA' => [
                'name' => 'Украина',
                'url'  => 'ukrainskie',
            ],
            'UG' => 'Уганда',
            'UK' => [
                'name' => 'Великобритания',
                'url'  => 'britanskie',
            ],
            'UM' => '',
            'US' => [
                'name' => 'США',
                'url'  => 'amerikanskie',
            ],
            'UY' => 'Уругвай',
            'UZ' => 'Узбекистан',
            'VA' => 'Ватикан',
            'VC' => 'Сент-Винсент и Гренадины',
            'VE' => 'Венесуэла',
            'VG' => 'Виргинские острова, Британские',
            'VI' => 'Виргинские острова, США',
            'VN' => 'Вьетнам',
            'VU' => 'Вануату',
            'WF' => 'Острова Уоллис и Футуна',
            'WS' => 'Западное Самоа',
            'YE' => 'Йемен',
            'YT' => 'Майотта',
            'YU' => 'Югославия',
            'ZA' => 'ЮАР',
            'ZM' => 'Замбия',
            'ZW' => 'Зимбабве'
        );
    }
}

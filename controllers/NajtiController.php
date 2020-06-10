<?php

namespace app\controllers;

use app\models\Movie;
use app\models\People;
use yii\web\Controller;

class NajtiController extends Controller
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
    public function actionFilmy($q=null)
    {
        $query = Movie::find();

        if( preg_match("/[А-Яа-я]/", $q) ) {
            $query->filterWhere(['like', 'title', $q]);
        } else {
            $query->filterWhere(['like', 'orig_title', $q]);
        }

        if ($q) {
            $movies = $query->limit(60)->asArray()->all();
        } else {
            $movies = [];
        }

        return $this->render('filmy', ['movies' => $movies, 'q' => $q]);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionAktery($q=null)
    {
        $query = People::find();

        if( preg_match("/[А-Яа-я]/", $q) ) {
            $query->filterWhere(['like', 'orig_name', $q]);
        } else {
            $query->filterWhere(['like', 'name', $q]);
        }

        if ($q) {
            $movies = $query->limit(60)->asArray()->all();
        } else {
            $movies = [];
        }

        $peoples = $query->limit(60)->asArray()->all();

        return $this->render('aktery', ['peoples' => $peoples, 'q' => $q]);
    }
}

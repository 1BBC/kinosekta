<?php

namespace app\controllers;

use app\models\Movie;
use app\models\People;
use app\models\Tv;
use Yii;
use yii\db\ActiveQuery;
use yii\web\Controller;

class NajtiController extends Controller
{
    public $q;

    static $LIMIT_CONTENT = 6;
    static $LIMIT_AUTOCOMPLETE_CONTENT = 4;

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->q = Yii::$app->request->get('q');

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return $this->render('index',
            [
                'movies'  => $this->content(Movie::find()),
                'tvs'     => $this->content(Tv::find()),
                'peoples' => $this->content(People::find(), 'orig_name', 'name'),
                'q'       => $this->q
            ]
        );
    }

    public function actionAutocomplete()
    {
        $this->layout = false;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/json');

        return array_merge(
            $this->autocomplete(Movie::find()),
            $this->autocomplete(Tv::find()),
            $this->autocomplete(People::find(), 'orig_name', 'name')
        );
    }

    public function content(ActiveQuery $query, $title='title', $orig_title='orig_title') : array
    {
        if( preg_match("/[А-Яа-я]/", $this->q) ) {
            $query->filterWhere(['like', $title, $this->q]);
        } else {
            $query->filterWhere(['like', $orig_title, $this->q]);
        }

        return $query->limit(self::$LIMIT_CONTENT)->asArray()->all();
    }

    public function autocomplete(ActiveQuery $query, $title='title', $orig_title='orig_title') : array
    {
        if( preg_match("/[А-Яа-я]/", $this->q) ) {
            $query->select([$title]);
            $query->filterWhere(['like', $title, $this->q]);
        } else {
            $query->select([$orig_title]);
            $query->filterWhere(['like', $orig_title, $this->q]);
        }

        return $query->limit(self::$LIMIT_AUTOCOMPLETE_CONTENT)->asArray()->column();
    }
}

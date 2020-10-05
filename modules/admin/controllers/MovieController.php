<?php

namespace app\modules\admin\controllers;

use app\models\Blocked;
use app\models\People;
use Faker\Provider\DateTime;
use Yii;
use app\models\Movie;
use app\models\search\MovieSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MovieController implements the CRUD actions for Movie model.
 */
class MovieController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Movie models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MovieSearch();
//        print_r(Yii::$app->request->queryParams);die();

        $params = Yii::$app->request->queryParams;

        $datefilter = $startDate = $endDate = '';

        if (!empty($params['datefilter'])) {
            list($params['MovieSearch']['startDate'], $params['MovieSearch']['endDate']) = explode(" - ", $params['datefilter']);
            $datefilter = $params['MovieSearch']['startDate'] . ' - ' . $params['MovieSearch']['endDate'];
            $startDateArr = explode('-', $params['MovieSearch']['startDate']);
            $startEndDateArr = explode('-', $params['MovieSearch']['endDate']);

            $startDate = $startDateArr[1] . '/' . $startDateArr[2] . '/' . $startDateArr[0];
            $endDate = $startEndDateArr[1] . '/' . $startEndDateArr[2] . '/' . $startEndDateArr[0];
        }

        if (!empty($params['genres'])) {
            $gArr = Movie::getGenresArr();
            $gLabel = $gArr[$params['genres']];
            $params['MovieSearch'][$gLabel] = 1;
        }

        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'datefilter' => $datefilter,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'genres' => $params['genres'] ?? '',
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Movie model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Movie model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Movie();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Movie model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Movie model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Blocked movie
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionBlocked($id)
    {
        $movie = $this->findModel($id);

        if (Blocked::blocked($movie->kp_id, $movie->imdb_id)) {
            $movie->delete();
        }

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelCache($id)
    {
        Yii::$app->cache->delete('movie' . $id);

        return $this->redirect(['movie/view', 'id' => $id]);
    }

    /**
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSetPeoples($id)
    {
        $movie = $this->findModel($id);
        $setPeoples = ArrayHelper::map($movie->peoples, 'id', 'name');


        if(Yii::$app->request->isPost) {
            $postPeoples = $request = Yii::$app->request->post('peoples');

            if($postPeoples){
                $postPeoples = array_flip($postPeoples);
                foreach ($movie->peoples as $people){
                    if(!array_key_exists($people->id, $postPeoples)){
                        $movie->unlink('peoples', $people, true);
                    }
                }

                $peopleForAdd = array_flip(array_diff_key($postPeoples, $setPeoples));

                foreach ($peopleForAdd as $peopleId) {
                    $people = People::findOne($peopleId);

                    if (isset($people))
                    {
                        $movie->link('peoples', $people, ['department' => '1', 'role' => 'tupa personaj']);
                    }
                }

                Yii::$app->session->setFlash('success', "Success actionSetPeoples");
            }
        }

        return $this->render('peoples', ['movie' => $movie]);
    }

    public function actionPeoplesLiveSearch()
    {
        $this->layout = false;

        if(!Yii::$app->request->isPost) return null;

        $q = $request = Yii::$app->request->post('q');
        $movieId = $request = Yii::$app->request->post('movieId');
        $type = $request = Yii::$app->request->post('type');

        if (empty($q) || empty($movieId) || empty($type)) return null;
        if (!in_array($type, ['id', 'name', 'orig_name'])) return null;

        $movie = Movie::findOne($movieId);
        $notIn = ArrayHelper::getColumn($movie->peoples, 'id');



        if ($type == 'id') {
            $peoples = People::find()->filterWhere(['not in', 'id', $notIn])->andFilterWhere([$type => $q])->all();
        } else {
            $peoples = People::find()->filterWhere(['not in', 'id', $notIn])->andFilterWhere(['like', $type, $q])->limit(5)->all();
        }

        $str = '';
        foreach ($peoples as $people){
            $str .= Html::checkbox('peoples[]', false, [
                'label' => Html::a($people->name,
                ['people/view', 'id' => $people->id], ['class' => 'profile-link']),
                'value' => $people->id,
                'class' => 'pFSearch',
            ]);
        }

        return $str;
    }

    /**
     * Finds the Movie model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Movie the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Movie::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\PeopleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Peoples';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="people-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create People', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'tmd_id',
                'format'=>'raw',
                'value' => function($data) {
                    return $data->tmd_id ? Html::a($data->tmd_id, 'https://www.themoviedb.org/person/' . $data->tmd_id) : '<span class="not-set">(not set)</span>';
                }
            ],
            [
                'attribute' => 'imdb_id',
                'format'=>'raw',
                'value' => function($data) {
                    return $data->imdb_id ? Html::a($data->imdb_id  , 'https://www.imdb.com/name/nm' . sprintf("%07d", $data->imdb_id)) : '<span class="not-set">(not set)</span>';
                }
            ],
            'name',
            'orig_name',
            //'birthday',
            //'deathday',
            //'place_of_birth',
            //'popularity',
            //'biography:ntext',
            //'gender',
            //'profile_path',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

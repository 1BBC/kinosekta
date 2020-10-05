<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\BlockedSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Blockeds';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blocked-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Blocked', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            't_created:datetime',
//            'kp_id',
//            'imdb_id',
            [
                'attribute' => 'kp_id',
                'format'=>'raw',
                'value' => function($data) {
                    return $data->kp_id ? Html::a($data->kp_id, 'https://www.kinopoisk.ru/film/' . $data->kp_id) : '<span class="not-set">(not set)</span>';
                }
            ],
            [
                'attribute' => 'imdb_id',
                'format'=>'raw',
                'value' => function($data) {
                    return $data->imdb_id ? Html::a($data->imdb_id, 'https://www.imdb.com/title/tt' . sprintf("%07d", $data->imdb_id)) : '<span class="not-set">(not set)</span>';
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

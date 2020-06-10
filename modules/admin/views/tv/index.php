<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Inflector;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\TvSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tvs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tv-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Tv', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            [
                'attribute' => 'id',
                'format'=>'raw',
                'value' => function($data) {
                    return Html::a($data->id, ['/serialy/view', 'id' => $data->id, 'title' => Inflector::slug($data->title)]);
                }
            ],
//            't_created',
//            't_updated',
            [
                'attribute' => 'tmd_id',
                'format'=>'raw',
                'value' => function($data) {
                    return $data->tmd_id ? Html::a($data->tmd_id, 'https://www.themoviedb.org/tv/' . $data->tmd_id) : '<span class="not-set">(not set)</span>';
                }
            ],
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
            //'r_kp',
            //'r_imdb',
            'title',
            'orig_title',
            //'overview:ntext',
            //'external_ids',
            //'episode_run_time:datetime',
            //'poster',
            'popularity',
            'first_air_date',
            //'images',
            //'video',
            //'is_action_adventure',
            //'is_animation',
            //'is_comedy',
            //'is_crime',
            //'is_documentary',
            //'is_drama',
            //'is_family',
            //'is_kids',
            //'is_mystery',
            //'is_reality',
            //'is_science_fiction_fantasy',
            //'is_soap',
            //'is_talk',
            //'is_war_politics',
            //'is_western',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

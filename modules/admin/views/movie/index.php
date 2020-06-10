<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Inflector;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\MovieSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $startDate string */
/* @var $endDate string */
/* @var $datefilter string */
/* @var $genres int */


$this->title = 'Movies';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="movie-index">

    <h1><?= Html::encode($this->title)?></h1>

    <p>
        <?= Html::a('Create Movie', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            [
                'attribute' => 'id',
                'format'=>'raw',
                'value' => function($data) {
                    return Html::a($data->id, ['/filmy/view', 'id' => $data->id, 'title' => Inflector::slug($data->title)]);
                }
            ],
//            't_created',
//            't_updated',
            [
                'attribute' => 'tmd_id',
                'format'=>'raw',
                'value' => function($data) {
                    return $data->tmd_id ? Html::a($data->tmd_id, 'https://www.themoviedb.org/movie/' . $data->tmd_id) : '<span class="not-set">(not set)</span>';
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
            //'runtime:datetime',
            'title',
            'orig_title',
            //'tagline',
            //'overview:ntext',
            //'budget',
            //'revenue',
            //'external_ids',
            //'poster',
            //'popularity',
            //'images',
            //'video',
            [
                'label' => 'Genres',
                'filter'=> Html::dropDownList('genres', [$genres],
                    ['' => 'all'] + \app\models\Movie::getGenresArr(), ['class' => 'form-control']),
                'value' => function($data) {
                    return $data->getGenresStr();
                }
            ],
            [
                'attribute' => 'release_date',
                'filter'=> Html::input('text', 'datefilter', $datefilter, ['class' => 'form-control']),
                'value' => function($data) {
                    return $data->release_date . ' ' . Yii::$app->request->post('release_date');
                },
                'contentOptions' => ['style' => 'width:200px;  min-width:200px;  '],
            ],
            //'is_action',
            //'is_adventure',
            //'is_animation',
            //'is_comedy',
            //'is_crime',
            //'is_documentary',
            //'is_drama',
            //'is_family',
            //'is_fantasy',
            //'is_history',
            //'is_horror',
            //'is_music',
            //'is_mystery',
            //'is_romance',
            //'is_science_fiction',
            //'is_tv_movie',
            //'is_thriller',
            //'is_war',
            //'is_western',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);

    $jsParam = '';
    if (!empty($startDate) && !empty($endDate)){
        $jsParam = 'startDate: \'' . $startDate . '\', endDate: \'' . $endDate . '\'';
    }

    $this->registerJs(
        '$(function() {

          $(\'input[name="datefilter"]\').daterangepicker({
              showDropdowns: true,
              autoUpdateInput: false,
              ' . $jsParam . '
          });
        
          $(\'input[name="datefilter"]\').on(\'apply.daterangepicker\', function(ev, picker) {
              $(this).val(picker.startDate.format(\'YYYY-MM-DD\') + \' - \' + picker.endDate.format(\'YYYY-MM-DD\'));
          });
        
          $(\'input[name="datefilter"]\').on(\'cancel.daterangepicker\', function(ev, picker) {
              $(this).val(\'\');
          });
        
        });'
    );

    ?>


</div>

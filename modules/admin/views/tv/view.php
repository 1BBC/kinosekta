<?php

use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Tv */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Tvs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tv-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Del cache', ['del-cache', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
        <?= Html::a('Blocked', ['blocked', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to blocked this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'id',
                'format'=>'raw',
                'value' => function($data) {
                    return Html::a($data->id, ['/serialy/view', 'id' => $data->id, 'title' => Inflector::slug($data->title)]);
                }
            ],
            't_created:date',
            't_updated:date',
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
            'r_kp',
            'r_imdb',
//            'imdb_id',
            'first_air_date',
            'title',
            'orig_title',
            'overview:ntext',
            [
                'attribute' => 'external_ids',
                'format'=>'raw',
                'value' => function($data) {
                    if(empty($data->external_ids)){
                        return '<span class="not-set">(not set)</span>';
                    }
                    $ei = explode(',', $data->external_ids);

                    if (isset($ei[0])) {
                        $ei[0] = Html::a($ei[0], 'https://www.facebook.com/' . $ei[0]);
                    }

                    if (isset($ei[1])) {
                        $ei[1] = Html::a($ei[1], 'https://www.instagram.com/' . $ei[1]);
                    }

                    if (isset($ei[2])) {
                        $ei[2] = Html::a($ei[2], 'https://twitter.com/' . $ei[2]);
                    }

                    return implode(',', $ei);
                }

            ],
            'episode_run_time',
            [
                'label' => 'Poster',
                'format'=>'raw',
                'value' => function($data) {
                    $folder = (int) ($data->id / 1000);
                    return Html::img($poster="/i/s/p/" . $folder . "/" . $data->id . ".jpg", ['width' => 100]);
                }
            ],
            'popularity',
            [
                'attribute' => 'images',
                'format'=>'raw',
                'value' => function($data) {
                    $folder = (int) ($data->id / 1000);
                    $str = '';

                    for ($i=1; $i<=$data->images; $i++) {
                        $str .= Html::img("/i/s/s/" . $folder . '/' . $data->id . '-' . $i .".jpg", ['width' => 150]);
                    }

                    return $str;
                }
            ],
            [
                'attribute' => 'video',
                'format'=>'raw',
                'value' => function($data) {
                    return Html::a($data->video, 'https://www.youtube.com/watch?v=' . $data->video);
                }
            ],
            [
                'label' => 'Peoples',
                'format'=>'raw',
                'value' => function($data) {
                    $arr = array();

                    foreach ($data->peoples as $people) {
                        array_push($arr, Html::a($people->name, ['people/view', 'id' => $people->id]));
                    }

                    return implode(',', $arr);
                }
            ],
            [
                'label' => 'Network',
                'format'=>'raw',
                'value' => function($data) {
                    $arr = array();

                    foreach ($data->networks as $network) {
                        array_push($arr, Html::a($network->name, ['network/view', 'id' => $network->id]));
                    }

                    return implode(',', $arr);
                }
            ],
            [
                'label' => 'VideoCdn',
                'format'=>'raw',
                'value' => function($data) {
                    if (!empty($data->kp_id)) {
                        $v = 'kp_id=' . $data->kp_id;
                    } elseif (!empty($data->imdb_id)) {
                        $v = 'imdb_id=tt' . sprintf("%07d", $data->imdb_id);
                    } else {
                        $v = 'name_eng=' . $data->orig_title;
                    }

                    $url = 'https://9684.videocdn.pw/7kytC46MWIdE?' . $v;

                    return Html::a($url, $url);
                }
            ],
            [
                'label' => 'Genres',
                'value' => function($data) {
                    return $data->getGenresStr();
                }
            ],
//            'is_action_adventure',
//            'is_animation',
//            'is_comedy',
//            'is_crime',
//            'is_documentary',
//            'is_drama',
//            'is_family',
//            'is_kids',
//            'is_mystery',
//            'is_reality',
//            'is_science_fiction_fantasy',
//            'is_soap',
//            'is_talk',
//            'is_war_politics',
//            'is_western',
        ],
    ]) ?>

</div>

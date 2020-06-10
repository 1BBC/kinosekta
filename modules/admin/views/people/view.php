<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\People */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Peoples', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="people-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
//            [
//                'attribute' => 'profile_path',
//                'format'=>'raw',
//                'value' => function($data) {
//                    return Html::img('https://image.tmdb.org/t/p/w200/' . $data->profile_path . '.jpg', ['width' => 100]);
//                }
//            ],
            'name',
            'orig_name',
            'birthday',
            'deathday',
            'place_of_birth',
            'popularity',
            'biography:ntext',
            [
                'attribute' => 'gender',
                'value' => function($data) {
                    return $data->getGenderLabel() ?? '<span class="not-set">(not set)</span>';
                }
            ],
        ],
    ]) ?>

</div>

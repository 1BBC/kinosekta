<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Blocked */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Blockeds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="blocked-view">

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
        ],
    ]) ?>

</div>

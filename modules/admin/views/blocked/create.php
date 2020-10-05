<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Blocked */

$this->title = 'Create Blocked';
$this->params['breadcrumbs'][] = ['label' => 'Blockeds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="blocked-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

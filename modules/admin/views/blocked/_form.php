<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Blocked */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="blocked-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 't_created')->textInput() ?>

    <?= $form->field($model, 'kp_id')->textInput() ?>

    <?= $form->field($model, 'imdb_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

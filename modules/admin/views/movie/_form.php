<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Movie */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="movie-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 't_created')->textInput() ?>

    <?= $form->field($model, 't_updated')->textInput() ?>

    <?= $form->field($model, 'tmd_id')->textInput() ?>

    <?= $form->field($model, 'kp_id')->textInput() ?>

    <?= $form->field($model, 'imdb_id')->textInput() ?>

    <?= $form->field($model, 'r_kp')->textInput() ?>

    <?= $form->field($model, 'r_imdb')->textInput() ?>

    <?= $form->field($model, 'release_date')->textInput() ?>

    <?= $form->field($model, 'runtime')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'orig_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tagline')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'overview')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'external_ids')->textInput(['maxlength' => true]) ?>

<!--    --><?//= $form->field($model, 'poster')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'popularity')->textInput() ?>

    <?= $form->field($model, 'images')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'video')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_action')->textInput() ?>

    <?= $form->field($model, 'is_adventure')->textInput() ?>

    <?= $form->field($model, 'is_animation')->textInput() ?>

    <?= $form->field($model, 'is_comedy')->textInput() ?>

    <?= $form->field($model, 'is_crime')->textInput() ?>

    <?= $form->field($model, 'is_documentary')->textInput() ?>

    <?= $form->field($model, 'is_drama')->textInput() ?>

    <?= $form->field($model, 'is_family')->textInput() ?>

    <?= $form->field($model, 'is_fantasy')->textInput() ?>

    <?= $form->field($model, 'is_history')->textInput() ?>

    <?= $form->field($model, 'is_horror')->textInput() ?>

    <?= $form->field($model, 'is_music')->textInput() ?>

    <?= $form->field($model, 'is_mystery')->textInput() ?>

    <?= $form->field($model, 'is_romance')->textInput() ?>

    <?= $form->field($model, 'is_science_fiction')->textInput() ?>

    <?= $form->field($model, 'is_tv_movie')->textInput() ?>

    <?= $form->field($model, 'is_thriller')->textInput() ?>

    <?= $form->field($model, 'is_war')->textInput() ?>

    <?= $form->field($model, 'is_western')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

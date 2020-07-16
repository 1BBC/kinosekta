<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Tv */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tv-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 't_created')->textInput() ?>

    <?= $form->field($model, 't_updated')->textInput() ?>

    <?= $form->field($model, 'tmd_id')->textInput() ?>

    <?= $form->field($model, 'kp_id')->textInput() ?>

    <?= $form->field($model, 'r_kp')->textInput() ?>

    <?= $form->field($model, 'r_imdb')->textInput() ?>

    <?= $form->field($model, 'imdb_id')->textInput() ?>

    <?= $form->field($model, 'first_air_date')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'orig_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'overview')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'external_ids')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'episode_run_time')->textInput() ?>

    <?= $form->field($model, 'popularity')->textInput() ?>

    <?= $form->field($model, 'images')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'video')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_action_adventure')->textInput() ?>

    <?= $form->field($model, 'is_animation')->textInput() ?>

    <?= $form->field($model, 'is_comedy')->textInput() ?>

    <?= $form->field($model, 'is_crime')->textInput() ?>

    <?= $form->field($model, 'is_documentary')->textInput() ?>

    <?= $form->field($model, 'is_drama')->textInput() ?>

    <?= $form->field($model, 'is_family')->textInput() ?>

    <?= $form->field($model, 'is_kids')->textInput() ?>

    <?= $form->field($model, 'is_mystery')->textInput() ?>

    <?= $form->field($model, 'is_reality')->textInput() ?>

    <?= $form->field($model, 'is_science_fiction_fantasy')->textInput() ?>

    <?= $form->field($model, 'is_soap')->textInput() ?>

    <?= $form->field($model, 'is_talk')->textInput() ?>

    <?= $form->field($model, 'is_war_politics')->textInput() ?>

    <?= $form->field($model, 'is_western')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

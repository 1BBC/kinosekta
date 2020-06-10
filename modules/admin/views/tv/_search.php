<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\TvSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tv-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 't_created') ?>

    <?= $form->field($model, 't_updated') ?>

    <?= $form->field($model, 'tmd_id') ?>

    <?= $form->field($model, 'kp_id') ?>

    <?php // echo $form->field($model, 'r_kp') ?>

    <?php // echo $form->field($model, 'r_imdb') ?>

    <?php // echo $form->field($model, 'imdb_id') ?>

    <?php // echo $form->field($model, 'first_air_date') ?>

    <?php // echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'orig_title') ?>

    <?php // echo $form->field($model, 'overview') ?>

    <?php // echo $form->field($model, 'external_ids') ?>

    <?php // echo $form->field($model, 'episode_run_time') ?>

    <?php // echo $form->field($model, 'poster') ?>

    <?php // echo $form->field($model, 'popularity') ?>

    <?php // echo $form->field($model, 'images') ?>

    <?php // echo $form->field($model, 'video') ?>

    <?php // echo $form->field($model, 'is_action_adventure') ?>

    <?php // echo $form->field($model, 'is_animation') ?>

    <?php // echo $form->field($model, 'is_comedy') ?>

    <?php // echo $form->field($model, 'is_crime') ?>

    <?php // echo $form->field($model, 'is_documentary') ?>

    <?php // echo $form->field($model, 'is_drama') ?>

    <?php // echo $form->field($model, 'is_family') ?>

    <?php // echo $form->field($model, 'is_kids') ?>

    <?php // echo $form->field($model, 'is_mystery') ?>

    <?php // echo $form->field($model, 'is_reality') ?>

    <?php // echo $form->field($model, 'is_science_fiction_fantasy') ?>

    <?php // echo $form->field($model, 'is_soap') ?>

    <?php // echo $form->field($model, 'is_talk') ?>

    <?php // echo $form->field($model, 'is_war_politics') ?>

    <?php // echo $form->field($model, 'is_western') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

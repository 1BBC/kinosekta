<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $movie app\models\Movie */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

$this->title = 'Set peoples';
$this->params['breadcrumbs'][] = ['label' => $movie->title, 'url' => ['view', 'id' => 2]];;
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs(
    "$('#search-id').on('input keyup', function(e) {
        type = 'id';
        ajax($(this).val(), type);
    });

    $('#search-name').on('input keyup', function(e) {
        type = 'name';
        ajax($(this).val(), type);
    });

    $('#search-orig-name').on('input keyup', function(e) {
        type = 'orig_name';
        ajax($(this).val(), type);
    });
    
    function ajax(val, type) {
        $.ajax({
            url: '" . Url::toRoute(['movie/peoples-live-search']) . "',
            type: 'post',
            data: {
            q: val,
            type : type,
            movieId: " . $movie->id . ",
            _csrf : '" . Yii::$app->request->getCsrfToken() . "',
        },
        success: function (data) {
            checkboxes = $(\".pFSearch\");
            
            for (var index = 0; index < checkboxes.length; index++) {
                 if (checkboxes[index].checked == false) {
                    checkboxes[index].closest('label').remove();
                 }
            }
            $(\".checkBoxes\").append(data);
        }
    })} ",
    View::POS_READY,
    'search-peoples'
);
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <small>Actor id:</small>
    <?= Html::input('text', 'search-id', null, ['class' => 'form-control', 'id' => 'search-id'])?>
    <small>Actor name:</small>
        <?= Html::input('text', 'search-name', null, ['class' => 'form-control', 'id' => 'search-name'])?>
    <small>Actor orig-name:</small>
    <?= Html::input('text', 'search-orig-name', null, ['class' => 'form-control', 'id' => 'search-orig-name'])?>
    <br>


    <?php $form = ActiveForm::begin(); ?>

    <div class="checkBoxes">
        <?php foreach ($movie->peoples as $people): ?>
            <?= Html::checkbox('peoples[]', true, ['label' => Html::a($people->name, ['people/view', 'id' => $people->id], ['class' => 'profile-link']), 'value' => $people->id]) ?>
        <?php endforeach; ?>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>






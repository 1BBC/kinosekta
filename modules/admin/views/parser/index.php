<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $result array */
/* @var $params array */

$this->title = 'Index';
$this->params['breadcrumbs'][] = ['label' => 'Parser', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $form = ActiveForm::begin(); ?>
<pre>
    <?php
        if ($result && is_array($result)) {
            foreach ($result as $item) {
                echo $item . "\n";
            }
        }
    ?>
</pre>
    <div class="form-group">
        <label>Id`s</label>
        <input value="<?=$params['ids'] ?? ''?>" type="text" class="form-control" name="ids" placeholder="1209567,tt9165642">
    </div>
    <div class="form-group">
        <label for="type">Type</label>
        <select class="form-control" name="type">
            <option <?=(!empty($params['type']) && $params['type'] == 'kp') ? 'selected' : '' ?>>kp</option>
            <option <?=(!empty($params['type']) && $params['type'] == 'imdb') ? 'selected' : '' ?>>imdb</option>
<!--            <option -->
<!--                --><?//=(!empty($params['type']) && $params['type'] == 'tmd') ? 'selected' : '' ?>
<!--            >tmd</option>-->
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>

<?php ActiveForm::end(); ?>
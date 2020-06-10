<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $result array */

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
        <label>Media</label>
        <select name="media" class="form-control">
            <option>movie</option>
            <option>tv</option>
        </select>
    </div>
    <div class="form-group">
        <label>Id`s</label>
        <input type="text" class="form-control" name="ids" placeholder="1228547,1324432">
    </div>
    <div class="form-group">
        <label for="type">Type</label>
        <select class="form-control" name="type">
            <option>kp</option>
            <option>imdb</option>
            <option>tmd</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>

<?php ActiveForm::end(); ?>
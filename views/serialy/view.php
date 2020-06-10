<?php


/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $movie object */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Filmy';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= $movie['title'] ?><small> <?= $movie['tagline'] ?? '' ?></small></h1>
<p><?= $movie['overview'] ?></p>
<?= Html::a('URL', ['/filmy/view', 'id' => 15]) ?>
<?= print_r($movie); ?>


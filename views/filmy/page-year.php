<?php

/* @var $this yii\web\View */
/* @var $movies array */


use yii\helpers\Html;
use yii\helpers\Inflector;
?>

<?php foreach ($movies as $movie):?>

    <div class="card-film figure col-lg-2 col-md-3 col-sm-4 col-6 my-2">

        <div class="img-figure-block">
            <a href="<?= '/filmy/' . $movie['id'] . '-' . Inflector::slug($movie['title']) ?>">
                <?php $poster = "/i/f/p/" . (int) ($movie['id'] / 1000) . "/" . $movie['id'] . ".jpg"; ?>
                <img src="<?= $poster ?>" style="width: 100%; box-shadow: 0 0 8px rgba(0,0,0,0.5);" class="image-figure figure-img img-fluid rounded" alt="A generic square placeholder image with rounded corners in a figure.">
                <div class="img-figure-overlay">
                    <div class="img-figure-text"><i class="far fa-play-circle"></i></div>
                </div>
            </a>


        </div>

        <?php
        $rating =  ($movie['r_kp']) ? ($movie['r_kp'] / 10) : ($movie['r_imdb']) ?  ($movie['r_kp'] / 10) : null;
        $rating = (!empty($rating)) ? ', ★ ' . $rating : null;
        ?>

        <div class="figure-caption">
            <?= Html::a($movie['title'], ['filmy/view', 'id' => $movie['id'], 'title' => Inflector::slug($movie['title'])], ['style' => "margin-bottom: 0px; font-size: 1em", 'class' => 'font-weight-bold'])?>
            <!--                    <a href="movie.html" style="margin-bottom: 0px; font-size: 1.3em" class="font-weight-bold">--><?//= $movie['title']?><!--</a>-->
            <p class="font-weight-light" style="margin-bottom: 0px; font-size: 0.9em"><?= date_format(date_create($movie['release_date']), 'Y') . $rating ?></p>
        </div>

    </div>

<?php endforeach;?>
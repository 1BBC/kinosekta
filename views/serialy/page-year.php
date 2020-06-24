<?php

/* @var $this yii\web\View */
/* @var $tvs array */


use yii\helpers\Html;
use yii\helpers\Inflector;
?>

<?php foreach ($tvs as $tv):?>

    <div class="card-film figure col-lg-2 col-md-3 col-sm-4 col-6 my-2">

        <div class="img-figure-block">
            <a href="<?= '/serialy/' . $tv['id'] . '-' . Inflector::slug($tv['title']) ?>">
                <?php $poster = "/i/s/p/" . (int) ($tv['id'] / 1000) . "/" . $tv['id'] . ".jpg"; ?>
                <img src="<?= $poster ?>" style="width: 100%; box-shadow: 0 0 8px rgba(0,0,0,0.5);" class="image-figure figure-img img-fluid rounded" alt="<?=$tv['title']?>">
                <div class="img-figure-overlay">
                    <div class="img-figure-text"><i class="far fa-play-circle"></i></div>
                </div>
            </a>


        </div>

        <?php
        $rating =  ($tv['r_kp']) ? ($tv['r_kp'] / 10) : ($tv['r_imdb']) ?  ($tv['r_kp'] / 10) : null;
        $rating = (!empty($rating)) ? ', ★ ' . $rating : null;
        ?>

        <div class="figure-caption">
            <?= Html::a($tv['title'], ['serialy/view', 'id' => $tv['id'], 'title' => Inflector::slug($tv['title'])], ['style' => "margin-bottom: 0px; font-size: 1em", 'class' => 'font-weight-bold'])?>
            <!--                    <a href="movie.html" style="margin-bottom: 0px; font-size: 1.3em" class="font-weight-bold">--><?//= $tv['title']?><!--</a>-->
            <p class="font-weight-light" style="margin-bottom: 0px; font-size: 0.9em"><?= date_format(date_create($tv['release_date']), 'Y') . $rating ?></p>
        </div>

    </div>

<?php endforeach;?>
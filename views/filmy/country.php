<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $movies array */
/* @var $country array */
/* @var $lastPage integer */
/* @var $iso string */


use yii\helpers\Html;
use yii\helpers\Inflector;

$this->title = $country['name'] . ' - лучшие фильмы.';

\Yii::$app->view->registerMetaTag([
    'name' => 'description',
    'content' => 'Лучшие фильмы производства ' . $country['name'] . '. Смотрите онлайн бесплатно в хорошем качестве.',
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:description',
    'content' => 'Лучшие фильмы производства ' . $country['name'] . '. Смотрите онлайн бесплатно в хорошем качестве.',
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'pageCount',
    'content' => $lastPage,
]);
\Yii::$app->view->registerMetaTag([
    'name' => 'pageCountry',
    'content' => $iso,
]);

$this->registerJsFile(
    '@web/js/ajax/a_country.js',
    ['depends' => [\app\assets\SiteAsset::className()]]
);
?>

<div class="container" style="background-color: #FFFFFF; box-shadow: 0 0 1px rgba(0,0,0,0.5);">

    <div class="">&nbsp;</div>
    <nav aria-label="breadcrumb my-2">
        <ol class="breadcrumb" style="padding: 6px 8px;">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item"><a href="/filmy/">Фильмы</a></li>
            <li class="breadcrumb-item active"><?=$country['name'] ?></li>
        </ol>
    </nav>

    <div class="title">
        <h1 class="font-weight-bold">Фильмы производства страны <?=$country['name'] ?></h1>
    </div>
    <hr>
    <nav>
        <ul class="pagination justify-content-center">

        </ul>
    </nav>

    <div class="row row-figure">

        <?php foreach ($movies as $movie):?>

            <div class="card-film figure col-lg-2 col-md-3 col-sm-4 col-6 my-2">

                <div class="img-figure-block">
                    <a href="<?= '/filmy/' . $movie['id'] . '-' . Inflector::slug($movie['title']) ?>">
                        <?php $poster = "/i/f/p/" . (int) ($movie['id'] / 1000) . "/" . $movie['id'] . ".jpg"; ?>
                        <img src="<?= $poster ?>" style="width: 100%; box-shadow: 0 0 8px rgba(0,0,0,0.5);" class="image-figure figure-img img-fluid rounded" alt="<?=$movie['title']?>">
                        <div class="img-figure-overlay">
                            <div class="img-figure-text"><i class="far fa-play-circle"></i></div>
                        </div>
                    </a>


                </div>
                <?php
                $rating =  ($movie['r_kp']) ? ($movie['r_kp'] / 10) : (($movie['r_imdb']) ? ($movie['r_imdb'] / 10) : null);
                $rating = (!empty($rating)) ? '★ ' . $rating : null;
                ?>
                <div class="figure-caption">
                    <?= Html::a($movie['title'], ['filmy/view', 'id' => $movie['id'], 'title' => Inflector::slug($movie['title'])], ['style' => "margin-bottom: 0px; font-size: 1em", 'class' => 'font-weight-bold'])?>
                    <!--                    <a href="movie.html" style="margin-bottom: 0px; font-size: 1.3em" class="font-weight-bold">--><?//= $movie['title']?><!--</a>-->
                    <p class="font-weight-light" style="margin-bottom: 0; font-size: 0.9em"><?= $rating ?></p>
                </div>

            </div>

        <?php endforeach;?>

    </div>
    <div class="loading d-flex justify-content-center my-2">
        <div class="loader"></div>
    </div>
    <div class="lp-message">
    </div>
</div>





<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $movies object */
/* @var $genre array */
/* @var $lastPage integer */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Inflector;
\Yii::$app->view->registerMetaTag([
    'name' => 'pageCount',
    'content' => $lastPage,
]);
\Yii::$app->view->registerMetaTag([
    'name' => 'pageGenre',
    'content' => $genre['name'],
]);

$this->title = 'Фильмы жанра ' . $genre['title'].' смотреть бесплатно онлайн в хорошем качестве';

$description = 'Лучшие фильмы в жанре ' . $genre['title'] . '. Смотреть список. ' . stristr(($genre['text'] ?? ''), '.', true);
\Yii::$app->view->registerMetaTag([
    'name' => 'description',
    'content' => $description,
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:description',
    'content' => $description,
]);

$this->registerJsFile(
    '@web/js/ajax/a_genre.js',
    ['depends' => [\app\assets\SiteAsset::className()]]
);
?>

<div class="container" style="background-color: #FFFFFF; box-shadow: 0 0 1px rgba(0,0,0,0.5);">

    <div class="">&nbsp;</div>
    <nav aria-label="breadcrumb my-2">
        <ol class="breadcrumb" style="padding: 6px 8px;">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item"><a href="/filmy/">Фильмы</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $genre['title'] ?></li>
        </ol>
    </nav>
    <div class="title">
        <h1 class="font-weight-bold">Фильмы жанра <?= $genre['title'] ?></h1>
    </div>
    <article>
        <?= $genre['text'] ?? ''?>
    </article>
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
                $rating =  ($movie['r_kp']) ? ($movie['r_kp'] / 10) : ($movie['r_imdb']) ?  ($movie['r_kp'] / 10) : null;
                $rating = (!empty($rating)) ? '★ ' . $rating : null;
                ?>
                <div class="figure-caption">
                    <?= Html::a($movie['title'], ['filmy/view', 'id' => $movie['id'], 'title' => Inflector::slug($movie['title'])], ['style' => "margin-bottom: 0px; font-size: 1em", 'class' => 'font-weight-bold'])?>
                    <!--                    <a href="movie.html" style="margin-bottom: 0px; font-size: 1.3em" class="font-weight-bold">--><?//= $movie['title']?><!--</a>-->
                    <p class="font-weight-light" style="margin-bottom: 0px; font-size: 0.9em"><?= date_format(date_create($movie['release_date']), 'Y') . ', ' . $rating ?></p>
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




<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $tvs object */
/* @var $year integer */
/* @var $lastPage integer */

use yii\helpers\Html;
use yii\helpers\Inflector;

\Yii::$app->view->registerMetaTag([
    'name' => 'pageCount',
    'content' => $lastPage,
]);
\Yii::$app->view->registerMetaTag([
    'name' => 'pageYear',
    'content' => $year,
]);

$this->title = 'Сериалы ' . $year .' года';

\Yii::$app->view->registerMetaTag([
    'name' => 'description',
    'content' => 'Лучшие сериалы ' . $year . ' года смотреть бесплатно онлайн в хорошем качестве.',
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:description',
    'content' => 'Лучшие сериалы ' . $year . ' года смотреть бесплатно онлайн в хорошем качестве.',
]);

//\Yii::$app->view->registerMetaTag([
//    'name' => 'og:image',
//    'content' => '',
//]);

$this->registerJsFile(
    '@web/js/ajax/a_s_year.js',
    ['depends' => [\app\assets\SiteAsset::className()]]
);
?>

<div class="container" style="background-color: #FFFFFF; box-shadow: 0 0 1px rgba(0,0,0,0.5);">

    <div class="">&nbsp;</div>
    <nav aria-label="breadcrumb my-2">
        <ol class="breadcrumb" style="padding: 6px 8px;">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item"><a href="/serialy/">Сериалы</a></li>
            <li class="breadcrumb-item active"><?=$year ?> года</li>
        </ol>
    </nav>

    <div class="title">
        <h1 class="font-weight-bold">Сериалы <?=$year ?> года</h1>
    </div>
    <hr>
    <nav>
        <ul class="pagination justify-content-center">

        </ul>
    </nav>

    <div class="row row-figure">

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
                $rating =  ($tv['r_kp']) ? ($tv['r_kp'] / 10) : (($tv['r_imdb']) ? ($tv['r_imdb'] / 10) : null);
                $rating = (!empty($rating)) ? '★ ' . $rating : null;
                ?>
                <div class="figure-caption">
                    <?= Html::a($tv['title'], ['serialy/view', 'id' => $tv['id'], 'title' => Inflector::slug($tv['title'])], ['style' => "margin-bottom: 0px; font-size: 1em", 'class' => 'font-weight-bold'])?>
                    <!--                    <a href="movie.html" style="margin-bottom: 0px; font-size: 1.3em" class="font-weight-bold">--><?//= $tv['title']?><!--</a>-->
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




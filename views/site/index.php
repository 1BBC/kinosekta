<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $movies object */
/* @var $peoples object */

use yii\helpers\Html;
use yii\helpers\Inflector;

$this->title = 'Онлайн фильмы, сериалы, мультфильмы, смотрите бесплатно онлайн в хорошем качестве';

\Yii::$app->view->registerMetaTag([
    'name' => 'description',
    'content' => 'Хотите найти фильм для просмотра? Воспользуйтесь нашей большой и удобной онлайн кинотекой. Смотрите беслатно кино в хорошем качестве без регистрации',
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:description',
    'content' => 'Хотите найти фильм для просмотра? Воспользуйтесь нашей большой и удобной онлайн кинотекой. Смотрите беслатно кино в хорошем качестве без регистрации',
]);

//\Yii::$app->view->registerMetaTag([
//    'name' => 'og:image',
//    'content' => '',
//]);


?>

<div class="container" style="background-color: #FFFFFF; box-shadow: 0 0 1px rgba(0,0,0,0.5);">

    <div class="">&nbsp;</div>
    <nav aria-label="breadcrumb my-2">
        <ol class="breadcrumb" style="padding: 6px 8px;">
            <li class="breadcrumb-item active" aria-current="page">Главная</li>
        </ol>
    </nav>
    <div class="title">
        <h1 class="font-weight-bold">Смотреть бесплатно онлайн фильмы</h1>
    </div>

    <hr>

    <h2>Фильмы</h2>


    <div class="row row-figure">

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
                    <p class="font-weight-light" style="margin-bottom: 0px; font-size: 0.9em"><a style="color: #6C757D;" href="/filmy/<?= date_format(date_create($movie['release_date']), 'Y')?>-goda/"><?= date_format(date_create($movie['release_date']), 'Y')?></a><?= $rating ?></p>
                </div>

            </div>

        <?php endforeach;?>

    </div>
    <a role="button" href="/filmy/" class="btn btn-sm btn-block btn-outline-dark">смотреть все фильмы</a>

    <hr>

    <h2>Актеры</h2>

    <div class="row row-figure">

        <?php foreach ($peoples as $people):?>

            <div class="card-film figure col-lg-2 col-md-3 col-sm-4 col-6 my-2">

                <div class="img-figure-block">
                    <a href="<?= '/aktery/' . $people['id'] . '-' . Inflector::slug($people['name']) ?>">
                        <?php $poster = "/i/a/" . (int) ($people['id'] / 1000) . "/" . $people['id'] . ".jpg"; ?>
                        <img src="<?= $poster ?>" style="width: 100%; box-shadow: 0 0 8px rgba(0,0,0,0.5);" class="image-figure figure-img img-fluid rounded" alt="A generic square placeholder image with rounded corners in a figure.">
                        <div class="img-figure-overlay">
                            <div class="img-figure-text"><i class="far fa-play-circle"></i></div>
                        </div>
                    </a>


                </div>

                <div class="figure-caption">
                    <?= Html::a($people['orig_name'], ['aktery/view', 'id' => $people['id'], 'title' => Inflector::slug($people['name'])], ['style' => "margin-bottom: 0px; font-size: 1em", 'class' => 'font-weight-bold'])?>
                    <!--                    <a href="movie.html" style="margin-bottom: 0px; font-size: 1.3em" class="font-weight-bold">--><?//= $movie['title']?><!--</a>-->
                    <!--                    <p class="font-weight-light" style="margin-bottom: 0px; font-size: 0.9em">--><?//= date_format(date_create($movie['release_date']), 'Y') . ', ★ ' . $movie['r_kp'] ?><!--</p>-->
                </div>

            </div>

        <?php endforeach;?>

    </div>
    <a role="button" href="/aktery/" class="btn btn-sm btn-block btn-outline-dark">смотреть всех актеров</a>

    <hr>

    <h2>Кинотеатр</h2>
    <p>Могу сказать только спасибо что воспользовались нашим ресурсом.
        Буду рад стараться подгружать контент в большом количестве каждый божий день. Ну там, фильмы разные, мультфильмы, сериалы и прочие кино-материалы. Сайт предлагает свежие новинки и старую классику, и все это в лучшем качестве что есть в интернете - HD и не только.
        Страницы адаптивные - это значит что Вам будет удобно пользоваться сайтом как на портативном компьютере, так и на мобильном телефоне или планшете. С уважением админ.
    </p>

    <?= $this->render('/layouts/footer');?>
</div>



<style>
    .img-figure-block {
        position: relative;
    }

    .image-figure {
        display: block;
        width: 100%;
        height: auto;
        min-height: 255.78px;
    }

    .img-figure-overlay {
        border: 2px double #FC8638;
        border-radius: 4px;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        height: 100%;
        width: 100%;
        opacity: 0;
        transition: .5s ease;
        background-color: rgba(0,0,0,0.4);
    }

    .img-figure-block:hover .img-figure-overlay {
        opacity: 1;
    }

    .img-figure-text {
        color: white;
        font-size: 7em;
        position: absolute;
        top: 50%;
        left: 50%;
        -webkit-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        text-align: center;
    }
</style>
<!---->
<!--<style type="text/css">-->
<!--    .card-film {-->
<!--        border: 1px double white;-->
<!--    }-->
<!---->
<!--    .card-film > img {-->
<!--        /*margin: 0px;*/-->
<!--    }-->
<!---->
<!--    .figure {-->
<!--        /*margin: 0px;*/-->
<!--        padding: 0px 7.5px;-->
<!--    }-->
<!---->
<!--    .row-figure {-->
<!--        padding: 0px 7.5px;-->
<!--    }-->
<!---->
<!--    .figure-caption > a{-->
<!--        color: #FC8638;-->
<!--    }-->
<!--</style>-->
<!---->
<!--<style>-->
<!--    .loader {-->
<!--        border: 16px solid #f3f3f3; /* Light grey */-->
<!--        border-top: 16px solid #FC8638; /* Blue */-->
<!--        border-radius: 50%;-->
<!--        width: 60px;-->
<!--        height: 60px;-->
<!--        animation: spin 2s linear infinite;-->
<!--    }-->
<!---->
<!--    @keyframes spin {-->
<!--        0% { transform: rotate(0deg); }-->
<!--        100% { transform: rotate(360deg); }-->
<!--    }-->
<!--</style>-->




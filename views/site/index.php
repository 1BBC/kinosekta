<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $cartoons object */
/* @var $movies object */
/* @var $tvs object */
/* @var $tv_cartoons object */
/* @var $peoples object */

use yii\helpers\Html;
use yii\helpers\Inflector;

$this->title = 'Фильмы, сериалы, мультфильмы смотрите онлайн бесплатно онлайн в хорошем качестве';

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
        <h1 class="font-weight-bold">Смотреть онлайн фильмы и сериалы бесплатно</h1>
    </div>

    <hr>

    <h2>Фильмы</h2>


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
                $rating = (!empty($rating)) ? ', ★ ' . $rating : null;
                ?>

                <div class="figure-caption">
                    <?= Html::a($movie['title'], ['filmy/view', 'id' => $movie['id'], 'title' => Inflector::slug($movie['title'])], ['style' => "margin-bottom: 0px; font-size: 1em", 'class' => 'font-weight-bold'])?>
                    <!--                    <a href="movie.html" style="margin-bottom: 0px; font-size: 1.3em" class="font-weight-bold">--><?//= $movie['title']?><!--</a>-->
                    <p class="font-weight-light" style="margin-bottom: 0; font-size: 0.9em"><a style="color: #6C757D;" href="/filmy/<?= date_format(date_create($movie['release_date']), 'Y')?>-goda/"><?= date_format(date_create($movie['release_date']), 'Y')?></a><?= $rating ?></p>
                </div>

            </div>

        <?php endforeach;?>

    </div>
    <a role="button" href="/filmy/" class="btn btn-sm btn-block btn-outline-dark">смотреть все фильмы</a>

    <hr>

    <h2>Сериалы</h2>


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
                $rating =  ($tv['r_kp']) ? ($tv['r_kp'] / 10) : ($tv['r_imdb']) ?  ($tv['r_kp'] / 10) : null;
                $rating = (!empty($rating)) ? ', ★ ' . $rating : null;
                ?>

                <div class="figure-caption">
                    <?= Html::a($tv['title'], ['serialy/view', 'id' => $tv['id'], 'title' => Inflector::slug($tv['title'])], ['style' => "margin-bottom: 0px; font-size: 1em", 'class' => 'font-weight-bold'])?>
                    <!--                    <a href="movie.html" style="margin-bottom: 0px; font-size: 1.3em" class="font-weight-bold">--><?//= $movie['title']?><!--</a>-->
                    <p class="font-weight-light" style="margin-bottom: 0; font-size: 0.9em"><a style="color: #6C757D;" href="/serialy/<?= date_format(date_create($tv['first_air_date']), 'Y')?>-goda/"><?= date_format(date_create($tv['first_air_date']), 'Y')?></a><?= $rating ?></p>
                </div>

            </div>

        <?php endforeach;?>

    </div>
    <a role="button" href="/serialy/" class="btn btn-sm btn-block btn-outline-dark">смотреть все сериалы</a>

    <hr>

    <h2>Мультфильмы</h2>

    <div class="row row-figure">

        <?php foreach ($cartoons as $cartoon):?>

            <div class="card-film figure col-lg-2 col-md-3 col-sm-4 col-6 my-2">

                <div class="img-figure-block">
                    <a href="<?= '/filmy/' . $cartoon['id'] . '-' . Inflector::slug($cartoon['title']) ?>">
                        <?php $poster = "/i/f/p/" . (int) ($cartoon['id'] / 1000) . "/" . $cartoon['id'] . ".jpg"; ?>
                        <img src="<?= $poster ?>" style="width: 100%; box-shadow: 0 0 8px rgba(0,0,0,0.5);" class="image-figure figure-img img-fluid rounded" alt="<?=$cartoon['title']?>">
                        <div class="img-figure-overlay">
                            <div class="img-figure-text"><i class="far fa-play-circle"></i></div>
                        </div>
                    </a>


                </div>

                <?php
                $rating =  ($cartoon['r_kp']) ? ($cartoon['r_kp'] / 10) : ($cartoon['r_imdb']) ?  ($cartoon['r_kp'] / 10) : null;
                $rating = (!empty($rating)) ? ', ★ ' . $rating : null;
                ?>

                <div class="figure-caption">
                    <?= Html::a($cartoon['title'], ['filmy/view', 'id' => $cartoon['id'], 'title' => Inflector::slug($cartoon['title'])], ['style' => "margin-bottom: 0px; font-size: 1em", 'class' => 'font-weight-bold'])?>
                    <!--                    <a href="movie.html" style="margin-bottom: 0px; font-size: 1.3em" class="font-weight-bold">--><?//= $movie['title']?><!--</a>-->
                    <p class="font-weight-light" style="margin-bottom: 0; font-size: 0.9em"><a style="color: #6C757D;" href="/serialy/<?= date_format(date_create($cartoon['release_date']), 'Y')?>-goda/"><?= date_format(date_create($cartoon['release_date']), 'Y')?></a><?= $rating ?></p>
                </div>

            </div>

        <?php endforeach;?>

    </div>
    <a role="button" href="/filmy/multfilmi/" class="btn btn-sm btn-block btn-outline-dark">смотреть все мультфильмы</a>

    <hr>

    <h2>Мультсериалы</h2>

    <div class="row row-figure">

        <?php foreach ($tv_cartoons as $tc):?>

            <div class="card-film figure col-lg-2 col-md-3 col-sm-4 col-6 my-2">

                <div class="img-figure-block">
                    <a href="<?= '/serialy/' . $tc['id'] . '-' . Inflector::slug($tc['title']) ?>">
                        <?php $poster = "/i/s/p/" . (int) ($tc['id'] / 1000) . "/" . $tc['id'] . ".jpg"; ?>
                        <img src="<?= $poster ?>" style="width: 100%; box-shadow: 0 0 8px rgba(0,0,0,0.5);" class="image-figure figure-img img-fluid rounded" alt="<?=$tc['title']?>">
                        <div class="img-figure-overlay">
                            <div class="img-figure-text"><i class="far fa-play-circle"></i></div>
                        </div>
                    </a>


                </div>

                <?php
                $rating =  ($tc['r_kp']) ? ($tc['r_kp'] / 10) : ($tc['r_imdb']) ?  ($tc['r_kp'] / 10) : null;
                $rating = (!empty($rating)) ? ', ★ ' . $rating : null;
                ?>

                <div class="figure-caption">
                    <?= Html::a($tc['title'], ['serialy/view', 'id' => $tc['id'], 'title' => Inflector::slug($tc['title'])], ['style' => "margin-bottom: 0px; font-size: 1em", 'class' => 'font-weight-bold'])?>
                    <!--                    <a href="movie.html" style="margin-bottom: 0px; font-size: 1.3em" class="font-weight-bold">--><?//= $movie['title']?><!--</a>-->
                    <p class="font-weight-light" style="margin-bottom: 0; font-size: 0.9em"><a style="color: #6C757D;" href="/serialy/<?= date_format(date_create($tc['first_air_date']), 'Y')?>-goda/"><?= date_format(date_create($tc['first_air_date']), 'Y')?></a><?= $rating ?></p>
                </div>

            </div>

        <?php endforeach;?>

    </div>
    <a role="button" href="/serialy/multfilmi/" class="btn btn-sm btn-block btn-outline-dark">смотреть все мультсериалы</a>

    <hr>

    <h2>Актеры</h2>

    <div class="row row-figure">

        <?php foreach ($peoples as $people):?>

            <div class="card-film figure col-lg-2 col-md-3 col-sm-4 col-6 my-2">

                <div class="img-figure-block">
                    <a href="<?= '/aktery/' . $people['id'] . '-' . Inflector::slug($people['name']) ?>">
                        <?php $poster = "/i/a/" . (int) ($people['id'] / 1000) . "/" . $people['id'] . ".jpg"; ?>
                        <img src="<?= $poster ?>" style="width: 100%; box-shadow: 0 0 8px rgba(0,0,0,0.5);" class="image-figure figure-img img-fluid rounded" alt="<?=$people['name']?>">
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

    <h2>О кинотеатре</h2>
    <p>Могу сказать только спасибо что воспользовались нашим ресурсом.
        Буду рад стараться подгружать контент в большом количестве каждый божий день. Ну там, фильмы разные, мультфильмы, сериалы и прочие кино-материалы. Сайт предлагает свежие новинки и старую классику, и все это в лучшем качестве что есть в интернете — HD и не только.
        Страницы адаптивные — это значит что Вам будет удобно пользоваться сайтом как на портативном компьютере, так и на мобильном телефоне или планшете. С уважением админ.
    </p>

    <?= $this->render('/layouts/footer');?>
</div>




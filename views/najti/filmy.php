<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $movies array */
/* @var $q string */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Inflector;
?>

<div class="container" style="background-color: #FFFFFF; box-shadow: 0 0 1px rgba(0,0,0,0.5);">

    <div class="">&nbsp;</div>
    <nav aria-label="breadcrumb my-2">
        <ol class="breadcrumb" style="padding: 6px 8px;">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item"><a href="/filmy/">Фильмы</a></li>
            <li class="breadcrumb-item active" aria-current="page">Поиск</li>
        </ol>
    </nav>

    <div class="page-header title">
        <h1 class="font-weight-bold">Поиск по фильмам
            <small>всего <?= count($movies)?> шт.</small>
        </h1>
    </div>

    <br>
    <form>
        <div class="input-group">
            <input type="text" class="form-control" value="<?=$q?>" name="q" placeholder="Введите название фильма" aria-label="Введите название фильма" aria-describedby="basic-addon2">

            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">Найти</button>
            </div>
        </div>

    </form>
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
</div>
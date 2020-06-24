<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $peoples object */
/* @var $q string */

use yii\helpers\Html;
use yii\helpers\Inflector;
?>

<div class="container" style="background-color: #FFFFFF; box-shadow: 0 0 1px rgba(0,0,0,0.5);">

    <div class="">&nbsp;</div>
    <nav aria-label="breadcrumb my-2">
        <ol class="breadcrumb" style="padding: 6px 8px;">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item"><a href="/aktery/">Актеры</a></li>
            <li class="breadcrumb-item active" aria-current="page">Поиск</li>
        </ol>
    </nav>

    <div class="page-header title">
        <h1 class="font-weight-bold">Поиск по актерам
            <small>всего <?= count($peoples)?> шт.</small>
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
    <hr>
    <nav>
        <ul class="pagination justify-content-center">

        </ul>
    </nav>

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
</div>




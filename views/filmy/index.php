<?php

/* @var $this yii\web\View */
/* @var $movies object */
/* @var $genreList array */
/* @var $countryList array */
/* @var $lastPage integer */


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Inflector;

$this->title = 'Фильмы онлайн: смотрите бесплатно онлайн в хорошем качестве';

$description = 'Фильмы онлайн: возникают проблемы с поиском? Воспользуйтесь нашей большой и удобной онлайн кинотекой: kinogaz. Смотрите бесллатно кино в хорошем качестве без регистрации';

\Yii::$app->view->registerMetaTag([
    'name' => 'description',
    'content' => $description,
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:description',
    'content' => $description,
]);

//\Yii::$app->view->registerMetaTag([
//    'name' => 'og:image',
//    'content' => '',
//]);


\Yii::$app->view->registerMetaTag([
    'name' => 'pageCount',
    'content' => $lastPage,
]);
$this->registerJsFile(
    '@web/js/ajax/a_index.js',
        ['depends' => [\app\assets\SiteAsset::className()]]
);
?>

<div class="container" style="background-color: #FFFFFF; box-shadow: 0 0 1px rgba(0,0,0,0.5);">

    <div class="">&nbsp;</div>
    <nav aria-label="breadcrumb my-2">
        <ol class="breadcrumb" style="padding: 6px 8px;">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item active" aria-current="page">Фильмы</li>
        </ol>
    </nav>
    <div class="title">
        <h1 class="font-weight-bold">Фильмы смотреть онлайн</h1>
    </div>
    <form>
        <div class="form-row form-row center mt-4">
            <div class="col-xs-4">
                <div class="form-group">
                    <select id="s-type" class="selectpicker form-control" title="по умолчанию">
<!--                        <option value="0">по умолчанию</option>-->
                        <option value="1">по дате</option>
                        <option value="2">по рейтингу</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    <select id="s-year" class="selectpicker form-control" title="год" data-live-search="true" >
<!--                        <option value="0">год</option>-->
                        <?php for ($i = 2020; $i >= 2015; $i--):?>
                            <option value="<?=$i?>"><?=$i?></option>
                        <?php endfor;?>
                    </select>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    <select id="s-genre" multiple data-max-options="2" class="selectpicker form-control" title="жанр" data-live-search="true" >
<!--                        <option value="0">жанр</option>-->
                        <?php foreach ($genreList as $key => $value):?>
                            <option value="<?=$key?>"><?=$value['name']?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    <select id="s-country" class="selectpicker form-control" title="страна" data-live-search="true" >
<!--                        <option value="0">страна</option>-->
                        <?php foreach ($countryList as $iso => $country):?>
                            <?php if (!is_array($country)) continue;?>
                            <option data-subtext="<?=$iso?>" value="<?=$iso?>"><?=$country['name']?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="form-group btn-group">
                    <button type="button" id="del-filter" class="btn btn-outline-danger">Сбросить</button>
                    <button type="button" id="set-filter" class="btn btn-outline-success">Отсортировать</button>
                </div>
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
                    <p class="font-weight-light" style="margin-bottom: 0; font-size: 0.9em"><a style="color: #6C757D;" href="/filmy/<?= date_format(date_create($movie['release_date']), 'Y')?>-goda/"><?= date_format(date_create($movie['release_date']), 'Y')?></a><?= $rating ?></p>
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





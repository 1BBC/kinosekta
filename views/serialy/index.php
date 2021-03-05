<?php

/* @var $this yii\web\View */
/* @var $tvs object */
/* @var $genreList array */
///* @var $countryList array */
/* @var $lastPage integer */


use app\assets\SiteAsset;
use yii\helpers\Html;
use yii\helpers\Inflector;

$this->title = 'Сериалы онлайн: смотрите бесплатно в хорошем качестве';

$description = 'Сериалы онлайн: возникают проблемы с поиском? Воспользуйтесь нашей большой и удобной онлайн кинотекой: kinogaz. Смотрите бесллатно любимые сериалы в хорошем качестве без регистрации';;

\Yii::$app->view->registerMetaTag([
    'name' => 'description',
    'content' => $description,
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:description',
    'content' => $description,
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'pageCount',
    'content' => $lastPage,
]);

$this->registerJsFile(
    '@web/js/ajax/a_s_index.js',
    ['depends' => [SiteAsset::className()]]
);
?>

<div class="container" style="background-color: #FFFFFF; box-shadow: 0 0 1px rgba(0,0,0,0.5);">

    <div class="">&nbsp;</div>
    <nav aria-label="breadcrumb my-2">
        <ol class="breadcrumb" style="padding: 6px 8px;">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item active" aria-current="page">Сериалы</li>
        </ol>
    </nav>
    <div class="title">
        <h1 class="font-weight-bold">Сериалы смотреть онлайн</h1>
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
                        <?php for ($i = 2021; $i >= 2015; $i--):?>
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
                $rating = (!empty($rating)) ? ', ★ ' . $rating : null;
                ?>

                <div class="figure-caption">
                    <?= Html::a($tv['title'], ['serialy/view', 'id' => $tv['id'], 'title' => Inflector::slug($tv['title'])], ['style' => "margin-bottom: 0px; font-size: 1em", 'class' => 'font-weight-bold'])?>
                    <!--                    <a href="movie.html" style="margin-bottom: 0px; font-size: 1.3em" class="font-weight-bold">--><?//= $tv['title']?><!--</a>-->
                    <p class="font-weight-light" style="margin-bottom: 0; font-size: 0.9em"><a style="color: #6C757D;" href="/serialy/<?= date_format(date_create($tv['first_air_date']), 'Y')?>-goda/"><?= date_format(date_create($tv['first_air_date']), 'Y')?></a><?= $rating ?></p>
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





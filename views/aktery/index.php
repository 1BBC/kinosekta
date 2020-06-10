<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $peoples object */
/* @var $lastPage integer */


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Inflector;

$this->title = 'Онлайн фильмы на c вашеми любимыми актерами, смотрите бесплатно онлайн в хорошем качестве';

\Yii::$app->view->registerMetaTag([
    'name' => 'description',
    'content' => 'Возникают проблемы с поиском фильмов вашего любимого актера, режиссера, оператора, сценариста? Воспользуйтесь нашей большой и удобной онлайн кинотекой. Смотрите бесллатно кино в хорошем качестве без регистрации',
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:description',
    'content' => 'Возникают проблемы с поиском фильмов вашего любимого актера, режиссера, оператора, сценариста? Воспользуйтесь нашей большой и удобной онлайн кинотекой. Смотрите бесллатно кино в хорошем качестве без регистрации',
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'pageCount',
    'content' => $lastPage,
]);

$this->registerJsFile(
    '@web/js/ajax/a_aktery_index.js',
    ['depends' => [\app\assets\SiteAsset::className()]]
);
?>

<div class="container" style="background-color: #FFFFFF; box-shadow: 0 0 1px rgba(0,0,0,0.5);">

    <div class="">&nbsp;</div>
    <nav aria-label="breadcrumb my-2">
        <ol class="breadcrumb" style="padding: 6px 8px;">
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item active" aria-current="page">Актеры</li>
        </ol>
    </nav>
    <div class="title">
        <h2 class="font-weight-bold">Актеры</h2>
    </div>
    <form>
        <div class="form-row form-row center mt-4">
            <div class="col-xs-4">
                <div class="form-group">
                    <select id="s-type" class="selectpicker form-control" title="по умолчанию (рейтинг)">
<!--                                                <option value="0">по умолчанию</option>-->
                        <option value="1">по дате добавления</option>
                        <option value="2">по возрасту(&#8593)</option>
                        <option value="3">по возрасту(&#8595)</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    <select id="s-gender" class="selectpicker form-control" title="пол">
                        <!--                                                <option value="0">по умолчанию</option>-->
                        <option value="1">Женский</option>
                        <option value="2">Мужской</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="form-group">
                    <select id="s-year" class="selectpicker form-control" title="дата рождения" data-live-search="true" >
                        <!--                        <option value="0">год</option>-->
                        <?php for ($i = 2020; $i >= 1880; $i--):?>
                            <option value="<?=$i?>"><?=$i?></option>
                        <?php endfor;?>
                    </select>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="form-group btn-group">
                    <button type="button" id="del-filter" class="btn btn-outline-danger">Сбросить</button>
                    <button type="button" id="set-filter" class="btn btn-outline-success">Сортировать</button>
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
    <div class="loading d-flex justify-content-center my-2">
        <div class="loader"></div>
    </div>
    <div class="lp-message">
    </div>
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

<style type="text/css">
    .fancybox-image {
        min-width: 600px;
    }
    .card-film {
        border: 1px double white;
    }

    .card-film > img {
        /*margin: 0px;*/
    }

    .figure {
        /*margin: 0px;*/
        padding: 0px 7.5px;
    }

    .row-figure {
        padding: 0px 7.5px;
    }

    .figure-caption > a{
        color: #FC8638;
    }
</style>

<style>
    .loader {
        border: 16px solid #f3f3f3; /* Light grey */
        border-top: 16px solid #FC8638; /* Blue */
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>




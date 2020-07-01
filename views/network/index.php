<?php

/* @var $networks object */


use yii\helpers\Html;
use yii\helpers\Inflector;

$this->title = 'Каналы. Смотрите сериалы на любой вкус от любимых каналов, смотрите бесплатно онлайн в хорошем качестве';

\Yii::$app->view->registerMetaTag([
    'name' => 'description',
    'content' => 'Возникают проблемы с поиском сериала? Воспользуйтесь удобным поиском по известным каналам. Смотрите бесллатно кино в хорошем качестве без регистрации',
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:description',
    'content' => 'Возникают проблемы с поиском сериала? Воспользуйтесь удобным поиском по известным каналам. Смотрите бесллатно кино в хорошем качестве без регистрации',
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
            <li class="breadcrumb-item"><a href="/">Главная</a></li>
            <li class="breadcrumb-item active" aria-current="page">Каналы</li>
        </ol>
    </nav>
    <div class="title">
        <h1 class="font-weight-bold">Каналы смотреть онлайн</h1>
    </div>
    <hr>

    <div class="row row-figure">

        <?php foreach ($networks as $network):?>

            <div class="card-film figure col-lg-2 col-md-3 col-sm-4 col-6 my-2">

                <div class="img-figure-block">
                    <a href="<?= '/network/' . $network['id'] . '-' . Inflector::slug($network['name']) ?>">
                        <?php $poster = "/i/n/" . $network['id'] . ".png"; ?>
                        <img src="<?= $poster ?>" style="width: 100%; box-shadow: 0 0 8px rgba(0,0,0,0.5);" class="figure-img img-fluid rounded" alt="<?=$network['name']?>">
                        <div class="img-figure-overlay">
                        </div>
                    </a>


                </div>

                <div class="figure-caption">
                    <?= Html::a($network['name'], ['network/view', 'id' => $network['id'], 'name' => Inflector::slug($network['name'])], ['style' => "margin-bottom: 0px; font-size: 1em", 'class' => 'font-weight-bold'])?>
                    <!--                    <a href="movie.html" style="margin-bottom: 0px; font-size: 1.3em" class="font-weight-bold">--><?//= $network['name']?><!--</a>-->
                </div>

            </div>

        <?php endforeach;?>

    </div>
</div>





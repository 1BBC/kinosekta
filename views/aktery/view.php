<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $people array */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Inflector;

$folder = (int) ($people['id'] / 1000);
$this->title = $people['orig_name'] . ' (' . $people['name'] . ') - все фильмы и сериалы с участием смотреть бесплатно онлайн';
$description = $people['orig_name'] . ' (' . $people['name'] . ') - краткая информация, биография и кинотека. ' . stristr($people['biography'], ',', true) . '.';
$this->params['breadcrumbs'][] = $this->title;

\Yii::$app->view->registerMetaTag([
    'name' => 'description',
    'content' => $description,
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:description',
    'content' => $description,
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:image',
    'content' => "http://$_SERVER[HTTP_HOST]" . "/i/a/" . $folder . "/" . $people['id'] . ".jpg",
])

?>

<div class="container" style="background-color: #FFFFFF; box-shadow: 0 0 1px rgba(0,0,0,0.5);;">
    <div class="row" id="main">
        <div id="sidebar" class="col-lg-3" style="border-right: 1px double #E8E8E8;">
            <!--            <div class="row">-->
            <!--                <div class="bg-secondary text-white font-weight-bold" style="width: 100%; padding: 5px 15px;">-->
            <!--                    <p style="font-size: 15px; margin: 0px;">Панель навигации</p>-->
            <!--                </div>-->
            <!--            </div>-->

            <?= $this->render('/layouts/genres-list');?>
            <hr>
        </div>
        <div id="content" class="col-lg-9 col-12">
            <div class="">&nbsp;</div>
            <nav aria-label="breadcrumb my-2">
                <ol class="breadcrumb" style="padding: 6px 8px;">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="/aktery/">Актеры</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $people['orig_name']?></li>
                </ol>
            </nav>

            <div class="title">
                <h2 class="font-weight-bold"><?= $people['orig_name']?></h2>
            </div>

            <div itemscope itemtype="http://schema.org/Person" class="row mt-3">
                <div class="col-lg-3 col-sm-4">
                    <div class="row">
                        <div class="col-sm-12 col-7 mb-2">

                            <?php $poster="/i/a/" . $folder . "/" . $people['id'] . ".jpg"  ?>
                            <a href="<?= $poster ?>" title="Постер к фильму" data-fancybox="gallery">
                                <img itemprop="image" src="<?= $poster ?>"
                                     style="width: 100%;" class="rounded" alt="Смотреть фото <?= $people['orig_name']?>">
                            </a>
                        </div>
                        <div class="col-sm-12 col-5 mb-2 action-btn">
                            <button type="button" class="btn btn-outline-success btn-sm btn-block" style="overflow: hidden;"><i class="fas fa-play"></i>  Фильмы</button>
                            <button type="button" class="btn btn-outline-dark btn-sm btn-block" style="overflow: hidden;"><i class="fas fa-play"></i>  Сериалы</button>
                            <p class="text-muted mt-2" style="margin: 0">Ссылки:</p>
                            <?php
                            $eiSId = array();
                            if (!empty($people['imdb_id'])) {
                                $eiSId['imdb'] = '<a title="IMDB" rel="nofollow" target="_blank" href="https://www.imdb.com/name/nm' . sprintf("%07d", $people['imdb_id']) . '"><img src="/img/imdb.ico" width="20px" alt=""></a>';
                            }
                            if (!empty($people['tmd_id'])) {
                                $eiSId['tmd'] = '<a title="TMDB" rel="nofollow" target="_blank" href="https://www.themoviedb.org/person/' . $people['tmd_id'] . '"><img src="/img/tmdb.ico" width="20px" alt=""></a>';
                            }
                            echo implode(' ', $eiSId);
                            ?>
                        </div>
                    </div>



                </div>

                <div class="col-lg-9 col-sm-8">

                    <table class="table table-hover table-sm">
                        <tbody>
                        <tr>
                            <td class="text-muted"><small>Имя:</small></td>
                            <td>
                                <?= '<span itemprop="name">' . $people['orig_name'] . '</span>' . ' <code>|</code> ' . $people['name']?>
                            </td>
                        </tr>
                        <?php if (!empty($people['gender'])):?>
                            <tr>
                                <td class="text-muted"><small>Пол:</small></td>
                                <td itemprop="gender">
                                    <?= ($people['gender'] ==2 ) ? 'Мужчина' : 'Женщина'?>
                                </td>
                            </tr>
                        <?php endif;?>
                        <?php if (!empty($people['birthday'])):?>
                        <tr>
                            <td class="text-muted"><small>Дата рождения:</small></td>
                            <td itemprop="birthDate">
                                <?= $people['birthday']?>
                            </td>
                        </tr>
                        <?php endif;?>
                        <?php if (!empty($people['deathday'])):?>
                            <tr>
                                <td class="text-muted"><small>Дата смерти:</small></td>
                                <td itemprop="deathDate">
                                    <?= $people['deathday']?>
                                </td>
                            </tr>
                        <?php endif;?>
                        <?php if (!empty($people['place_of_birth'])):?>
                            <tr>
                                <td class="text-muted"><small>Место рождения:</small></td>
                                <td itemprop="birthPlace">
                                    <?= $people['place_of_birth']?>
                                </td>
                            </tr>
                        <?php endif;?>
                        <?php if (!empty($people['popularity'])):?>
                            <tr>
                                <td class="text-muted"><small>Популярность:</small></td>
                                <td>
                                    <?= $people['popularity'] / 10 . '%'?>
                                </td>
                            </tr>
                        <?php endif;?>
                        <tr>
                            <td class="text-muted"><small>Фильмы/Сериалы с участием:</small></td>
                            <td>
                                <?= count($people['movies']) ?><code> (на сайте)</code>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <?php if (!empty($people['biography'])):?>
                        <div class="mt-2">
                            <h3>Биография</h3>
                            <article itemprop="description"><?= $people['biography'] ?? '' ?></article>
                        </div>
                    <?php endif;?>
                </div>
            </div>

            <hr>
            <div class="mt-4">
                <h3 class="font-weight-bold my-3">Фильмы</h3>
                <div class="row row-figure">
                    <?php foreach ($people['movies'] as $mId => $mVal):?>

                        <figure class="card-film figure col-lg-3 col-md-4 col-sm-6 col-6">

                            <div class="img-figure-block">
                                <a href="<?= '/filmy/' . $mId . '-' . Inflector::slug($mVal['title']) ?>">
                                    <?php $poster = "/i/f/p/" . (int) ($mId / 1000) . "/" . $mId . ".jpg"; ?>
                                    <img src="<?= $poster ?>" style="width: 100%; box-shadow: 0 0 8px rgba(0,0,0,0.5);" class="image-figure figure-img img-fluid rounded" alt="A generic square placeholder image with rounded corners in a figure.">
                                    <div class="img-figure-overlay">
                                        <div class="img-figure-text"><i class="far fa-play-circle"></i></div>
                                    </div>
                                </a>


                            </div>

                            <figcaption class="figure-caption">
                                <?= Html::a($mVal['title'], ['filmy/view', 'id' => $mId, 'title' => Inflector::slug($mVal['title'])], ['style' => "margin-bottom: 0px; font-size: 1em", 'class' => 'font-weight-bold'])?>
                                <p class="font-weight-light" style="margin-bottom: 0px; font-size: 0.9em"><?= implode(', ', $mVal['description']) ?></p>
                            </figcaption>

                        </figure>

                    <?php endforeach;?>
                </div>

            </div>
        </div>
    </div>

    <?= $this->render('/layouts/footer');?>
</div>
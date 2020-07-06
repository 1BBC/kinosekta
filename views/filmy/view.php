<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $movie array */
/* @var $similar_movies array */

use yii\helpers\Html;
use yii\helpers\Inflector;

$movie['year'] = date_format(date_create($movie['release_date']), 'Y');
$folder = (int) ($movie['id'] / 1000);
$this->title = $movie['title'] . ' (' . $movie['year'] . '): смотреть онлайн';
$description = 'Смотрите бесплатно онлайн фильм ' . $movie['title'] . ' (' . $movie['year'] . ') в кинотеатре ' . $_SERVER['SERVER_NAME'] . '. ' . stristr($movie['overview'], '.', true) . '.';
$this->params['breadcrumbs'][] = $this->title;

\Yii::$app->view->registerMetaTag([
    'name' => 'description',
    'content' => $description,
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:locale',
    'content' => 'ru_RU',
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:type',
    'content' => 'video.movie',
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:duration',
    'content' => ($movie['runtime'] ?? 90) * 100,
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:video',
    'content' => "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:video:height',
    'content' => 430,
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:video:width',
    'content' => 600,
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:image',
    'content' => "http://$_SERVER[HTTP_HOST]" . (($movie['images'] > 1) ? '/i/f/s/' . $folder . '/' . $movie['id'] . '-' . 2 . '.jpg' : "/i/f/p/" . $folder . "/" . $movie['id'] . ".jpg"),
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:title',
    'content' => $this->title,
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:description',
    'content' => $description,
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:url',
    'content' => "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:site_name',
    'content' => $_SERVER['HTTP_HOST'],
]);

\Yii::$app->view->registerMetaTag([
    'name' => 'og:description',
    'content' => $description,
]);


?>

<div class="container" style="background-color: #FFFFFF; box-shadow: 0 0 1px rgba(0,0,0,0.5);;">
    <div id="main" class="row">
        <div class="col-lg-3" id="sidebar" style="border-right: 1px double #E8E8E8;">

            <?= $this->render('/layouts/genres-list');?>
            <hr>
            <div class="row my-4 no-gutters justify-content-center">
                <?php shuffle($similar_movies); $sm = 4?>
                <?php foreach (array_slice($similar_movies, 0, 4) as $sm):?>
                    <?php
                    $imgPath = '/i/f/s/' . $folder . '/' . $sm['id'] . '-2.jpg';
                    $smTitle = Inflector::slug($sm['title']);
                    ?>
                <div class="center">
                    <div><?= Html::a('<img src="' . $imgPath . '" style="" class="img-thumbnail" alt="'. $sm['title'] . '">', ['filmy/view', 'id' => $sm['id'], 'title' => $smTitle], ['style' => "margin-bottom: 0px; font-size: 1em", 'class' => 'font-weight-bold'])?></div>


                    <div><?= Html::a($sm['title'], ['filmy/view', 'id' => $sm['id'], 'title' => $smTitle], ['style' => "margin-bottom: 0px; font-size: 1em", 'class' => 'font-weight-bold'])?></div>
                    <br>
                    <br>
                </div>
                <?php endforeach;?>
            </div>
        </div>
        <div itemscope itemtype="http://schema.org/Movie" id="content" class="col-lg-9 col-12 ">
            <div class="">&nbsp;</div>
            <nav aria-label="breadcrumb my-2">
                <ol class="breadcrumb" style="padding: 6px 8px;">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="/filmy/">Фильмы</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $movie['title']?></li>
                </ol>
            </nav>
            <div class="title">
                <h1 itemprop="name" class="font-weight-bold"><?= $movie['title']?></h1>
            </div>

            <div class="row mt-3">
                <div class="col-lg-3 col-sm-4">
                    <div class="row">
                        <div class="col-sm-12 col-7 mb-2">

                            <?php $poster="/i/f/p/" . $folder . "/" . $movie['id'] . ".jpg"  ?>
                            <a href="<?= $poster ?>" title="Постер к фильму" data-fancybox="gallery">
                                <img itemprop="image" src="<?= $poster ?>"
                                     style="width: 100%;" class="rounded" alt="Смотреть онлайн <?= $movie['title']?>">
                            </a>
                        </div>

                        <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="col-sm-12 col-5 mb-2 action-btn">
                            <a type="button" href="#player" class="btn btn-outline-success btn-sm btn-block" style="overflow: hidden;"><i class="fas fa-play"></i>  Смотреть онлайн</a>
                            <?php if (!empty($movie['video'])): ?>
                                <a data-fancybox href="https://www.youtube.com/watch?v=<?= $movie['video'] ?>" type="button" class="btn btn-outline-danger btn-sm btn-block" style="overflow: hidden;"><i class="fab fa-youtube"></i>  Трейлер</a>
                            <?php endif;?>



                            <a type="button" href="#actors" class="btn btn-outline-dark btn-sm btn-block" style="overflow: hidden;"><i class="fa fa-user-check"></i>  Актеры</a>

                            <?php if(!empty($movie['r_kp']) || !empty($movie['r_imdb'])):?>
                                <div class="card my-2">
                                <div class="card-body text-center" style="padding: 10px">

                                    <?php if (!empty($movie['r_kp'])):?>
                                        <?php $roundRkp = round($movie['r_kp'] / 10);?>
                                        <ul class="list-inline" style="margin-bottom: 0">
                                            <li class="list-inline-item"><img width="25px" style="vertical-align: bottom" src="/img/kp.ico"></li>
                                            <li class="list-inline-item"><h4 style="margin-bottom: 0"><span itemprop="ratingValue"><?= $movie['r_kp'] / 10?></span> <small>/ <span itemprop="bestRating">10</span></small></h4></li>
                                        </ul>
                                        <span itemprop="ratingCount" style="display: none">5000</span>
                                        <?php for($i = 1; $i <= $roundRkp; $i++):?>
                                            <button type="button" style="padding: 3px 5px;" class="btn btn-warning btn-sm" aria-label="Left Align"></button>
                                        <?php endfor;?>

                                        <?php for($i = $roundRkp; $i < 10; $i++):?>
                                            <button type="button" style="padding: 3px 5px;" class="btn btn-default btn-grey btn-sm" aria-label="Left Align"></button>
                                        <?php endfor;?>

                                    <?php endif;?>

                                    <?php if (!empty($movie['r_imdb'])):?>
                                        <?php $roundRimdb = round($movie['r_imdb'] / 10);?>
                                        <ul class="list-inline" style="margin-bottom: 0">
                                            <li class="list-inline-item"><img width="25px" style="vertical-align: bottom" src="/img/imdb.ico"></li>
                                            <li class="list-inline-item"><h4 style="margin-bottom: 0"><?= $movie['r_imdb'] / 10?> <small>/ 10</small></h4></li>
                                        </ul>

                                        <?php for($i = 1; $i <= $roundRimdb; $i++):?>
                                            <button type="button" style="padding: 3px 5px;" class="btn btn-warning btn-sm" aria-label="Left Align"></button>
                                        <?php endfor;?>

                                        <?php for($i = $roundRimdb; $i < 10; $i++):?>
                                            <button type="button" style="padding: 3px 5px;" class="btn btn-default btn-grey btn-sm" aria-label="Left Align"></button>
                                        <?php endfor;?>
                                    <?php endif;?>

                                </div>
                            </div>
                            <?php endif;?>
                        </div>
                    </div>



                </div>

                <div class="col-lg-9 col-sm-8">

                    <table class="table table-hover table-sm">
                        <tbody>
                        <tr>
                            <td class="text-muted"><small>Название:</small></td>
                            <td>
                                <?= $movie['title'] . ' <code>|</code> <span itemprop="alternativeHeadline">' . $movie['orig_title'] . '</span>'?>
                            </td>

                        </tr>
                        <tr>
                            <td class="text-muted"><small>Cсылки:</small></td>
                            <td>
                                <?php
                                $ei = explode(',', $movie['external_ids']);

                                if (!empty($ei[0])) {
                                    $ei[0]= '<a title="Facebook" rel="nofollow" target="_blank" href="https://www.facebook.com/' . $ei[0] . '"><img src="/img/fb.ico" width="20px" alt=""></a>';
                                }

                                if (!empty($ei[1])) {
                                    $ei[1]= '<a title="Instagram" rel="nofollow" target="_blank" href="https://www.instagram.com/' . $ei[1] . '"><img src="/img/inst.ico" width="20px" alt=""></a>';
                                }

                                if (!empty($ei[2])) {
                                    $ei[2]= '<a title="Twitter" rel="nofollow" target="_blank" href="https://twitter.com/' . $ei[2] . '"><img src="/img/twitter.ico" width="20px" alt=""></a>';
                                }

                                $eiSId = array();
                                if (!empty($movie['kp_id'])) {
                                    $eiSId['kp'] = '<a title="Kinopoisk" rel="nofollow" target="_blank" href="https://kinopoisk.ru/film/' . $movie['kp_id'] . '"><img src="/img/kp.ico" width="20px" alt=""></a>';
                                }
                                if (!empty($movie['imdb_id'])) {
                                    $eiSId['imdb'] = '<a title="IMDB" rel="nofollow" target="_blank" href="https://www.imdb.com/title/tt' . sprintf("%07d", $movie['imdb_id']) . '"><img src="/img/imdb.ico" width="20px" alt=""></a>';
                                }
                                if (!empty($movie['tmd_id'])) {
                                    $eiSId['tmd'] = '<a title="TMDB" rel="nofollow" target="_blank" href="https://www.themoviedb.org/movie/' . $movie['tmd_id'] . '"><img src="/img/tmdb.ico" width="20px" alt=""></a>';
                                }


                                echo implode(' ', $eiSId) . ' ' . implode(' ', $ei);
                                ?>

                            </td>

                        </tr>
<!--                        <tr>-->
<!--                            <td class="text-muted"><small>Оригинальное название:</small></td>-->
<!--                            <td>--><?//= $movie['orig_title'] ?><!--</td>-->
<!--                        </tr>-->
                        <tr>
                            <td class="text-muted"><small>Год:</td>
                            <?php
                               $y =  $movie['year'];
                               $yAgo = 2020 - $y;

                               if ($yAgo == 0) {
                                   $yLabel = 'Новинка';
                               } elseif ($yAgo > 4 && $yAgo < 21) {
                                   $yLabel = $yAgo . ' лет назад';
                               }else {
                                   $lastDigit = ($yAgo%10);
                                   $yLabel = $yAgo . ' '
                                       . (($lastDigit > 1 && $lastDigit < 5) ? 'года' : (($lastDigit == 1) ? 'год' : 'лет'))
                                       . ' ' . 'назад';
                               }
                            ?>
                            <td><a title="Смотреть фильмы <?=$y?> года" href="/filmy/<?=$y?>-goda/"><span itemprop="dateCreated"><?= $y ?></span></a><code> (<?=$yLabel?>)</code></td>
                        </tr>
                        <?php if (!empty($movie['countries'])):?>
                            <tr>
                                <td class="text-muted"><small>Страна:</small></td>
                                <td>
                                    <?php
                                        $countryArr = array();
                                        foreach ($movie['countries'] as $country) {
                                            if (isset($country['url'])) {
                                                array_push($countryArr, '<a href="/filmy/' . $country['iso'] . '-' . $country['url'] . '/">' . $country['name'] . '</a>');
                                            } else {
                                                array_push($countryArr, $country['name']);
                                            }
                                        }
                                    ?>
                                    <?= implode(', ', $countryArr) ?>
                                </td>
                            </tr>
                        <?php endif;?>

                        <?php if (!empty($movie['tagline'])):?>
                            <tr>
                                <td class="text-muted"><small>Слоган:</small></td>
                                <td>
                                    <em itemprop="headline">
                                        <?= $movie['tagline'] ?>
                                    </em>
                                </td>
                            </tr>
                        <?php endif;?>

                        <?php if (!empty($movie['genres'])):?>
                        <tr>
                            <td class="text-muted"><small>Жанр:</small></td>
                            <td>
                                <?php $genresStr = ''; $lastGenre = end($movie['genres'])?>

                                <?php foreach ($movie['genres'] as $genre): ?>
                                    <?php
                                    $genresStr .= '<a title="Фильмы в жанре ' . $genre['name'] . '" href="/filmy/' . $genre['url'] . '/"><span itemprop="genre">' . $genre['name'] . '</span></a>';
                                    if ($genre != $lastGenre) {
                                        $genresStr .= ', ';
                                    }
                                    ?>
                                <?php endforeach;?>
                                <?= $genresStr ?>
                            </td>

                        </tr>
                        <?php endif;?>

                        <?php if (!empty($movie['runtime'])):?>
                            <tr>
                                <td class="text-muted"><small>Время:</small></td>
                                <td>
                                    <?php
                                        $time = $movie['runtime'];
                                        $hours = floor($time / 60);
                                        $minutes = $time % 60;

                                        echo '<span itemprop="duration">' . sprintf('%02d:%02d', $hours, $minutes) . '</span>  <code>(' . $time . ' мин.)</code>';
                                    ?>
<!--                                    --><?php //printf('%02d:%02d', $hours, $minutes) ?>
                                </td>
                            </tr>
                        <?php endif;?>

                        <?php if (!empty($movie['director'])):?>
                            <tr>
                                <td class="text-muted"><small>Режиссер:</small></td>

                                <td>
                                    <?php $peopleStr = ''; $lastPeople = end($movie['director'])?>
                                    <?php foreach ($movie['director'] as $people): ?>
                                        <?php
                                        $peopleStr .= '<span itemprop="director" itemscope itemtype="http://schema.org/Person">' .
                                            Html::a('<span itemprop="name">' . $people['orig_name'] . '</span>', ['aktery/view', 'id' => $people['id'], 'name' => $people['url_name']],
                                                ['title' => 'Фильмы с ' . $people['orig_name'], 'itemprop' => 'url' ]) . '</span>';
                                        if ($people != $lastPeople) {
                                            $peopleStr .= ', ';
                                        }
                                        ?>
                                    <?php endforeach;?>
                                    <?= $peopleStr ?>
                                </td>

                            </tr>
                        <?php endif;?>

                        <?php if (!empty($movie['producer'])):?>
                            <tr>
                                <td class="text-muted"><small>Продюсер:</small></td>

                                <td>
                                    <?php $peopleStr = ''; $lastPeople = end($movie['producer'])?>
                                    <?php foreach ($movie['producer'] as $people): ?>
                                        <?php
                                        $peopleStr .= '<span itemprop="producer" itemscope itemtype="http://schema.org/Person">' .
                                            Html::a('<span itemprop="name">' . $people['orig_name'] . '</span>', ['aktery/view', 'id' => $people['id'], 'name' => $people['url_name']],
                                                ['title' => 'Фильмы с ' . $people['orig_name'], 'itemprop' => 'url' ]) . '</span>';
                                        if ($people != $lastPeople) {
                                            $peopleStr .= ', ';
                                        }
                                        ?>
                                    <?php endforeach;?>
                                    <?= $peopleStr ?>
                                </td>

                            </tr>
                        <?php endif;?>

                        <?php if (!empty($movie['story'])):?>
                            <tr>
                                <td class="text-muted"><small>Сценарий:</small></td>

                                <td>
                                    <?php $peopleStr = ''; $lastPeople = end($movie['story'])?>
                                    <?php foreach ($movie['story'] as $people): ?>
                                        <?php
                                        $peopleStr .= Html::a($people['orig_name'], ['aktery/view', 'id' => $people['id'], 'name' => $people['url_name']], ['title' => 'Фильмы с ' . $people['orig_name']]);
                                        if ($people != $lastPeople) {
                                            $peopleStr .= ', ';
                                        }
                                        ?>
                                    <?php endforeach;?>
                                    <?= $peopleStr ?>
                                </td>

                            </tr>
                        <?php endif;?>

                        <?php if (!empty($movie['camera'])):?>
                            <tr>
                                <td class="text-muted"><small>Опертор:</small></td>

                                <td>
                                    <?php $peopleStr = ''; $lastPeople = end($movie['camera'])?>
                                    <?php foreach ($movie['camera'] as $people): ?>
                                        <?php
                                        $peopleStr .= Html::a($people['orig_name'], ['aktery/view', 'id' => $people['id'], 'name' => $people['url_name']], ['title' => 'Фильмы с ' . $people['orig_name']]);
                                        if ($people != $lastPeople) {
                                            $peopleStr .= ', ';
                                        }
                                        ?>
                                    <?php endforeach;?>
                                    <?= $peopleStr ?>
                                </td>

                            </tr>
                        <?php endif;?>

                        <?php if (!empty($movie['sound'])):?>
                            <tr>
                                <td class="text-muted"><small>Композиотр:</small></td>

                                <td>
                                    <?php $peopleStr = ''; $lastPeople = end($movie['sound'])?>
                                    <?php foreach ($movie['sound'] as $people): ?>
                                        <?php
                                        $peopleStr .= '<div itemprop="musicBy" itemscope itemtype="http://schema.org/Person">' .
                                            Html::a('<span itemprop="name">' . $people['orig_name'] . '</span>', ['aktery/view', 'id' => $people['id'], 'name' => $people['url_name']],
                                                ['title' => 'Фильмы с ' . $people['orig_name'], 'itemprop' => 'url']) . '</div>';
                                        if ($people != $lastPeople) {
                                            $peopleStr .= ', ';
                                        }
                                        ?>
                                    <?php endforeach;?>
                                    <?= $peopleStr ?>
                                </td>

                            </tr>
                        <?php endif;?>

                        <?php if (!empty($movie['actors'])):?>
                            <tr>
                                <td class="text-muted"><small>В главных ролях:</small></td>
                                <td>
                                    <?php $peopleStr = ''; $lastPeople = end($movie['actors'])?>
                                    <?php foreach ($movie['actors'] as $people): ?>
                                        <?php
                                        $peopleStr .= Html::a($people['orig_name'], ['aktery/view', 'id' => $people['id'], 'title' => $people['url_name']], ['title' => 'Фильмы с ' . $people['orig_name']]);
                                        if ($people != $lastPeople) {
                                            $peopleStr .= ', ';
                                        }
                                        ?>
                                    <?php endforeach;?>
                                    <?= $peopleStr ?>
                                </td>

                            </tr>
                        <?php endif;?>
                        </tbody>
                    </table>

                    <div class="mt-2">
                        <h2>Сюжет</h2>
                        <article itemprop="description"><?= $movie['overview'] ?? '' ?></article>
                    </div>

                </div>
            </div>
            <hr>
            <div class="mt-4" id="player">
                <div>
                    <div class="" style="">
                        <div class="row no-gutters">
                            <div class=" text-center text-white bg-dark" style="width: 100%; border-radius: 0;">
                                <div class="card-body embed-responsive embed-responsive-16by9" style="padding: 0px 0px 0px 0px; font-size: 0px">

                                    <?php
                                    if (!empty($movie['kp_id'])) {
                                        $v = 'kp_id=' . $movie['kp_id'];
                                    } elseif (!empty($movie['imdb_id'])) {
                                        $v = 'imdb_id=tt' . sprintf("%07d", $movie['imdb_id']);
                                    } else {
                                        $v = 'name_eng=' . $movie['orig_title'];
                                    }

                                    if (!empty($movie['images'])){
//                                        $frame_poster = '&poster=https://image.tmdb.org/t/p/w500/' . explode(',', $movie['images'])[0] . '.jpg';
                                    }

                                    $url = 'https://9684.videocdn.pw/7kytC46MWIdE?' . $v . ($frame_poster ?? '');

                                    ?>

                                    <iframe src="<?= $url ?>" width="640" height="480" frameborder="0" allowfullscreen></iframe>

                                </div>
<!--                                <div class="card-footer" style="padding: 0px 0px 0px 10px; background-color: #1F1F1F !important ;">-->
<!--                                    <ul class="nav nav-pills card-header-pills">-->
<!--                                        <li class="nav-item">-->
<!--                                            <a class="nav-link active" style="border-radius: 0;" href="#">Плєер #1</a>-->
<!--                                        </li>-->
<!--                                        <li class="nav-item">-->
<!--                                            <a class="nav-link" href="#" style="color: white;">Плеер #2</a>-->
<!--                                        </li>-->
<!--                                        <li class="nav-item">-->
<!--                                            <a class="nav-link" href="#" style="color: white;">Трейлер</a>-->
<!--                                        </li>-->
<!--                                    </ul>-->
<!--                                </div>-->
                            </div>
                        </div>

                    </div>
                    <hr>

                    <?php if (!empty($movie['images'])):?>
                        <div class="mt-4" id="screen">
                            <h2 class="font-weight-bold">Кадры из фильма</h2>
                            <div class="row no-gutters">
                                <?php $imgPath = '/i/f/s/' . $folder . '/' . $movie['id'] . '-'; ?>

                                <?php if (1 <= $movie['images']):?>
                                    <div class="col-8">
                                        <a data-fancybox="gallery" href="<?= $imgPath . 1 . '.jpg' ?>">
                                            <img width="100%" height="100%" src="<?= $imgPath . 1 . '.jpg' ?>">
                                        </a>
                                    </div>

                                    <div class="col-4">
                                        <?php if (2 <= $movie['images']):?>
                                            <a data-fancybox="gallery" href="<?= $imgPath . 2 . '.jpg' ?>">
                                                <div>
                                                    <img width="100%" height="50%" style="padding: 0px 0px 7px 7px;"
                                                         src="<?= $imgPath . 2 . '.jpg' ?>">
                                                </div>
                                            </a>
                                        <?endif;?>

                                        <?php if (3 == $movie['images']):?>
                                            <a data-fancybox="gallery" href="<?= $imgPath . 3 . '.jpg' ?>">
                                                <div>
                                                    <img width="100%" height="50%" style="padding: 0px 0px 7px 7px;"
                                                         src="<?= $imgPath . 3 . '.jpg' ?>">
                                                </div>
                                            </a>
                                        <?elseif (3 < $movie['images']):?>
                                            <a data-fancybox="gallery" href="<?= $imgPath . 3 . '.jpg' ?>">
                                                <div class="img-figure-block-s" width="100%" style="padding: 0px 0px 0px 7px;">
                                                    <img width="100%" height="100%" class="image-figure-s" src="<?= $imgPath . 3 . '.jpg' ?>">
                                                    <div class="img-figure-overlay-s"  style="margin: 0px 0px 0px 7px;">
                                                        <div class="img-figure-text-s">+<?= ($movie['images']-3) ?></div>
                                                    </div>
                                                </div>
                                            </a>
                                        <?endif;?>
                                        <?php if ($movie['images'] > 4):?>
                                        <div style="display: none">
                                            <?php for ($i = 4; $i <= $movie['images']; $i++): ?>
                                                <a data-fancybox="gallery" href="<?= $imgPath . $i . '.jpg' ?>"></a>
                                            <?endfor;?>
                                        </div>
                                        <?php endif;?>

                                    </div>
                                <?endif;?>
                            </div>

                        </div>
                        <hr>

                    <?php endif;?>

                    <?php if (!empty($movie['actors'])):?>
                        <div id="actors" class="mt-4">
                            <h2 class="font-weight-bold">Актерский состав</h2>

                            <div class="actors-block">

                                <div class="actors">
                                    <?php foreach ($movie['actors'] as $people): ?>

                                        <?php
                                            $folderA = (int) ($people['id'] / 1000);
                                        ?>
                                        <div itemprop="actor" itemscope itemtype="http://schema.org/Person" class="parent">
                                            <?= Html::a('<img itemprop="image" src="/i/a/' . $folderA . '/' . $people['id'] . '.jpg" class="" alt="" height="244px;">', ['aktery/view', 'id' => $people['id'], 'title' => $people['url_name']], ['itemprop' => 'url', 'style' => 'color: #FC8638;'])?>
<!--                                            <a href=""><img src="/i/a/$folderA/$people['id'].jpg" class="" alt="" height="244px;"></a>-->
                                            <div class="font-weight-bold">
                                                <small><?= $people['role'] ?></small><br>
                                                <?= Html::a('<span itemprop="name">' . $people['orig_name'] . '</span>', ['aktery/view', 'id' => $people['id'], 'title' => $people['url_name']], ['style' => 'color: #FC8638;'])?>
                                            </div>
                                        </div>
                                    <?php endforeach;?>

                                </div>
                            </div>

                        </div>
                    <?php endif;?>

                    <br>

                </div>
            </div>
        </div>
    </div>

    <?= $this->render('/layouts/footer');?>


</div>



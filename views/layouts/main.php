<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\SiteAsset;

\Yii::$app->view->registerMetaTag([
    'name' => 'author',
    'content' => 'kinogas',
]);

SiteAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-172900719-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-172900719-1');
    </script>

    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="yandex-verification" content="855511c39e435e4c" />
    <?php $this->registerCsrfMetaTags() ?>
    <link rel="shortcut icon" href="/img/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans:wght@300&display=swap" rel="stylesheet">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        body {
            font-family: 'IBM Plex Sans', sans-serif;
            font-size: 15px;
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/">
<!--            <img src="http://porno365.blog/settings/l8.png" width="90%" alt="">-->
<!--            <img height="30" src="https://img.icons8.com/pastel-glyph/2x/movie-beginning.png" alt="logo">-->
            <i class="fa fa-gas-pump"></i>
            <b>KinoGas.online</b>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item <?= (Yii::$app->controller->id == 'filmy') ? 'active' : '' ?>">
                    <a class="nav-link" href="/filmy/">Фильмы</a>
                </li>
                <li class="nav-item <?= (Yii::$app->controller->id == 'serialy') ? 'active' : '' ?>">
                    <a class="nav-link" href="/serialy/">Сериалы</a>
                </li>

                <li class="nav-item <?= (Yii::$app->controller->id == 'aktery') ? 'active' : '' ?>">
                    <a class="nav-link" href="/aktery/">Актеры</a>
                </li>

                <li class="nav-item <?= (Yii::$app->controller->id == 'network') ? 'active' : '' ?>">
                    <a class="nav-link" href="/network/">Каналы</a>
                </li>

            </ul>
            <form action="/najti" class="form-inline my-2 my-lg-0">
                <div class="input-group">
                    <input class="form-control m r-sm-2 basicAutoComplete" data-url="/najti/autocomplete" autocomplete="off" value="<?= $_GET['q'] ?? ''?>" id="inputSearch" type="search" name="q" placeholder="Название" aria-label="Search">

                    <div class="input-group-append">
                        <button class="form-control btn btn-outline-success m r-sm-2" type="submit">Поиск</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</nav>

<?= $content ?>


<?php $this->endBody() ?>
<!--<script>-->
<!--    $('.basicAutoComplete').autoComplete();-->
<!--</script>-->
</body>
</html>
<?php $this->endPage() ?>

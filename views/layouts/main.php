<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\SiteAsset;

\Yii::$app->view->registerMetaTag([
    'name' => 'author',
    'content' => 'kinogaz',
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
            <b>KinoGaz.online</b>
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
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" id="inputSearch" type="search" placeholder="Название" aria-label="Search">
<!--                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Поиск</button>-->
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                    <div class="btn-group" role="group">
                        <button id="btnGroupDrop1" type="button" class="btn btn-outline-success my-2 my-sm-0 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Поиск
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <a class="dropdown-item" id="findFilmy" href="#">по фильмам</a>
                            <a class="dropdown-item" id="findSerialy" href="#">по сериалам</a>
                            <a class="dropdown-item" id="findAktery" href="#">по актерам</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</nav>

<?= $content ?>


<?php $this->endBody() ?>
<script>
    findFilmy.addEventListener("click", handler1);
    findSerialy.addEventListener("click", handler2);
    findAktery.addEventListener("click", handler3);

    function handler1() {
        if (document.getElementById('inputSearch').value != "") {
            window.location.href = "/filmy/najti?q=" + document.getElementById('inputSearch').value;
        }
    }

    function handler2() {
        if (document.getElementById('inputSearch').value != "") {
            window.location.href = "/serialy/najti?q=" + document.getElementById('inputSearch').value;
        }
    }

    function handler3() {
        if (document.getElementById('inputSearch').value != "") {
            window.location.href = "/aktery/najti?q=" + document.getElementById('inputSearch').value;
        }
    }
</script>
</body>
</html>
<?php $this->endPage() ?>

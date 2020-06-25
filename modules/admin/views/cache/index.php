<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\web\View;

$this->title = 'Cache';
$this->params['breadcrumbs'][] = ['label' => 'Cache', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php if( Yii::$app->session->hasFlash('flash') ): ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php echo Yii::$app->session->getFlash('flash'); ?>
    </div>
<?php endif;?>
<div class="row">

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">main</div>
            <div class="panel-body">
                <p class="panel-text">Главная страница<code>60*60*24</code></p>
                <?= Html::a('Del cache', ['main'], ['class' => 'btn btn-danger']) ?>
                <?php if (Yii::$app->cache->exists('main')): ?>
                    <button type="button" class="modalClick btn btn-success" data-toggle="modal">Посмотреть</button>
                    <div style="display: none;" class="cache"><?= print_r(Yii::$app->cache->get('main'))?></div>
                <?php endif;?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">similar_movies</div>
            <div class="panel-body">
                <p class="panel-text">Очистить похожие фильмы<code>60*60*24</code></p>
                <?= Html::a('Del cache', ['similar-movies'], ['class' => 'btn btn-danger']) ?>
                <?php if (Yii::$app->cache->exists('similar_movies')): ?>
                    <button type="button" class="modalClick btn btn-success" data-toggle="modal">Посмотреть</button>
                    <div style="display: none;" class="cache"><?= print_r(Yii::$app->cache->get('similar_movies'))?></div>
                <?php endif;?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">similar_tvs</div>
            <div class="panel-body">
                <p class="panel-text">Очистить похожие сериалы<code>60*60*24</code></p>
                <?= Html::a('Del cache', ['similar-tvs'], ['class' => 'btn btn-danger']) ?>
                <?php if (Yii::$app->cache->exists('similar_tvs')): ?>
                    <button type="button" class="modalClick btn btn-success" data-toggle="modal">Посмотреть</button>
                    <div style="display: none;" class="cache"><?= print_r(Yii::$app->cache->get('similar_tvs'))?></div>
                <?php endif;?>
            </div>
        </div>
    </div>


    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">networks</div>
            <div class="panel-body">
                <p class="panel-text">Страница телесетей<code>60*60</code></p>
                <?= Html::a('Del cache', ['networks'], ['class' => 'btn btn-danger']) ?>
                <?php if (Yii::$app->cache->exists('networks')): ?>
                    <button type="button" class="modalClick btn btn-success" data-toggle="modal">Посмотреть</button>
                    <div style="display: none;" class="cache"><?= print_r(Yii::$app->cache->get('networks'))?></div>
                <?php endif;?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-warning">
            <div class="panel-heading">movies</div>
            <div class="panel-body">
                <p class="panel-text">Очистить все фильмы<code>60*60</code></p>
                <?= Html::a('Del cache', ['movies'], ['class' => 'btn btn-danger']) ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-warning">
            <div class="panel-heading">tvs</div>
            <div class="panel-body">
                <p class="panel-text">Очистить все сериалы<code>60*60</code></p>
                <?= Html::a('Del cache', ['tvs'], ['class' => 'btn btn-danger']) ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading">sitemap movie</div>
            <div class="panel-body">
                <p class="panel-text">Sitemap фильмов<code>60*60*6</code></p>
                <?= Html::a('Del cache', ['sitemap-movie'], ['class' => 'btn btn-danger']) ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading">sitemap tv</div>
            <div class="panel-body">
                <p class="panel-text">Sitemap сериалов<code>60*60*6</code></p>
                <?= Html::a('Del cache', ['sitemap-movie'], ['class' => 'btn btn-danger']) ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading">sitemap actors</div>
            <div class="panel-body">
                <p class="panel-text">Sitemap актеров<code>60*60*6</code></p>
                <?= Html::a('Del cache', ['sitemap-movie'], ['class' => 'btn btn-danger']) ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-danger">
            <div class="panel-heading">All</div>
            <div class="panel-body">
                <p class="panel-text">Очистить весь кэш</p>
                <?= Html::a('Del cache', ['all'], ['class' => 'btn btn-danger']) ?>
            </div>
        </div>
    </div>

</div>
<!--<a href="#myModal" class="modalClick btn btn-primary" data-toggle="modal">Открыть модальное окно</a>-->
<div id="myModalBox" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Заголовок модального окна -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Просмотр кэша</h4>
            </div>
            <!-- Основное содержимое модального окна -->
            <pre id="myModalText" class="modal-body">
                ERROR!
            </pre>
        </div>
    </div>
</div>


<?php $this->registerJs(
    "$(document).ready(function() {
        $( \".modalClick\" ).on( \"click\", function() {
            // console.log( $( this ).text() );
            $(\"#myModalText\").text($( this ).parent().find(\".cache\").text());
            $(\"#myModalBox\").modal('show');
        });

    });",
    View::POS_READY,
    'my-button-handler'
); ?>



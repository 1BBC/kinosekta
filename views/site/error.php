    <?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
    <div class="container" style="background-color: #FFFFFF; box-shadow: 0 0 1px rgba(0,0,0,0.5);">
        <div class="">&nbsp;</div>

        <div class="site-error">

            <h1 class="font-weight-bold"><?= Html::encode($this->title) ?></h1>
            <div class="row justify-content-center">
                <img src="/img/error.gif" width="400px" alt="404">
            </div>

            <h2><small>Увы, но эта страница где-то затерялась в галактике Интернета</small></h2>
        
            <div class="alert alert-danger">
                <?= nl2br(Html::encode($message)) ?>
            </div>

        </div>

        <?= $this->render('/layouts/footer');?>
    </div>


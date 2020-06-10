<?php

use yii\web\View;
/** @var string $json_movies */

?>
    <div class="frontend-default-index">
        <h1>Фильмы</h1>

        <p>
<!--            --><?//= $json_movies?><!--<br>-->
            <code><?= \yii\helpers\Html::encode($json_movies) ?></code>
        </p>
    </div>

    <script>
        json = '<?php echo $json_movies; ?>';
        console.log(JSON.parse(json));
    </script>
<?php
//$this->registerJs(
//    'alert(text)',
//    View::POS_READY,
//    'movies-list'
//);

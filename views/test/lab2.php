<?php
/* @var $this yii\web\View */
?>
<h1>lab2</h1>
<p>Змiна частоти кадрiв</p>

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="<?=Yii::$app->request->csrfParam; ?>" value="<?=Yii::$app->request->getCsrfToken(); ?>" />
    <p>Вiдео: <input required name="userfile" accept=".webm, .mp4" type="file" /></p>
    <p>Кiлькiсть FPS: <input required name="fps" type="number" min="1" max="30"/></p>
    <input type="submit" value="Convert"/>
</form>

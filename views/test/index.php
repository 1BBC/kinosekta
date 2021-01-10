<?php
/* @var $this yii\web\View */
?>
<h1>lab1</h1>
<p>Конвертер відео до GIF-файлу і навпаки.</p>

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="<?=Yii::$app->request->csrfParam; ?>" value="<?=Yii::$app->request->getCsrfToken(); ?>" />
    <input required name="userfile" accept=".webm, .mp4, .gif" type="file" />
    <input type="submit" value="Convert"/>
</form>

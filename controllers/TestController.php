<?php

namespace app\controllers;

class TestController extends \yii\web\Controller
{
    public function actionLab1()
    {
        $this->layout = false;

        if (empty($_POST) || empty($_FILES['userfile'])) {
            return $this->render('index');
        }

        $fileType = str_replace(array('image/', 'video/'), '', $_FILES['userfile']['type']);

        $nameFile = time();
        $uploadFile = 'i/' . $nameFile . '.' . $fileType;

        if (!move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile)) {
            exit("Можлива атака за допомогою файлового завантаження\n");
        }

        if ($fileType == 'gif') {
            $finalFile = "i/{$nameFile}.mp4";
            $command = "ffmpeg -i {$uploadFile} -f mp4 -pix_fmt yuv420p {$finalFile}";
        } else {
            $finalFile = "i/{$nameFile}.gif";
            $command = "ffmpeg -i {$uploadFile} -f gif {$finalFile}";
        }

        exec($command, $output, $retval);

        if ($retval != 0) {
            exit("Помилка загрузки: {$retval}\n");
        }

        $this->redirect("/{$finalFile}");
//        header('Location: /' . $finalFile);
    }
}

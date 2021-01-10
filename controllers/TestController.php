<?php

namespace app\controllers;

class TestController extends \yii\web\Controller
{
    public function actionLab1()
    {
        $this->layout = false;

        //Перевірка даних з форми
        if (empty($_POST) || empty($_FILES['userfile'])) {
            return $this->render('index');
        }

        //Тип файлу
        $fileType = str_replace(array('image/', 'video/'), '', $_FILES['userfile']['type']);

        //Назва файлу
        $nameFile = time();
        $uploadFile = 'i/' . $nameFile . '.' . $fileType;

        //Загрузка файлу на сервер
        if (!move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile)) {
            exit("Можлива атака за допомогою файлового завантаження\n");
        }

        //Визначення типу конвертацii
        if ($fileType == 'gif') {
            //Назва кiнцевого файлу
            $finalFile = "i/{$nameFile}.mp4";
            //Формування команди для конвертування gif to video
            $command = "ffmpeg -i {$uploadFile} -f mp4 -pix_fmt yuv420p {$finalFile}";
        } else {
            $finalFile = "i/{$nameFile}.gif";
            //Формування команди для конвертування video to gif
            $command = "ffmpeg -i {$uploadFile} -f gif {$finalFile}";
        }

        //Виконання команди
        exec($command, $output, $retval);

        //Перевырка результату
        if ($retval != 0) {
            exit("Помилка загрузки: {$retval}\n");
        }

        //Редiрект на кiнцевий файл
        $this->redirect("/{$finalFile}");
//        header('Location: /' . $finalFile);
    }

    public function actionLab2()
    {
        $this->layout = false;

        //Перевірка даних з форми
        if (empty($_POST) || empty($_FILES['userfile']) || empty($_POST['fps'])) {
            return $this->render('lab2');
        }

        //Тип файлу
        $fileType = str_replace('video/', '', $_FILES['userfile']['type']);

        //Назва файлу
        $nameFile = time();
        $uploadFile = 'i/' . $nameFile . '.' . $fileType;

        //Загрузка файлу на сервер
        if (!move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile)) {
            exit("Можлива атака за допомогою файлового завантаження\n");
        }

        //Назва кiнцевого файлу
        $finalFile = "i/{$nameFile}final.{$fileType}";

        //Формування команди для змiни fps
        $command = "ffmpeg -i {$uploadFile} -r {$_POST['fps']} {$finalFile}";

        //Виконання команди
        exec($command, $output, $retval);

        //Перевырка результату
        if ($retval != 0) {
            exit("Помилка ffmpeg: {$retval}\n");
        }

        //Редiрект на кiнцевий файл
        $this->redirect("/{$finalFile}");
//        header('Location: /' . $finalFile);
    }
}

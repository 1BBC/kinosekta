<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;

/**
 * Parser controller for the `admin` module
 */
class ParserController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $params = array();

        if(Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();

            exec("php ../yii main-parser/{$params['type']} {$params['ids']} ", $result);

            Yii::$app->session->setFlash('success', "parser launched");
        }

        return $this->render('index', ['result' => $result ?? array(), 'params' => $params]);
    }
}

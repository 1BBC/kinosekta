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
        if(Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
            $parserName = ($params['media'] == 'movie') ? '' : 'tv-';

            exec('php ../yii ' . $parserName . 'parser/index ' . $params['ids'] . ' -' . $params['type'] . '=1 -l=1', $result);

            Yii::$app->session->setFlash('success', "parser launched");
        }

        return $this->render('index', ['result' => $result ?? array()]);
    }
}

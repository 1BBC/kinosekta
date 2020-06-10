<?php

namespace app\modules\admin;

use Yii;
use yii\helpers\Url;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $layout = 'main';
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'denyCallback' => function($rules, $action)
                {
                    return Yii::$app->getResponse()->redirect(Url::to(['/site/login']), 302);
                },
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function($rules, $action)
                        {
                            return (!Yii::$app->user->isGuest);
                        }
                    ]
                ],
            ],
        ];
    }
}

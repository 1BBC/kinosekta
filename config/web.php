<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'name' => 'КиноСекта',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'site/index',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@parser' => '@runtime/parser',
    ],
    'components' => [
        'assetManager' => [
            'appendTimestamp' => true,
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '2L-Hdh-3oUTKKqn557z4cRWa3s8m67mQ',
            'enableCsrfValidation'=>true,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
//        'mailer' => [
//            'class' => 'yii\swiftmailer\Mailer',
//            // send all mails to a file by default. You have to set
//            // 'useFileTransport' to false and configure a transport
//            // for the mailer to send real emails.
//            'useFileTransport' => true,
//        ],
//        'log' => [
//            'traceLevel' => YII_DEBUG ? 3 : 0,
//            'targets' => [
//                [
//                    'class' => 'yii\log\FileTarget',
//                    'levels' => ['error', 'warning'],
//                ],
//            ],
//        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
//            'suffix' => '.html',
            'enableStrictParsing' => true,
            'rules' => [
                '<action:|about|login>' => 'site/<action>',
                'admin/<controller>/<action>' => 'admin/<controller>/<action>',

                'najti' => 'najti/index',
                'najti/<action>' => 'najti/<action>',
                //'<action>/najti' => 'najti/<action>',
                'filmy/<action:|page|page-count|country|page-country|genre|page-genre|year|page-year>' => 'filmy/<action>',
                'serialy/<action:|page|page-count|genre|page-genre|year|page-year>' => 'serialy/<action>',
                'aktery/<action:|page|page-count>' => 'aktery/<action>',
                'network/<action:|page>' => 'network/<action>',

                'filmy<suffix:/>' => 'filmy/index/',
                'filmy/<id:\d+>-<title:[\w,-]+>' => 'filmy/view/',
                'filmy/<id:\d+>' =>'filmy/view/',


                'serialy<suffix:/>' => 'serialy/index/',
                'serialy/<id:\d+>-<title:[\w,-]+>' => 'serialy/view/',
                'serialy/<id:\d+>' => 'serialy/view/',

                'network<suffix:/>' => 'network/index/',
                'network/<id:\d+>-<name:[\w,-]+>' => 'network/view/',
                'network/<id:\d+>' => 'network/view/',

                'aktery<suffix:/>' => 'aktery/index/',
                'aktery/<id:\d+>-<title:[\w,-]+>' => 'aktery/view/',
                'aktery/<id:\d+>' => 'aktery/view/',

                'filmy/<genre:\w+><suffix:/>' => 'filmy/genre/',
                'filmy/<year:\d+>-goda<suffix:/>' => 'filmy/year/',

                'serialy/<genre:\w+><suffix:/>' => 'serialy/genre/',
                'serialy/<year:\d+>-goda<suffix:/>' => 'serialy/year/',

                'filmy/<iso:\w+>-<title:[\w,-]+><suffix:/>' => 'filmy/country/',
                'filmy/<iso:\w+><suffix:/>' => 'filmy/country/',

                'sitemap.xml' => 'sitemap/sitemap',
                '/uploads/sitemap1.xml' => 'sitemap/sitemap1',
                '/uploads/sitemap2.xml' => 'sitemap/sitemap2',
                '/uploads/sitemap3.xml' => 'sitemap/sitemap3',
                '/uploads/sitemap4.xml' => 'sitemap/sitemap4',
                '/uploads/sitemap5.xml' => 'sitemap/sitemap5',
                '/robots.txt' => 'sitemap/robots',
            ],
        ],
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
        'allowedIPs' => ['*'],
    ];
}

return $config;

<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SiteAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/bootstrap.min.css',
        'css/bootstrap-select.min.css',
        'css/jquery.fancybox.min.css',
        'fontawesome/css/all.min.css',
        'slick/slick.css',
        'slick/slick-theme.css',
        'css/main.css',
    ];

    public $js = [
        'js/jquery.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js',
        'js/bootstrap.min.js',
        'js/bootstrap-select.min.js',
        'js/jquery.fancybox.min.js',
        'js/readmore.min.js',
        'slick/slick.min.js',
        'js/main.js',
    ];

    public $depends = [

    ];
}
<?php
namespace app\assets;

use yii\web\AssetBundle;

class MainAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/style.min.css',
        'css/errorStyle.css'
    ];
    public $js = [
        'js/vendor.js',
        'js/main.js'
    ];
    public $depends = [
    ];
}
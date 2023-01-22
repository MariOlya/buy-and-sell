<?php

use yii\authclient\clients\VKontakte;
use yii\authclient\Collection;
use wapmorgan\yii2inflection\Inflection;
use yii\queue\amqp_interop\Queue;
use yii\queue\LogBehavior;
use yii\sphinx\Connection;
use yii\debug\panels\DbPanel;
use yii\debug\Module;
use yii\log\FileTarget;
use yii\caching\FileCache;
use omarinina\infrastructure\modules\Bootstrap;
use omarinina\infrastructure\constants\KeysConstants;
use yii\symfonymailer\Mailer;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'emailQueue',
        Bootstrap::class
    ],
    'homeUrl' => ['/'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'sphinx' => [
            'class' => Connection::class,
            'dsn' => 'mysql:host=sphinxsearch;port=9306;',
            'username' => 'root',
            'password' => 'root_password',
        ],
        'authClientCollection' => [
            'class' => Collection::class,
            'clients' => [
                'vkontakte' => [
                    'class' => VKontakte::class,
                    'clientId' => KeysConstants::VK_CLIENT_ID,
                    'clientSecret' => KeysConstants::VK_CLIENT_KEY,
                ],
            ],
        ],
        'inflection' => [
            'class' => Inflection::class
        ],
        'emailQueue' => [
            'class' => Queue::class,
            'as log' => LogBehavior::class,
            'ttr' => 3 * 60,
            'attempts' => 1,
            'queueName' => 'email-queue',
            'exchangeName' => 'email-queue',
            'driver' => Queue::ENQUEUE_AMQP_BUNNY,
            'dsn' => "amqp://root:root@rabbit:5672",
            'connectionTimeout' => 60,
            'heartbeat' => 60,
            'vhost' => '/',
            'routingKey' => ''
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Y8_s8-6VrhNYFHLcQHigCwvITCMZUteq',
        ],
        'cache' => [
            'class' => FileCache::class,
        ],
        'user' => [
            'identityClass' => \omarinina\domain\models\Users::class,
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => false,
            'transport' => [
                'dsn' => 'smtp://mailhog:1025'
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'offers/<id:\d+>' => 'offers/view',
                'offers/category/<categoryId:\d+>/page/<page:\d+>' => 'offers/category',
                'offers/category/<categoryId:\d+>' => 'offers/category',
                'offers/edit/<id:\d+>' => 'offers/edit',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => Module::class,
        'panels' => [
            'db' => [
                'class' => DbPanel::class,
                'defaultOrder' => [
                    'seq' => SORT_ASC
                ],
                'defaultFilter' => [
                    'type' => 'SELECT'
                ]
            ],
            'profiling' => \yii\debug\panels\ProfilingPanel::class,
        ],
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}

return $config;

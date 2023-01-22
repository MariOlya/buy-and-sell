<?php

use yii\queue\amqp_interop\Queue;
use yii\queue\LogBehavior;
use yii\sphinx\Connection;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'emailQueue'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'sphinx' => [
            'class' => Connection::class,
            'dsn' => 'mysql:host=127.0.0.1;port=9306;dbname=buyAndSell',
            'username' => 'root',
            'password' => 'root_password',
        ],
        'emailQueue' => [
            'class' => Queue::class,
            'as log' => LogBehavior::class,
            'ttr' => 3 * 60,
            'attempts' => 1,
            'queueName' => 'email-queue',
            'exchangeName' => 'email-queue',
            'driver' => Queue::ENQUEUE_AMQP_LIB,
            'dsn' => "amqp://root:root@rabbit:5672",
            'connectionTimeout' => 60,
            'heartbeat' => 60,
            'vhost' => '/',
            'routingKey' => ''
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\faker\FixtureController',
            'templatePath' => '@app/fixtures/templates',
            'fixtureDataPath' => '@app/fixtures/data',
            'namespace' => 'app\fixtures',
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
    // configuration adjustments for 'dev' environment
    // requires version `2.1.21` of yii2-debug module
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;

<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'barber',
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
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'pattern' => '/',
                    'route' => '/auth/login',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/',
                    'route' => '/auth/logout',
                    'verb' => 'DELETE',
                ],
                [
                    'pattern' => '/register',
                    'route' => '/site/register',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/addvisit',
                    'route' => '/site/addvisit',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/visits/<barber_id:\d+>',
                    'route' => '/site/getvisits',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/uservisits',
                    'route' => '/site/getuservisit',
                    'verb' => 'GET',
                ],
                [
                    'pattern' => '/changeuserdata',
                    'route' => '/site/changeuserdata',
                    'verb' => 'PUT',
                ],
                [
                    'pattern' => '/deletevisit',
                    'route' => '/site/deletevisit',
                    'verb' => 'DELETE',
                ],
                [
                    'pattern' => '/banuser/<phone:\d+>',
                    'route' => '/site/banuser',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/unbanuser/<phone:\d+>',
                    'route' => '/site/unbanuser',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/dayoff',
                    'route' => '/site/dayoff',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/bannedusers',
                    'route' => '/site/banedusers',
                    'verb' => 'GET',
                ],
                [
                    'pattern' => '/userdata',
                    'route' => '/site/userdata',
                    'verb' => 'GET',
                ],
                [
                    'pattern' => '/closeacc',
                    'route' => '/site/closeacc',
                    'verb' => 'DELETE',
                ],
                [
                    'pattern' => '/getprices',
                    'route' => '/site/getprices',
                    'verb' => 'GET',
                ],
                [
                    'pattern' => '/changeprices',
                    'route' => '/site/changeprices',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/verify',
                    'route' => '/site/verificateacc',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/sendsms',
                    'route' => '/site/sendsms',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/addtype',
                    'route' => '/site/addtype',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/changepassword',
                    'route' => '/site/changepass',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/changenotification',
                    'route' => '/site/changenot',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/send',
                    'route' => '/notification/sendnoti',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/deletetype',
                    'route' => '/notification/deletetype',
                    'verb' => 'DELETE',
                ],
                [
                    'pattern' => '/smsforpassword',
                    'route' => '/notification/smsforpassword',
                    'verb' => 'PUT',
                ],
            ],
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
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '172.18.0.1'],
    ];
}

return $config;

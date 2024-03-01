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
                    'verb' => 'PUT',
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
                    'pattern' => '/gettypes',
                    'route' => '/site/gettypes',
                    'verb' => 'GET',
                ],
                [
                    'pattern' => '/changetype',
                    'route' => '/site/changetype',
                    'verb' => 'PUT',
                ],
                [
                    'pattern' => '/verify',
                    'route' => '/site/verificateacc',
                    'verb' => 'PUT',
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
                    'verb' => 'PUT',
                ],
                [
                    'pattern' => '/changenotification',
                    'route' => '/site/changenot',
                    'verb' => 'PUT',
                ],
                [
                    'pattern' => '/send',
                    'route' => '/notification/sendnoti',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/deletetype',
                    'route' => '/site/deletetype',
                    'verb' => 'DELETE',
                ],
                [
                    'pattern' => '/smsforpassword',
                    'route' => '/site/smsforpassword',
                    'verb' => 'PUT',
                ],
                [
                    'pattern' => '/dayon',
                    'route' => '/site/dayon',
                    'verb' => 'DELETE',
                ],
                [
                    'pattern' => '/updatevisit',
                    'route' => '/site/updatevisit',
                    'verb' => 'PUT',
                ],
                [
                    'pattern' => '/pushnoti',
                    'route' => '/push/pushnoti',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/getbarbers',
                    'route' => '/site/getbarbers',
                    'verb' => 'GET',
                ],
                [
                    'pattern' => '/sendmes',
                    'route' => '/site/sendmessage',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/getchat',
                    'route' => '/site/getchat',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/getchats',
                    'route' => '/site/getchats',
                    'verb' => 'GET',
                ],
                [
                    'pattern' => '/createuser',
                    'route' => '/site/createuser',
                    'verb' => 'POST',
                ],
                [
                    'pattern' => '/test',
                    'route' => '/mqtt/index',
                    'verb' => 'POST',
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

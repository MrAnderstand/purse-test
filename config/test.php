<?php

$files = [
    'aliases'    => 'aliases.php',
    'db'         => 'test_db.php',
    'urlManager' => 'urlManager.php',
    'modules'    => 'modules.php',
    'params'     => 'params.php',
];
foreach ($files as $varName => $path) {
    $path = __DIR__ . DIRECTORY_SEPARATOR . $path;
    if (file_exists($path)) {
        ${$varName} = require $path;
    } else {
        exit('Config file ' . $path . ' doesn\'t exists');
    }
}

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => $aliases,
    'language' => 'ru-RU',
    'components' => [
        'db' => $db,
        'mailer' => [
            'useFileTransport' => true,
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => $urlManager,
        'user' => [
            'identityClass' => 'app\models\User',
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],
    ],
    'modules' => $modules,
    'params' => $params,
];

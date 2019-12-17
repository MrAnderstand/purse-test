<?php

return [
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
        [
            'class' => 'yii\rest\UrlRule',
            'pluralize' => false,
            'controller' => ['api/balance'],
            'patterns' => [
                'GET'  => 'get',
                'POST' => 'change',
            ],
        ],
    ],
];

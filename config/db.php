<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=localhost;dbname=purse',
    'username' => 'user_purse',
    'password' => 'pursepass',
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql' => [
            'class' => 'yii\db\pgsql\Schema',
            'defaultSchema' => 'public',
        ],
    ],
    'on afterOpen' => function ($event) {
        $event->sender->createCommand("SET TIME ZONE '+00:00';")->execute();
    },
];

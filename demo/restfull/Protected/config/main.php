<?php

return array(

    'basePath' => dirname(__FILE__) . '/..',
    'components' => array(
        'router' => array(
                'class'     => 'Z\Router\ZRouter',
                'urlFormat' => 'path'
            ),
        'annotation' => array(
                'class' => 'Z\Core\Annotation\AnnotationManager',
                'separator' => '/',
            ),
        'db' => array(
            'class' => 'Z\Core\Orm\ZDbConnection',
            'dsn' => 'mysql:dbname=tbl;host=127.0.0.1',
            'userName' => 'root',
            'password' => '123123',
            'charset' => 'utf8',
        ),
    ),
);


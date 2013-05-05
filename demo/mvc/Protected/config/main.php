<?php

return array(

    'basePath' => dirname(__FILE__) . '/..',
    'components' => array(
        'router' => array(
                'class'     => 'Z\Router\ZWebRouter',
                'urlFormat' => 'path',
                'rules'     => array(
                    '/site/:id/:name' => 'site/index',
                ),
            ),
        'annotation' => array(
                'class' => 'Z\Core\Annotation\AnnotationManager',
                'separator' => '/',
            ),
    ),
);


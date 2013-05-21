<?php

return array(

    'basePath' => dirname(__FILE__) . '/..',
    'components' => array(
        'router' => array(
                'class'     => 'Z\Router\ZRestfulRouter',
                'urlFormat' => 'path'
            ),
        'annotation' => array(
                'class' => 'Z\Core\Annotation\AnnotationManager',
                'separator' => '/',
            ),
    ),
);


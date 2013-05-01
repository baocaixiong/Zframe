<?php

return array(

    'basePath' => dirname(__FILE__) . '/..',
    'components' => array(
        'router' => array(
                'class'     => 'Z\Router\ZWebRouter',
                'urlFormat' => 'get',
                'rules'     => array(
                    '/site/:id/:name' => 'site/index',
                ),
            ),
    ),
);


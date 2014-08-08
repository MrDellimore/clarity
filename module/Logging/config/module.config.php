<?php

/*
 * This is module.config.php
 * It hosts configuration parameters as well as routing.
 * */

return array(
    'router'   =>  array(
        'routes' =>  array(
            'logging'   =>  array(
                'type'  =>  'literal',
                'options'   =>  array(
                    'route' =>  '/sku-history',
                    'defaults'  =>  array(
                        'controller'    =>  'Logging\Controller\Logging',
                        'action'    =>  'index',
                    )
                )
            ),
            'listUsers'   =>  array(
                'type'  =>  'literal',
                'options'   =>  array(
                    'route' =>  '/sku-history/user',
                    'defaults'  =>  array(
                        'controller'    =>  'Logging\Controller\Logging',
                        'action'    =>  'listUsers',
                    )
                )
            ),

        )
    ),
    'view_manager'  =>  array(
        'template_path_stack'   =>  array(
            'logging'   =>  __DIR__ .'/../view',
        ),
    ),
    'controllers'   =>  array(
        'invokables'    =>  array(
            'Logging\Controller\Logging'    =>  'Logging\Controller\LoggingController',
        )
    )
);
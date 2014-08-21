<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/17/14
 * Time: 3:57 PM
 */
return array(
    'router'    =>  array(
        'routes'    =>  array(
            'apis'   =>  array(
                'type'  =>  'Zend\Mvc\Router\Http\Literal',
                'options'   =>  array(
                    'route' =>  '/api-feeds',
                    'defaults'  =>  array(
                        'controller'    =>  'Api\Magento\Controller\Magento',
                        'action'        =>  'magento',
                    ),
                ),
            ),
            'api-magento-items'   =>  array(
                'type'  =>  'Zend\Mvc\Router\Http\Literal',
                'options'   =>  array(
                    'route' =>  '/api-feeds/magento/items',
                    'defaults'  =>  array(
                        'controller'    =>  'Api\Magento\Controller\Magento',
                        'action'        =>  'soapItem',
                    ),
                ),
            ),
            'api-magento-images'   =>  array(
                'type'  =>  'Zend\Mvc\Router\Http\Literal',
                'options'   =>  array(
                    'route' =>  '/api-feeds/magento/images',
                    'defaults'  =>  array(
                        'controller'    =>  'Api\Magento\Controller\Magento',
                        'action'        =>  'soapImages',
                    ),
                ),
            ),
            'api-magento-new-items'   =>  array(
                'type'  =>  'Zend\Mvc\Router\Http\Literal',
                'options'   =>  array(
                    'route' =>  '/api-feeds/magento/new-items',
                    'defaults'  =>  array(
                        'controller'    =>  'Api\Magento\Controller\Magento',
                        'action'        =>  'soapNewItems',
                    ),
                ),
            ),
        ),
    ),
    'controllers'   =>  array(
        'invokables'    =>  array(
            'Api\Magento\Controller\Magento'    =>  'Api\Magento\Controller\MagentoController'
        ),
    ),
    'view_manager'  =>  array(
//        'template_map'  =>  array(
//            'profile/index/index'   => __DIR__ . '/../view/profile/profile/profile.phtml',
//        ),
        'template_path_stack'   =>  array(
            __DIR__ .'/../view',
        ),
    )
);
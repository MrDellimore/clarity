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
            'apis-update-items'   =>  array(
                'type'  =>  'Zend\Mvc\Router\Http\Literal',
                'options'   =>  array(
                    'route' =>  '/api-feeds/update-items',
                    'defaults'  =>  array(
                        'controller'    =>  'Api\Magento\Controller\Magento',
                        'action'        =>  'updateItems',
                    ),
                ),
            ),
            'api-magento-categories'   =>  array(
                'type'  =>  'Zend\Mvc\Router\Http\Literal',
                'options'   =>  array(
                    'route' =>  '/api-feeds/update-categories',
                    'defaults'  =>  array(
                        'controller'    =>  'Api\Magento\Controller\Magento',
                        'action'        =>  'updateCategories',
                    ),
                ),
            ),

            'api-magento-related'   =>  array(
                'type'  =>  'Zend\Mvc\Router\Http\Literal',
                'options'   =>  array(
                    'route' =>  '/api-feeds/update-related',
                    'defaults'  =>  array(
                        'controller'    =>  'Api\Magento\Controller\Magento',
                        'action'        =>  'updateRelated',
                    ),
                ),
            ),
            'api-images'   =>  array(
                'type'  =>  'Zend\Mvc\Router\Http\Literal',
                'options'   =>  array(
                    'route' =>  '/api-feeds/new-images',
                    'defaults'  =>  array(
                        'controller'    =>  'Api\Magento\Controller\Magento',
                        'action'        =>  'newImages',
                    ),
                ),
            ),
            'api-new-products'   =>  array(
                'type'  =>  'Zend\Mvc\Router\Http\Literal',
                'options'   =>  array(
                    'route' =>  '/api-feeds/new-products',
                    'defaults'  =>  array(
                        'controller'    =>  'Api\Magento\Controller\Magento',
                        'action'        =>  'newProducts',
                    ),
                ),
            ),
            'update-count'   =>  array(
                'type'  =>  'Zend\Mvc\Router\Http\Literal',
                'options'   =>  array(
                    'route' =>  '/api-feeds/mage-update-count',
                    'defaults'  =>  array(
                        'controller'    =>  'Api\Magento\Controller\Magento',
                        'action'        =>  'kpiUpdateCount',
                    ),
                ),
            ),
            'image-count'   =>  array(
                'type'  =>  'Zend\Mvc\Router\Http\Literal',
                'options'   =>  array(
                    'route' =>  '/api-feeds/mage-new-image-count',
                    'defaults'  =>  array(
                        'controller'    =>  'Api\Magento\Controller\Magento',
                        'action'        =>  'kpiImageCount',
                    ),
                ),
            ),
            'new-product-count'   =>  array(
                'type'  =>  'Zend\Mvc\Router\Http\Literal',
                'options'   =>  array(
                    'route' =>  '/api-feeds/mage-new-product-count',
                    'defaults'  =>  array(
                        'controller'    =>  'Api\Magento\Controller\Magento',
                        'action'        =>  'kpiNewProductCount',
                    ),
                ),
            ),
//            'category-count'   =>  array(
//                'type'  =>  'Zend\Mvc\Router\Http\Literal',
//                'options'   =>  array(
//                    'route' =>  '/api-feeds/mage-category-count',
//                    'defaults'  =>  array(
//                        'controller'    =>  'Api\Magento\Controller\Magento',
//                        'action'        =>  'kpiCategoryCount',
//                    ),
//                ),
//            ),
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
                    'route' =>  '/api-feeds/magento/new-images',
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
    'console' => array(
        'router' => array(
            'routes' => array(
                'soap-create-products' => array(
                    'options' => array(
                        'route' => 'createProduct',
//                        'route' => 'get happen [--verbose|-v] <doname>',
                        'defaults' => array(
//                            '__NAMESPACE__' => 'Api\Magento\Controller',
                            'controller' => 'Api\Magento\Controller\ConsoleMagento',
                            'action' => 'soapCreateProducts'
                        ),
                    ),
                ),
            )
        )
    ),
    'controllers'   =>  array(
        'invokables'    =>  array(
            'Api\Magento\Controller\Magento'    =>  'Api\Magento\Controller\MagentoController',
            'Api\Magento\Controller\ConsoleMagento'    =>  'Api\Magento\Controller\ConsoleMagentoController',
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
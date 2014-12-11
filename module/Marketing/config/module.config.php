<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 12/11/14
 * Time: 3:58 PM
 */
return array(
    'router' => array(
        'routes' => array(
            'create-deal' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/create-deals',
                    'defaults' => array(
                        'controller' => 'Marketing\Deals\Controller\Deals',
                        'action'     => 'index',
                    ),
                ),
            ),
            'deals' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/deals',
                    'defaults' => array(
                        'controller' => 'Marketing\Deals\Controller\Deals',
                        'action'     => 'searchDeals',
                    ),
                ),
            ),
            'deals-update' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/update-deals[/][/:sku]',
                    'constraints'   =>  array(
                        'sku'    =>  '[0-9a-zA-Z].+',
                    ),
                    'defaults' => array(
                        'controller' => 'Marketing\Deals\Controller\Deals',
                        'action'     => 'displayFormDeals',
                    ),
                ),
            ),

            'deals-submit-update' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/deals/update',
                    'defaults' => array(
                        'controller' => 'Marketing\Deals\Controller\Deals',
                        'action'     => 'dealsUpdate',
                    ),
                ),
            ),

            'deals-delete' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/deals/delete',
                    'defaults' => array(
                        'controller' => 'Marketing\Deals\Controller\Deals',
                        'action'     => 'deleteDeal',
                    ),
                ),
            ),
            'deals-submit' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/deals/submit',
                    'defaults' => array(
                        'controller' => 'Marketing\Deals\Controller\Deals',
                        'action'     => 'dealsSubmit',
                    ),
                ),
            ),
        ),
    ),

    'controllers' => array('invokables' =>
        array(
            'Marketing\Deals\Controller\Deals'   => 'Marketing\Deals\Controller\DealsController',
        )
    ),

    'view_manager' => array(
        'template_path_stack' => array(__DIR__ . '/../view'),
    ),
);

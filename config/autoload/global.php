<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Config\SessionConfig;

return array(
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Dashboard',
                'route' => 'home',
                'resource' => 'home',
            ),
            array(
                'label' => 'Manage Content',
                'route' => 'home',
                'pages' => array(
                    array(
                        'label' => 'Search',
                        'route' => 'search',
                    ),
                    array(
                        'label' => 'Attribute Management',
                        'route' => 'manageattributes',
                    ),
                    array(
                        'label' => 'Category Management',
                        'route' => 'managecategories',
                    ),
                    array(
                        'label' => 'Website Assignment',
                        'route' => 'webassignment',
                    ),
                ),
            ),
/*
            array(
                'label' => 'Help Desk',
                'route' => 'home',
                'pages' =>  array(
                    array(
                        'label' =>  'Tickets',
                        'route' =>  'tickets',
                    ),
                    array(
                        'label' =>  'Kanban Board',
                        'route' =>  'kanban',
                    ),

                ),
            ),
*/
            array(
                'label' => 'API',
                'route' => 'home',
                'pages' =>  array(
                    array(
                        'label' =>  'Magento',
                        'route' =>  'apis',
                    ),
                ),
            ),
            array(
                'label' => 'History',
                'route' => 'home',
                'pages' =>  array(
                    array(
                        'label' =>  'SKU History',
                        'route' =>  'logging'
                    ),
                    array(
                        'label' =>  'Magento Soap History',
                        'route' =>  'mage-soap-logging',
                    ),
                ),
            ),
              
             
        )
    ),
    'event_listener_construct' =>  array(
        'logger'  =>  array(
            'extra'    =>  array(
                'entity_id' => 'entity_id',
                'sku'   =>  'sku',
                'oldvalue'  =>  'oldvalue',
                'newvalue'  =>  'newvalue',
                'datechanged'   =>  'datechanged',
                'changedby' =>  'changedby',
                'property'  =>  'property',
            ),
        ),
        'mage_logs'  =>  array(
            'extra'    =>  array(
                'sku' => 'sku',
                'resource'   =>  'resource',
                'speed'  =>  'speed',
                'pushedby'  =>  'pushedby',
                'datepushed'   =>  'datepushed',
                'status'        =>  'status',
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'Zend\Db\Adapter\Adapter' =>'Zend\Db\Adapter\AdapterServiceFactory',
            'sessionService'    =>  function (ServiceLocatorInterface $serviceLocator){
                    $sessionNames  =  array(
                        'intranet',
                        'login',
                        'dirty_skus',
                    );
                    var_dump('hahaha'); exit();
                    foreach($sessionNames as $sessions){
                        $sessionContainer = new \Zend\Session\Container($sessions);

                        $authTimeout = 1;
                        $sessionConfig = new SessionConfig();
                        $sessionConfig->setOptions(array(
                            'use_cookies' => true,
                            'cookie_httponly' => true,
                            'gc_maxlifetime' => $authTimeout,
                            'cookie_lifetime' => $authTimeout
                        ));
                        $sessionManager = $sessionContainer->getDefaultManager();
                        $sessionManager->setConfig($sessionConfig);
                        $sessionContainer->setDefaultManager($sessionManager);

                        $sessionManager = $sessionContainer->getManager();
                        $sessionManager->setConfig($sessionConfig);

                        $sessionService = new SessionService();
                        $sessionService->setSessionContainer($sessionContainer);
                    }
                    return $sessionService;

                }
        ),
    )
);

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

return array(
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Dashboard',
                'route' => 'home',
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
                        'label' => 'Website Assignment',
                        'route' => 'webassignment',
                    ),
                    array(
                        'label' => 'ApiCron Management',
                        'route' => 'apimanage',
                    ),
                ),
            ),
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
                    foreach($sessionNames as $sessions){
                        $sessionContainer = new \Zend\Session\Container($sessions);
                        $sessionService = new SessionService();
                        $sessionService->setSessionContainer($sessionContainer);
                    }
                    return $sessionService;

                }
        ),
    )
);

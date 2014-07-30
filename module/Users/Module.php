<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/14/14
 * Time: 12:01 AM
 */

namespace Users;


class Module {


    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

} 
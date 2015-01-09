<?php
namespace Authenticate;

use Zend\Mvc\MvcEvent;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

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

    public function onBootstrap(MvcEvent $e) {
        $app = $e->getApplication();

        $sm = $app->getServiceManager();
        $acl = $sm->get('acl');

        $acl->initAcl($e);

        $em = $app->getEventManager();
        $em->attach('route', array($acl, 'requireAcl'));
    }

}

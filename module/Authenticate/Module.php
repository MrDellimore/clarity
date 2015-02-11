<?php
namespace Authenticate;

use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

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

        //redirect if not logged in
        $em->attach('route', function(MvcEvent $e){
            $loginSession= new Container('login');
            $userLogin = $loginSession->sessionDataforUser;
            $loginAction = [
                "controller" =>  "Authenticate\\Controller\\Authenticate",
                "action" =>  "login"
            ];
            if(empty($userLogin) && $loginAction != $e->getRouteMatch()->getParams()){
                $e->getRouteMatch()
                    ->setParam('controller', 'Authenticate\Controller\Authenticate')
                    ->setParam('action', 'index');
            }
        });
    }

}

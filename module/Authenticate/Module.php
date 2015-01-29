<?php
namespace Authenticate;


use Authenticate\Model\AuthDBSession;
use Authenticate\Model\AuthDBSessionOptions;
use Users\Entity\User;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;


class Module {
    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }
    public function getAutoloaderConfig() {
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

        $em->attach('route', function(MvcEvent $e){
            $loginSession= new Container('login');
            $userLogin = $loginSession->sessionDataforUser;
            if(! empty($userLogin)){
                $authTable = $e->getApplication()->getServiceManager()->get('Authenticate\Model\AuthTable');
                $res = $authTable->updateRouteForUser($userLogin['userid'], $e->getRouteMatch());
            }
        });



        $sessionOptions = new AuthDBSessionOptions();
        $sessionOptions->setDataColumn('data')
            ->setIdColumn('id')
            ->setModifiedColumn('modified')
            ->setLifetimeColumn('lifetime')
            ->setNameColumn('name');

        $application = $e->getApplication();
        $serviceManager = $application->getServiceManager();
        $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
        $sessionTable = new Sql($dbAdapter, 'session');
        $session = new AuthDBSession($sessionTable, $sessionOptions);
        $config = $serviceManager->get('Configuration');
        $sessionConfig = new SessionConfig();
        $sessionManager = new SessionManager($sessionConfig);
        $sessionManager->setSaveHandler($session);

        Container::setDefaultManager($sessionManager);

        $eventManager = $application->getEventManager();
        $eventManager->attach('finish', array($session, 'saveRouteData'));
    }
}

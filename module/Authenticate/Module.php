<?php
namespace Authenticate;


use Authenticate\Model\AuthDBSession;
use Authenticate\Model\AuthDBSessionOptions;
use Zend\Db\Sql\Sql;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\Container;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;


class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $event){

        $sessionOptions = new AuthDBSessionOptions();
        $sessionOptions->setDataColumn('data')
            ->setIdColumn('id')
            ->setModifiedColumn('modified')
            ->setLifetimeColumn('lifetime')
            ->setNameColumn('name');

        $application    = $event->getApplication();
        $serviceManager = $application->getServiceManager();
        $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
        $sessionTableGateway = new Sql($dbAdapter, 'session');
        $sessionGateway = new AuthDBSession($sessionTableGateway, $sessionOptions);
        $config = $serviceManager->get('Configuration');
        $sessionConfig = new SessionConfig();
        // $sessionConfig->setOptions($config['session']);
        $sessionManager = new SessionManager($sessionConfig);
        $sessionManager->setSaveHandler($sessionGateway);

        Container::setDefaultManager($sessionManager);
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
}

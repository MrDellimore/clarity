<?php
namespace Search;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\Event;
//use Zend\EventManager\StaticEventManager;
use Zend\Log\Logger;
use Zend\Log\Writer\Db;


class Module
{

    use EventManagerAwareTrait;

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

    public function onBootstrap(MvcEvent $event)
    {

        $eventManager       = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        $sharedEventManager->attach('*', 'log', function($e){
            $writer = new Db($e->getParam('dbAdapter'), 'logger', $e->getParam('fields'));
            $logger = new Logger();
            $logger->addWriter($writer);
            $logger->info(null,$e->getParam('extra'));
        },100);
        $sharedEventManager->attach('*', 'constructLog', function($e){
                $this->getEventManager()->trigger('log', null, $e->getParam('makeFields'));
        },100);
    }


    public function getServiceConfig() {
        return array(
            'invokables'    =>  array(
                'EventListeners' =>  'Listeners\Event\Listener',
            ),
            'factories' => array(
                'Search\Model\SearchTable' => function($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $table = new Model\SearchTable($dbAdapter);
                        return $table;
                    },
            ),
        );
    }
}


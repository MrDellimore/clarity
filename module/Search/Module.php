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
            $fields = $this->getConfig()['event_listener_construct']['logger'];
            $writer = new Db($e->getParam('dbAdapter'), 'logger', $fields);//$e->getParam('fields')
            $logger = new Logger();
            $logger->addWriter($writer);
            $logger->info(null,$e->getParam('extra'));
        },100);
        $sharedEventManager->attach('*', 'constructLog', function($e){
            $fields = $this->getConfig()['event_listener_construct']['logger'];
//            $makeFields = $e->getParam('makeFields');
            $eventWritables = array('dbAdapter'=>$e->getParam('makeFields')['dbAdapter'], 'fields'=>$fields, 'extra'=>$e->getParam('makeFields')['extra'] );
            $this->getEventManager()->trigger('log', null, $eventWritables);
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


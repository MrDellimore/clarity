<?php
namespace Content;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\Event;
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
        $sharedEventManager->attach('*', 'sku_log', function($e) use ($event){
//            $fields = $this->getConfig()['event_listener_construct']['logger'];
            $fields = $event->getApplication()->getServiceManager()->get('Config')['event_listener_construct']['logger'];
            $writer = new Db($e->getParam('dbAdapter'), 'logger', $fields);//$e->getParam('fields')
            $logger = new Logger();
            $logger->addWriter($writer);
            $logger->info(null,$e->getParam('extra'));
        },100);
        $sharedEventManager->attach('*', 'construct_sku_log', function($e) use ($event){
            $fields = $event->getApplication()->getServiceManager()->get('Config')['event_listener_construct']['logger'];
//            $fields = $this->getConfig()['event_listener_construct']['logger'];
//                $makeFields = $e->getParam('makeFields');
                $eventWritables = array('dbAdapter'=>$e->getParam('makeFields')['dbAdapter'], 'fields'=>$fields, 'extra'=>$e->getParam('makeFields')['extra'] );
                $this->getEventManager()->trigger('sku_log', null, $eventWritables);
//            }

        },100);
    }


    public function getServiceConfig() {
        return array(
            'invokables'    =>  array(
                'EventListeners' =>  'Listeners\Event\Listener',
            ),
            'factories' => array(
                'Content\ContentForm\Model\SearchTable' => function($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $table = new \Content\ContentForm\Model\SearchTable($dbAdapter);
                        return $table;
                    },
            ),



        );
    }
}


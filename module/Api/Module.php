<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/17/14
 * Time: 3:57 PM
 */

namespace Api;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Log\Logger;
use Zend\Log\Writer\Db;

class Module {

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
        $sharedEventManager->attach('*', 'mage_log', function($e) use ($event){
//            $fields = $this->getConfig()['event_listener_construct']['logger'];
            $fields = $event->getApplication()->getServiceManager()->get('Config')['event_listener_construct']['mage_logs'];
            $writer = new Db($e->getParam('dbAdapter'), 'mage_logs', $fields);//$e->getParam('fields')
            $logger = new Logger();
            $logger->addWriter($writer);
            $logger->info(null,$e->getParam('extra'));
        },100);
        $sharedEventManager->attach('*', 'construct_mage_log', function($e) use ($event){
            $fields = $event->getApplication()->getServiceManager()->get('Config')['event_listener_construct']['mage_logs'];
//            $fields = $this->getConfig()['event_listener_construct']['logger'];
//                $makeFields = $e->getParam('makeFields');
            $eventWritables = array('dbAdapter'=>$e->getParam('makeFields')['dbAdapter'], 'fields'=>$fields, 'extra'=>$e->getParam('makeFields')['extra'] );
            $this->getEventManager()->trigger('mage_log', null, $eventWritables);
//            }

        },100);
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'Api\Magento\Model\MagentoTable' => function($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $table = new Magento\Model\MagentoTable($dbAdapter);
                        return $table;
                    },
            ),
        );
    }
} 
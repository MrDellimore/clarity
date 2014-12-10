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
    /**
     * Trait
     */
    use EventManagerAwareTrait;

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
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

    /**
     * This method does the same thing as the method in the Content module. Refer to that for more information.
     * It is connected to insertIntoMageLog in the AbstractSoap class.
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        $eventManager       = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        $sharedEventManager->attach('*', 'mage_log', function($e) use ($event){
            $fields = $event->getApplication()->getServiceManager()->get('Config')['event_listener_construct']['mage_logs'];
            $writer = new Db($e->getParam('dbAdapter'), 'mage_logs', $fields);
            $logger = new Logger();
            $logger->addWriter($writer);
            $logger->info(null,$e->getParam('extra'));
        },100);
        $sharedEventManager->attach('*', 'construct_mage_log', function($e) use ($event){
            $fields = $event->getApplication()->getServiceManager()->get('Config')['event_listener_construct']['mage_logs'];
            $eventWritables = array('dbAdapter'=>$e->getParam('makeFields')['dbAdapter'], 'fields'=>$fields, 'extra'=>$e->getParam('makeFields')['extra'] );
            $this->getEventManager()->trigger('mage_log', null, $eventWritables);
        },100);
    }

    /**
     * @return array
     */
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
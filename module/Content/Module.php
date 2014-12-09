<?php
namespace Content;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Log\Logger;
use Zend\Log\Writer\Db;

class Module
{

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
     * This method is used for the event listener. When any kind of change happens to any particular field in a sku.
     * This method will log that change in the table. Take a look at insertLogging method in the ProductsTable Model in ContentForm
     * insertLogging method will trigger the construct_sku_log, which then triggers the sku_log
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        $eventManager       = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
//        This will log the changes that a user makes to a sku.
        $sharedEventManager->attach('*', 'sku_log', function($e) use ($event){
            $fields = $event->getApplication()->getServiceManager()->get('Config')['event_listener_construct']['logger'];
            $writer = new Db($e->getParam('dbAdapter'), 'logger', $fields);//$e->getParam('fields')
            $logger = new Logger();
            $logger->addWriter($writer);
            $logger->info(null,$e->getParam('extra'));
        },100);
//        This will create the array for the event listener.
        $sharedEventManager->attach('*', 'construct_sku_log', function($e) use ($event){
            $fields = $event->getApplication()->getServiceManager()->get('Config')['event_listener_construct']['logger'];
            $eventWritables = array('dbAdapter'=>$e->getParam('makeFields')['dbAdapter'], 'fields'=>$fields, 'extra'=>$e->getParam('makeFields')['extra'] );
            $this->getEventManager()->trigger('sku_log', null, $eventWritables);
        },100);
    }

    /**
     * @return array
     */
    public function getServiceConfig()
   {
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


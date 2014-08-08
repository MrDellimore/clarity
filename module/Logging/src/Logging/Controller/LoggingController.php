<?php

namespace Logging\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LoggingController extends AbstractActionController
{

    protected $loggingTable;

    public function indexAction()
    {
        $logs = $this->getLoggingTable();
        $request = $this->getRequest();
        if($request->isPost()) {
            $logsInfo = $request->getPost();
            $draw = $logsInfo['draw'];
            $sku = $logsInfo['search']['value'];
//            $limit = $loadAccessories['length'];
//            if($limit == '-1'){
//                $limit = 100;
//            }
            $loadedLogs = $logs->lookupLoggingInfo($sku);
            $result = json_encode(
                array(
                    'draw'  =>  (int)$draw,
                    'data'  =>  $loadedLogs,
                    'recordsTotal'  =>  1000,
//                    'recordsFiltered'   =>  $limit,
                )
            );
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $response;

        }
    }
    public function getLoggingTable()
    {
        if (!$this->loggingTable) {
            $sm = $this->getServiceLocator();
            $this->loggingTable = $sm->get('Logging\Model\LoggingTable');
        }
        return $this->loggingTable;
    }
}
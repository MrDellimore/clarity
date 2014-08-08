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
            $sku = (!is_null($logsInfo['search']['value']))? $logsInfo['search']['value']: null;
//            $filterDateRange = (!is_null($logsInfo['filterDateRange'])) ? $logsInfo['filterDateRange'] : null;
//            $dateRange = explode('to',$filterDateRange);
//            $fromDate = trim((string)$dateRange[0]);
//            $toDate = trim((string)$dateRange[1]);

            $searchParams = array('sku'=>$sku);//,'from'=>$fromDate,'to'=>$toDate);

//            $dateRange = array('from'=>$fromDate,'to'=>$toDate);

//            var_dump($filterDateRange);
//            $limit = $loadAccessories['length'];
//            if($limit == '-1'){
//                $limit = 100;
//            }
            $loadedLogs = $logs->lookupLoggingInfo($searchParams);
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
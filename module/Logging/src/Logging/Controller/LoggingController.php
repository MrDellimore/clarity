<?php

namespace Logging\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;


class LoggingController extends AbstractActionController
{

    protected $loggingTable;

    /**
     * Description: this action on load will load all rows from the logger table into the data table in the view.
    */
    public function skuLogAction(){

        $logs = $this->getLoggingTable();
        $request = $this->getRequest();
        if($request->isPost()) {
            $logsInfo = $request->getPost();
            $draw = $logsInfo['draw'];
            $sku = (!is_null($logsInfo['search']['value']))? $logsInfo['search']['value']: null;
            $limit = $logsInfo['length'];

//            $Qty = $logsInfo['more_old'];
//            $qty = $logsInfo['moreold'];
//            $filterDateRange = (!is_null($logsInfo['filterDateRange'])) ? $logsInfo['filterDateRange'] : null;
//            $dateRange = explode('to',$filterDateRange);
//            $fromDate = trim((string)$dateRange[0]);
//            $toDate = trim((string)$dateRange[1]);
//            $fromDate = date('Y-m-d h:i:s', strtotime($fromDate) );
//            $toDate = date('Y-m-d h:i:s', strtotime($toDate) );


            $searchParams = array('sku'=>$sku);//,'from'=>$fromDate,'to'=>$toDate);
//            $dateRange = array('from'=>$fromDate,'to'=>$toDate);

            if($limit == '-1'){
                $limit = 100;
            }
            $loadedLogs = $logs->lookupLoggingInfo($searchParams, $limit);
            $result = json_encode(
                array(
                    'draw'  =>  (int)$draw,
                    'data'  =>  $loadedLogs,
                    'recordsTotal'  =>  1000,
                    'recordsFiltered'   =>  $limit,
                )
            );
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $response;
        }
    }

    public function mageSoapLogAction(){

        $logs = $this->getLoggingTable();
        $request = $this->getRequest();
        if($request->isPost()) {
            $logsInfo = $request->getPost();
            $draw = $logsInfo['draw'];
            $sku = (!is_null($logsInfo['search']['value']))? $logsInfo['search']['value']: null;
            $limit = $logsInfo['length'];


//            $filterDateRange = (!is_null($logsInfo['filterDateRange'])) ? $logsInfo['filterDateRange'] : null;
//            $dateRange = explode('to',$filterDateRange);
//            $fromDate = trim((string)$dateRange[0]);
//            $toDate = trim((string)$dateRange[1]);
//            $fromDate = date('Y-m-d h:i:s', strtotime($fromDate) );
//            $toDate = date('Y-m-d h:i:s', strtotime($toDate) );


            $searchParams = array('sku'=>$sku);//,'from'=>$fromDate,'to'=>$toDate);
//            $dateRange = array('from'=>$fromDate,'to'=>$toDate);

            if($limit == '-1'){
                $limit = 100;
            }
            $loadedLogs = $logs->fetchMageLogs($searchParams, $limit);
            $result = json_encode(
                array(
                    'draw'  =>  (int)$draw,
                    'data'  =>  $loadedLogs,
                    'recordsTotal'  =>  1000,
                    'recordsFiltered'   =>  $limit,
                )
            );
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $response;
        }
    }

    public function revertAction(){
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        $userID = $userLogin['userid'];
        $revert = $this->getLoggingTable();
        $request = $this->getRequest();
        if($request->isPost()) {
            $logsInfo = $request->getPost();
            $searchParams = array(
                'old'=>$logsInfo['old'],
                'new'=>$logsInfo['new'],
                'eid'=>$logsInfo['eid'],
                'pk'=>$logsInfo['pk'],
                'property'=>$logsInfo['property'],
                'user'=>$userID,
                'sku'=>$logsInfo['sku'],
            );
            $revert->undo($searchParams);
            $this->redirect()->toRoute('logging');
        }
        $this->redirect()->toRoute('logging');
    }

    public function listUsersAction()
    {
        $users = $this->getLoggingTable();
        $userList = $users->listUser();
        $result = json_encode($userList);
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);
        return $response;
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
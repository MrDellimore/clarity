<?php

namespace Logging\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

class LoggingController extends AbstractActionController
{

    /**
     * @var LoggingTable $loggingTable object
     */
    protected $loggingTable;

    /**
     * This action on load will load all rows from the logger table into the data table in the view.
     * @return \Zend\Http\Response|\Zend\Stdlib\ResponseInterface
     */
    public function skuLogAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $logs = $this->getLoggingTable();
        $request = $this->getRequest();
        if($request->isPost()) {
            $logsInfo = $request->getPost();
            $draw = $logsInfo['draw'];
            $sku = (!is_null($logsInfo['search']['value']))? $logsInfo['search']['value']: null;
            $limit = $logsInfo['length'];

//TODO the below will be for filtering through a date range.
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

    /**
     * This action will display all the of api call that were made to Mage
     * @return \Zend\Http\Response|\Zend\Stdlib\ResponseInterface
     */
    public function mageSoapLogAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $logs = $this->getLoggingTable();
        $request = $this->getRequest();
        if($request->isPost()) {
            $logsInfo = $request->getPost();
            $draw = $logsInfo['draw'];
            $sku = (!is_null($logsInfo['search']['value']))? $logsInfo['search']['value']: null;
            $limit = $logsInfo['length'];

//TODO the below will be for filtering through a date range.
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

    /**
     * This action will revert a change in Sku History tab. When the user clicks on revert link it will swap the old value
     * and the new value and log that change as well as update that particular attribute.
     * @return \Zend\Http\Response
     */
    public function revertAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
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

    /**
     * This method populates a select2 dropdown with a list of registered users.
     * @return \Zend\Stdlib\ResponseInterface
     */
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

    /**
     * @return LoggingTable|object
     */
    public function getLoggingTable()
    {
        if (!$this->loggingTable) {
            $sm = $this->getServiceLocator();
            $this->loggingTable = $sm->get('Logging\Model\LoggingTable');
        }
        return $this->loggingTable;
    }
}
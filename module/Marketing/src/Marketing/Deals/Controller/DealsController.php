<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 12/11/14
 * Time: 10:35 AM
 */

namespace Marketing\Deals\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\View\Model\ViewModel;
use Marketing\Deals\Entity\Deals;
use Zend\Session\Container;

class DealsController  extends AbstractActionController
{
    /**
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function indexAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        return new ViewModel();
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\ResponseInterface
     */
    public function dealsSubmitAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $request = $this->getRequest();
        if($request->isPost()) {
            $dealsData = $request->getPost();
            $hydrator = new ClassMethods();
            $deals = new Deals();
            $dates = explode('to',$dealsData['startendDate']);
            $dealsData = ['sku'=>$dealsData['sku'] ,'specialPrice'=>$dealsData['specialPrice'],'inventory'=>$dealsData['inventory'],
                          'startDate'=>trim($dates[0]),'endDate'=>trim($dates[1]), 'maxQty'=>$dealsData['maxQty'], 'usStandard'=>$dealsData['usStandard']];
            $hydrator->hydrate($dealsData, $deals);
            $dealsTable = $this->getServiceLocator()->get('Marketing\Deals\Model\DealsTable');
            $result = $dealsTable->persist($deals);

            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $response;
        }
    }

    public function displayFormDealsAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
//        $request = $this->getRequest();
        $sku = $this->params()->fromRoute('sku');
        $dealsTable = $this->getServiceLocator()->get('Marketing\Deals\Model\DealsTable');
        if( $sku ){
            $deals = $dealsTable->fetchDeals($sku);
            return new ViewModel(['deals'=>$deals]);
        }
        else {
            return $this->redirect()->toRoute('deals');
        }
    }

    public function dealsUpdateAction()
    {
        $result = '';
        $request = $this->getRequest();
        if($request->isPost()) {
            $deals = new Deals();
            $dealsTable = $this->getServiceLocator()->get('Marketing\Deals\Model\DealsTable');
            $dealsData = $request->getPost();
            $dates = explode('to',$dealsData['startendDate']);
            $dealsData = ['sku'=>$dealsData['sku'] ,'specialPrice'=>$dealsData['specialPrice'],'inventory'=>$dealsData['inventory'],
                'startDate'=>trim($dates[0]),'endDate'=>trim($dates[1]), 'maxQty'=>$dealsData['maxQty'], 'usStandard'=>$dealsData['usStandard']];
            $hydrator = new ClassMethods();
            $hydrator->hydrate($dealsData,$deals);
            $result = $dealsTable->updateDeals($deals);
            if($result == ''){
                $result = 'No changes to sku made.';
            }
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $response;
        }
    }

    public function searchDealsAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $dealsTable = $this->getServiceLocator()->get('Marketing\Deals\Model\DealsTable');
        $request = $this->getRequest();
        if($request->isPost()) {
            $searchDeals = $request->getPost();
            $draw = $searchDeals['draw'];
            $sku = $searchDeals['search']['value'];
            $limit = $searchDeals['length'];
            if($limit == '-1'){
                $limit = 50;
            }
            $deals = $dealsTable->searchDeals($sku, $limit);
            $result = json_encode(
                array(
                    'draw' => $draw,
                    'recordsTotal' => 1000,
                    'recordsFiltered' => $limit,
                    //results
                    'data' => $deals));
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $response;
        }
    }

    public function deleteDealAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $dealsTable = $this->getServiceLocator()->get('Marketing\Deals\Model\DealsTable');
        $request = $this->getRequest();
        if($request->isPost()) {
            $deleteDeal = $request->getPost();
            $sku = $deleteDeal['sku'];
            $result = $dealsTable->deleteDeal($sku);
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $response;
        }
    }
} 
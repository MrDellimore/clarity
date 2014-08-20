<?php
/**
 * Created by PhpStorm.
 * User: adellimore
 * Date: 8/20/14
 * Time: 12:47 PM
 */


namespace Search\WebAssignment\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class IndexController extends AbstractActionController{

    protected $webassignTable;

    public function indexAction(){
        //check if logged in
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }

        $web = $this->getWebTable()->accessWeb();

        $viewResult = new ViewModel();
        return $viewResult;

    }

    public function getWebTable(){
        if (!$this->webassignTable) {
            $sm = $this->getServiceLocator();
            $this->webassignTable = $sm->get('Search\WebAssignment\Model\WebAssignTable');
        }
        return $this->webassignTable;
    }
}
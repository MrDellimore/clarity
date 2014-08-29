<?php


namespace Content\ManageAttributes\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;


class AttributesController extends AbstractActionController{

    public function indexAction(){
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }

        $lookupTable = $this->getServiceLocator()->get('Content\ContentForm\Model\AttributesTable')->fetchLookupTable();
        $viewResult = new ViewModel(array('lookup'=>$lookupTable));
        return $viewResult;
    }



}
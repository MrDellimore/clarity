<?php


namespace Content\ManageAttributes\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;


class AttributesController extends AbstractActionController{

    /**
     * @return array|\Zend\Http\Response
     */
    public function indexAction(){
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
    }



}
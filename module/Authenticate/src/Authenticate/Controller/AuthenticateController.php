<?php

namespace Authenticate\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;



class AuthenticateController extends AbstractActionController{

    public function LoginAction(){
            
            //if post run auth method
            $result = new ViewModel();
            $result ->setTerminal(true);
            return $result;
        
    }
    

    public function RegisterAction(){
            
        if(true){
            //if post request to this then do ya thing
        }
        
    }



}


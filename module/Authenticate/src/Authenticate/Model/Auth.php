<?php
namespace Authenticate\Model;

//use Authenticate\Model\AuthTable;
//use Zend\Mvc\Controller\AbstractActionController;
//use Zend\Mvc\Controller\AbstractController;
use Zend\Db\Sql\Select;
/*
 * Model for user updates and creation
 */

class Auth{
    
    protected $authTable;

    protected $userId;
    
    //register User
    //return boolean if user was created
    public function createUser($authTable, $userData){

//        $authTable = $this->getAuthTable();
//        $authTable = new AuthTable();
        return $authTable->saveUser($userData);
    }
    
    
    //validate credentials
    //return boolean
    public function validateUser($credentials){
        $authTable = $this->getAuthTable();
        return $authTable->checkUser($credentials);   
    }
    

    
//    protected function getAuthTable(){
//        if (!$this->authTable) {
//            $sm = $this->getServiceLocator();
//            $this->authTable = $sm->get('Authenticate\Model\AuthTable');
//
//        }
//        return $this->authTable;
//    }
}
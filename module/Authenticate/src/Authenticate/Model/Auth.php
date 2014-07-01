<?php
namespace Users\Model;
/*
 * Model for user updates and creation
 */

class Auth{
    
    protected $authTable;
    
    //register User
    //return boolean if user was created
    public function createUser($userData){
        $authTable = $this ->getAuthTable();
        return $authTable->saveUser($userData);
    }
    
    
    //validate credentials
    //return boolean
    public function validateUser($credentials){
        $authTable = $this->getAuthTable();
        return $authTable->checkUser($credentials);   
    }
    
    
    
    
    protected function getAuthTable(){
        if (!$this->authTable) {
            $sm = $this->getServiceLocator();
            $this->authTable = $sm->get('Authenticate\Model\AuthTable');
            
        }
        return $this->authTable;
    }
}
<?php
namespace Authenticate\Model;

/**
 * This class is just an intermediary Between Controller the the AuthTable Model Class.
 * */
class Auth{
    
    protected $authTable;

    protected $userId;

    /**
     * Method was created by Andrew so I decided to keep it.
     * @param AuthTable $authTable object
     * @param $userData array
     * @return boolean
     * */
    public function createUser($authTable, $userData)
    {
        return $authTable->saveUser($userData);
    }
    
    
    //validate credentials
    //return boolean
    public function validateUser($credentials)
    {
        $authTable = $this->getAuthTable();
        return $authTable->checkUser($credentials);   
    }
}
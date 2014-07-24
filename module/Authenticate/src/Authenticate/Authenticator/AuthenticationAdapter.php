<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/10/14
 * Time: 1:46 PM
 */

namespace Authenticate\Authenticator;


use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Adapter\DbTable;

class AuthenticationAdapter implements AdapterInterface {

    protected $username;

    protected $password;

    protected $authAdapter;

    public function setAdapter(DbTable $authAdapter){
        $this->authAdapter = $authAdapter;
    }

    /**
     * Sets username and password for authentication
     *
     * @return void
     */
//    public function __construct($username, $password)
//    {
//        $this->username = $username;
//        $this->password = $password;
//    }

    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface
     *               If authentication cannot be performed
     */
    public function authenticate()
    {
        var_dump($this->authAdapter->authenticate());
    }
} 
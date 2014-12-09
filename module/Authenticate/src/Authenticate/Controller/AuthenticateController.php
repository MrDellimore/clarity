<?php

/**
 * This class is for user registration and login in.
 * */

namespace Authenticate\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Crypt\Password\Bcrypt;
use Authenticate\Entity\User;
use Authenticate\Model\Auth;
use Zend\Authentication\Adapter\DbTable;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Session\Container;

use Zend\Validator\StringLength;

class AuthenticateController extends AbstractActionController{

    /**
     * Method verifies that a user used the right credentials to enter Spex Dashboard.
     * Posted vars are $login['username'] and $login['password']
     * @return \Zend\Http\Response $response object
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $bcrypt = new Bcrypt();
        if($request->isPost()) {
            $login = (array) $request->getPost();
            $authTable = $this->getServiceLocator()->get('Authenticate\Model\AuthTable');
            $username = $authTable->selectUser($login['username']);
            //  Checks to see if user supplied any credentials.
            if( empty($username) ) {
                $this->flashMessenger()->addMessage("Username doesn't exist.");
                return $this->redirect()->toRoute("auth", array('action'=>'index'));
            } else {
                $registeredPassword = trim($username[0]['password']);
                $registeredUsername = trim($username[0]['username']);
                $userPassword = trim($login['password']);
                //  Verifies is supplied password and registered password are the same.
                if( $bcrypt->verify($userPassword, $registeredPassword) ) {
                    $dbAdapter = $this->serviceLocator->get('Zend\Db\Adapter\Adapter');
                    $authAdapter = new DbTable($dbAdapter, 'users', 'username', 'password');
                    $authAdapter->setIdentity($registeredUsername)
                                ->setCredential($registeredPassword);
                    $authService = $this->serviceLocator->get('Zend\Authentication\AuthenticationService');
                    $authService->setAdapter($authAdapter);
                    $result = $authService->authenticate();
                    // Authenticates and if valid redirects to Spex Dashboard
                    if ($result->isValid()) {
                        // set id as identifier in session
                        $userId = $authAdapter->getResultRowObject('userid')->userid;
                        $authService->getStorage()
                                    ->write($userId);

                        $authTable = $this->getServiceLocator()->get('Authenticate\Model\AuthTable');
                        $authTable->storeUser($userId);
                        return $this->redirect()->toRoute('home');
                    } else {
                        $loginMsg = $result->getMessages();
                        $message = $loginMsg[0];
                        $this->flashMessenger()->addMessage($message);
                        return $this->redirect()->toRoute("auth", array('action'=>'index'));
                    }
                } else {
                    $this->flashMessenger()->addMessage("Password is incorrect");
                    return $this->redirect()->toRoute("auth", array('action'=>'index'));
                }
            }
        }
    }

    /**
     * Will persist user supplied info.
     * @return \Zend\Http\Response $response object
     */
    public function registerAction()
    {
        $request = $this->getRequest();
        $errorMessages = array();
        if($request->isPost()) {
            $register = (array) $request->getPost();
            echo '<pre>';
//            var_dump($register);
//            $password = $register['password'];
//            die();
            $user = new User();

            $auth = new Auth();
            $authTable = $this->getServiceLocator()->get('Authenticate\Model\AuthTable');

            $validate = new StringLength(array('min'=>8, 'max'=>12));
            if( !$validate->isValid( $register['password'] ) ) {
                $errorMessages = $validate->getMessages();
                $message = array_shift($errorMessages);
                $this->flashMessenger()->addMessage($message);
                return $this->redirect()->toRoute("register", array('action'=>'register'));
            }
            $register = $authTable->encryptPassword($register);
            $hydrator = new ClassMethods();
            $hydrator->hydrate($register, $user);
            //  Persists user
            if(!$auth->createUser($authTable, $user)){
                $this->flashMessenger()->addMessage("You have already registered. Try again.");
                return $this->redirect()->toRoute("auth", array('action'=>'register'));
            }
            return $this->redirect()->toRoute("auth", array('action'=>'index'));
        }
        else{
            return $this->redirect()->toRoute("auth", array('action'=>'index'));
        }
    }

    /**
     * When logs out it destroys the session and redirects to login screen.
     * @return \Zend\Http\Response $response object
     */
    public function logoutAction()
    {
        $loginSession= new Container('login');
        $loginSession->offsetUnset('sessionDataforUser');
        return $this->redirect()->toRoute("auth", array('action'=>'index'));
    }

    /**
     * This just displays the user login.
     * @return array|\Zend\View\Model\ViewModel $result object
     */
    public function indexAction()
    {
        $return = array();
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            $return = array('message' => $flashMessenger->getMessages());
        }
        $result = new ViewModel($return);
        $result ->setTerminal(true);
        return $result;
    }
}


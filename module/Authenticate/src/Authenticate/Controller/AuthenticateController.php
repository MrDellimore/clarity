<?php

namespace Authenticate\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Crypt\Password\Bcrypt;
use Zend\Authentication\AuthenticationService as AuthService;
use Authenticate\Authenticator\AuthenticationAdapter;
use Authenticate\Entity\User;
use Authenticate\Model\Auth;
use Zend\Authentication\Adapter\DbTable;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\Mvc\Controller\Plugin\PluginInterface;
use Zend\View\Helper\FlashMessenger;
use Zend\Session\Container;

use Zend\Validator\StringLength;

class AuthenticateController extends AbstractActionController{

    public function loginAction()
    {
        $request = $this->getRequest();
        $bcrypt = new Bcrypt();
        if($request->isPost()) {
            $login = (array) $request->getPost();
            $authTable = $this->getServiceLocator()->get('Authenticate\Model\AuthTable');
            $username = $authTable->selectUser($login['username']);
            if( empty($username) ) {
                $this->flashMessenger()->addMessage("Username doesn't exist.");
                return $this->redirect()->toRoute("auth", array('action'=>'index'));
            } else {
                $registeredPassword = trim($username[0]['password']);
                $registeredUsername = trim($username[0]['username']);
                $userPassword = trim($login['password']);
                if( $bcrypt->verify($userPassword, $registeredPassword) ) {
                    $dbAdapter = $this->serviceLocator->get('Zend\Db\Adapter\Adapter');
                    $authAdapter = new DbTable($dbAdapter, 'users', 'username', 'password');
                    $authAdapter->setIdentity($registeredUsername)
                                ->setCredential($registeredPassword);
                    $authService = $this->serviceLocator->get('Zend\Authentication\AuthenticationService');
                    $authService->setAdapter($authAdapter);
                    $result = $authService->authenticate();
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

    public function logoutAction()
    {
        $loginSession= new Container('login');
        $loginSession->offsetUnset('sessionDataforUser');
        return $this->redirect()->toRoute("auth", array('action'=>'index'));
    }

    public function indexAction()
    {
//        $return = array('success' => true);
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


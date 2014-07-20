<?php

namespace Authenticate\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService as AuthService;
use Authenticate\Authenticator\AuthenticationAdapter;
use Users\Entity\User;
use Authenticate\Model\Auth;
use Zend\Authentication\Adapter\DbTable;
use Zend\Mvc\Controller\Plugin\PluginInterface;
use Zend\View\Helper\FlashMessenger;
use Zend\Validator\StringLength;

class AuthenticateController extends AbstractActionController{

    public function loginAction(){
        $request = $this->getRequest();
        if($request->isPost()) {
            $login = (array) $request->getPost();
            $username = $login['username'];
            $password = $login['password'];

            $dbAdapter = $this->serviceLocator->get('Zend\Db\Adapter\Adapter');

            $authAdapter = new DbTable($dbAdapter, 'users', 'username', 'password');
            $authAdapter->setIdentity($username)
                        ->setCredential($password);
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
                return $this->redirect()->toUrl('/');

            } else {
                $loginMsg = $result->getMessages();
                $message = $loginMsg[0];
                $this->flashMessenger()->addMessage($message);
                return $this->redirect()->toRoute("auth", array('action'=>'index'));
            }

        }

        else{
            return $this->redirect()->toRoute("auth", array('action'=>'index'));
        }
    }

    public function registerAction(){
        $request = $this->getRequest();
        $errorMessages = array();
        if($request->isPost()) {
            $register = (array) $request->getPost();
            $user = new User();
            $auth = new Auth();
            $authTable = $this->getServiceLocator()->get('Authenticate\Model\AuthTable');
            foreach($register as $method => $value){
                if ( $method != 'rpassword' ){
                    $setMethods = 'set'.ucfirst($method);
                    $user->$setMethods($value);
                }
            }
            $validate = new StringLength(array('min'=>8, 'max'=>12));
            if( !$validate->isValid( $user->getPassword() ) ) {
                $errorMessages = $validate->getMessages();
                $message = array_shift($errorMessages);
//                var_dump($message);
                $this->flashMessenger()->addMessage($message);
                return $this->redirect()->toRoute("register", array('action'=>'register'));
            }
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

    public function indexAction(){
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


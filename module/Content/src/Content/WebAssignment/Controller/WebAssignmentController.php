<?php
/**
 * Created by PhpStorm.
 * User: adellimore
 * Date: 8/20/14
 * Time: 12:47 PM
 */


namespace Content\WebAssignment\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class WebAssignmentController extends AbstractActionController{

    /**
     * @var WebAssignTable $webassignTable object
     */
    protected $webassignTable;

    /**
     * This action uses a data table but not server side. It displays all of the Manufacturer and their corresponding site (Focus|Asavings)
     * @return array|\Zend\Http\Response|ViewModel
     */
    public function indexAction()
    {
        //check if logged in
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $web = $this->getWebTable()->accessWeb();
        $viewResult = new ViewModel(array('web'=>$web));
        return $viewResult;

    }

    /**
     * @return WebAssignTable $webassignTable object
     */
    public function getWebTable()
    {
        if (!$this->webassignTable) {
            $sm = $this->getServiceLocator();
            $this->webassignTable = $sm->get('Content\WebAssignment\Model\WebAssignTable');
        }
        return $this->webassignTable;
    }

    /**
     * Action updates webassignment table based on user selection.
     * @return \Zend\Stdlib\ResponseInterface $response object
     */
    public function submitFormAction()
    {
        $result = '';
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        $request = $this->getRequest();
        if($request->isPost()) {
//            var_dump($request->getPost());
            $simpleProd = $request->getPost();
           $result = $this->getWebTable()->updateWebsiteTable($simpleProd['mfc'], $simpleProd['website'], $userLogin['userid']);
        }
        if($result == ''){
            $result = 'No changes to Manufacturer have been made.';
        }

        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);
        return $response;
    }
}
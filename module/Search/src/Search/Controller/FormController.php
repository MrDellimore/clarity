<?php
/**
 * Created by wsalazar.
 * Date: 6/16/14
 * Time: 10:40 AM
 */

namespace Search\Controller;

use Search\Model\NewFields;
use Zend\Mvc\Controller\AbstractActionController;
//use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\View\Model\ViewModel;
use Search\Model\Form;
use Zend\Session\Container;
use Search\Model\SearchCleaner;
use Search\Model\Images;
use Search\Model\RelatedProducts;


/**
 * Class FormController
 * @package Search\Controller
 */
class FormController extends AbstractActionController {

    protected $formTable;

    protected $skuData = array();
    /**
     * @return ViewModel
     */
    public function indexAction(){
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $container = new Container('intranet');
        $queriedData = new Form(new Images(), new RelatedProducts());
        $postData = new Form(new Images(), new RelatedProducts());
        $sku = $this->params()->fromRoute('sku');
        if (!$this->formTable) {
            $this->formTable = $this->getServiceLocator()->get('Search\Model\FormTable');
        }
        if($sku){
            $entityID = $this->formTable->validateSku($sku);
//            if( $entityID === False ) {
//                $view = new ViewModel(array('message'  => 'This Sku does not exist.'));
//                $view->setTemplate('error/404');
//                return $view;
//            } else {
                $this->skuData = $this->formTable->setupData($this->formTable->lookupData($entityID,$sku));
                foreach($this->skuData as $key => $value){
                    $method = 'set'.ucfirst($key);
                    $queriedData->$method($value);
                }
                $container->data = $queriedData;
//            }

        }

        $request = $this->getRequest();
        if($request->isPost()) {
            $formData = array();
            $formData = (array) $request->getPost();
//            echo "<pre>";
//            var_dump($formData);
            foreach($formData as $key => $value){
//                echo 'this are the indexs ' . $key . "<br />";
                $method = 'set'.ucfirst($key);
                $postData->$method($value);
            }
            $cleaner = new SearchCleaner();
            $cleaner->determineQueryStatement($container->data, $postData);
//            $getNew = new NewFields();
//            echo "this is new cost " . $getNew->getCost() . "<br />";
//            die();
        }

        $view = new ViewModel($this->skuData);
        return $view;
    }
}
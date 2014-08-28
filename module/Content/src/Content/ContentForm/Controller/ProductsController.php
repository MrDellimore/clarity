<?php
/**
 * Created by wsalazar.
 * Date: 6/16/14
 * Time: 10:40 AM
 */

namespace Content\ContentForm\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\ClassMethods as cHydrator;
use Zend\View\Model\ViewModel;
use Content\ContentForm\Entity\Products as Form;
use Zend\Session\Container;
//use Content\\Model\EntityCompare;


/**
 * Class ProductsController
 * @package Content\Products\Controller
 */
class ProductsController extends AbstractActionController {

    protected $formTable;
    protected $imageTable;

    //protected $skuData = array();
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
        $queriedData = new Form();
        $sku = $this->params()->fromRoute('sku');
        $form = $this->getFormTable();

        if($sku){
            $entityID = $form->validateSku($sku);
            //insert error handle for invalid sku here

            //lookupdata
            $skuData = $form->lookupForm($entityID);

            //hydrate data to form entity
            $hydrator = new cHydrator;
            $hydrator->hydrate($skuData,$queriedData);

            //stash object in container
            $container->data = $queriedData;
        }

        $view = new ViewModel(array('data'=>$queriedData));
        return $view;
    }
//  load accessories action was here

//  load categories action was here.

//  submit form action was here.

//  brand load action was here.

//  manufacturer load action was here.

//  image save action was here.


    public function getFormTable(){
        if (!$this->formTable) {
            $sm = $this->getServiceLocator();
            $this->formTable = $sm->get('Content\ContentForm\Products\Model\ProductsTable');
        }
        return $this->formTable;
    }

    public function getImageTable(){
        if (!$this->imageTable) {
            $sm = $this->getServiceLocator();
            $this->imageTable = $sm->get('Content\ContentForm\Products\Model\ImageTable');
        }
        return $this->imageTable;
    }
}

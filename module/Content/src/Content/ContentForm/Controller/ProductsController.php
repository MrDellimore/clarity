<?php
/**
 * Created by wsalazar.
 * Date: 6/16/14
 * Time: 10:40 AM
 */

namespace Content\ContentForm\Controller;


use Content\ContentForm\Model\ProductsTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\ClassMethods as cHydrator;
use Zend\View\Model\ViewModel;
use Content\ContentForm\Entity\Products;
use Zend\Session\Container;


/**
 * Class ProductsController
 * @package Content\Products\Controller
 */
class ProductsController extends AbstractActionController
{
    /**
     * Content\ContentForm\Model\ProductsTable object
     * */
    protected $formTable;

    /**
     * Content\ContentForm\Model\ImageTable object
     * */
    protected $imageTable;

    /**
     * Will take in a sku and validate it.
     * @return ViewModel
     */
    public function indexAction(){
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }

        $queriedData = new Products();
        $sku = $this->params()->fromRoute('sku');
        $form = $this->getFormTable();

        if($sku){
            /*Checks to see if Sku exists*/
            $entityID = $form->validateSku($sku);

            /*If sku does not exist, redirect user back to search page.*/
            if(!$entityID){
                return $this->redirect()->toRoute('search');
            }
            //insert error handle for invalid sku here

            //lookupdata
            $skuData = $form->lookupForm($entityID['entity_id']);

            //hydrate data to form entity
            $hydrator = new cHydrator;
            $hydrator->hydrate($skuData,$queriedData);
        }
        else{
            return $this->redirect()->toRoute('search');
        }
//        echo '<pre>';
//        var_dump($skuData);
//        echo "==============";
//        var_dump($queriedData);
//        exit();
        $view = new ViewModel(array('data'=>$queriedData,'originalData' => $skuData));
//        $view->setTerminal(true);

        return $view;
    }

    /**
     * Populates variable as an object of ProductsTable
     * @return ProductsTable $formTable object
     * */
    public function getFormTable(){
        if (!$this->formTable) {
            $sm = $this->getServiceLocator();
            $this->formTable = $sm->get('Content\ContentForm\Model\ProductsTable');
        }
        return $this->formTable;
    }

    /**
     * Populates variable as an object of ImageTable
     * @return ImageTable $imageTable object
     * */
    public function getImageTable(){
        if (!$this->imageTable) {
            $sm = $this->getServiceLocator();
            $this->imageTable = $sm->get('Content\ContentForm\Model\ImageTable');
        }
        return $this->imageTable;
    }
}

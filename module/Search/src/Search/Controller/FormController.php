<?php
/**
 * Created by wsalazar.
 * Date: 6/16/14
 * Time: 10:40 AM
 */

namespace Search\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\ClassMethods as cHydrator;
use Zend\View\Model\ViewModel;
use Search\Model\Form;
use Zend\Session\Container;
use Search\Model\EntityCompare;


/**
 * Class FormController
 * @package Search\Controller
 */
class FormController extends AbstractActionController {

    protected $formTable;

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

/* Removing custom hydrator and using std Classmethod hydrator
            foreach($this->skuData as $key => $value){
//		echo 'key ' . $key . ' value ' . $value . "\n"; 
                $method = 'set'.ucfirst($key);
                $queriedData->$method($value);
            }
*/
            //stash object in container
            $container->data = $queriedData;
        }
        $view = new ViewModel(array('data'=>$queriedData));
        return $view;
    }

    public function loadAccessoriesAction()
    {
        $form = $this->getFormTable();
        $request = $this->getRequest();
        if($request->isPost()) {
            $loadAccessories = $request->getPost();
            $draw = $loadAccessories['draw'];
            $sku = $loadAccessories['search']['value'];
            $limit = $loadAccessories['length'];
            if($limit == '-1'){
                $limit = 100;
            }
            $loadedAccessories = $form->lookupAccessories($sku, (int)$limit);
            $result = json_encode(
                array(
                    'draw'  =>  (int)$draw,
                    'data'  =>  $loadedAccessories,
                    'recordsTotal'  =>  1000,
                    'recordsFiltered'   =>  $limit,
                )
            );
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $response;
        }
    }

    public function submitFormAction(){

        $request = $this->getRequest();
        if($request->isPost()) {
            $postData = new Form();
            $container = new Container('intranet');
            $formData = (array) $request->getPost();
            //fix dates on post...

            //Hydrate into object
            $hydrator = new cHydrator;
            $hydrator->hydrate($formData,$postData);
            //Find dirty and new entities
            $comp = new EntityCompare();
            $dirtyData = $comp->dirtCheck($container->data, $postData);
            $newData = $comp->newCheck($container->data, $postData);

            //run update or insert data
            $form = $this->getFormTable();
            $result = $form->dirtyHandle($dirtyData);
            $form->newHandle($newData);

            if($result == ''){
                $result = 'No changes to sku made.';
            }

            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);

            return $response;
            //return $this->redirect()->toRoute("search", array('action'=>'index'));
        }
    }

    public function brandLoadAction()
    {
        $form = $this->getFormTable();
        $brandList = $form->brandDropDown();
        $result = json_encode($brandList);
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);
        return $response;
    }

    public function manufacturerLoadAction(){

        $form = $this->getFormTable();
        $manufacturerlist = $form->manufacturerDropDown();

        $result = json_encode($manufacturerlist);
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);

        return $response;
    }


    public function getFormTable(){
        if (!$this->formTable) {
            $sm = $this->getServiceLocator();
            $this->formTable = $sm->get('Search\Model\FormTable');
        }
        return $this->formTable;
    }
}

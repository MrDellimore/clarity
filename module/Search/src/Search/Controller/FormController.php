<?php
/**
 * Created by wsalazar.
 * Date: 6/16/14
 * Time: 10:40 AM
 */

namespace Search\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Search\Model\Form;

/**
 * Class FormController
 * @package Search\Controller
 */
class FormController extends AbstractActionController {

    protected $formTable;

    /**
     * @return ViewModel
     */
    public function indexAction(){
//        $form = new Form();
        $sku = $this->params()->fromRoute('sku');
        if (!$this->formTable) {
            $this->formTable = $this->getServiceLocator()->get('Search\Model\FormTable');
        }
        if($sku){
            $entityID = $this->formTable->validateSku($sku);
            if( $entityID === False ) {
                $view = new ViewModel(array('message'  => 'This Sku does not exist.'));
                $view->setTemplate('error/404');
                return $view;
            } else {
//                $form->setSku($sku);
                $fields = array();
                $fields = $this->formTable->lookupData($entityID,$sku);
                $view = new ViewModel($this->formTable->setupData($fields) );
                return $view;
            }
        }
        $request = $this->getRequest();
        error_log( print_r( $this->getRequest()->getPost() ) );
        error_log( $request->isPost());

        if($request->isPost()) {
//            $request->getPost();
//            error_log('hello world');
        }

    }
}
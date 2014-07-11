<?php
/**
 * Created by wsalazar.
 * Date: 6/16/14
 * Time: 10:40 AM
 */

namespace Search\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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
        $id = (int) $this->params()->fromRoute('id');
        if (!$this->formTable) {
            $this->formTable = $this->getServiceLocator()->get('Search\Model\FormTable');
        }
        $this->formTable->validateSku($id);
        $request = $this->getRequest();

        //echo '<pre>';
        //var_dump($this->getRequest()->getPost());
        //echo '</pre>';

        #$request = $this->getRequest();

        error_log( print_r( $this->getRequest()->getPost() ) );
        error_log( $request->isPost());
        if($request->isPost()) {
//            $request->getPost();
//            echo 'this works';
//            error_log('hello world');
        }
        $view = new ViewModel(array('test'  => $id ));
        return $view;
    }

    public function imageUploadAction(){
        return new ViewModel();
    }
/*
    public function addAction(){

    }

    public function editAction(){

    }

    public function deleteAction(){

    }
*/
} 
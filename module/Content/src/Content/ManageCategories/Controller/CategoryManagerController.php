<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 10/7/14
 * Time: 12:22 PM
 */

namespace Content\ManageCategories\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class CategoryManagerController extends AbstractActionController
{

    public function indexAction(){
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $cats = $this->getServiceLocator()->get('Content\ManageCategories\Model\CategoryTable');
        $request = $this->getRequest();
        $cat = '';

        if($request->isPost()){
            $apiData = $request->getPost();
            $category = $apiData['category'];
            $draw = $apiData['draw'];
            $sku = $apiData['search']['value'];
            $limit = $apiData['length'];

            if( isset($category) ){
                foreach( $category as $cat ){
                    $cat = $cat['value'];
                }
            }

            if($limit == '-1'){
                $limit = 100;
            }
            $catProducts = $cats->fetchCategoryProducts($sku , (int)$limit, (int)$cat);
            $result = json_encode(
                array(
                    'draw' => $draw,
                    'recordsTotal' => 1000,
                    'recordsFiltered' => $limit,
                    //results
                    'data' => $catProducts)
            );
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);

            return $response;
        }
//        var_dump($catProducts);
//        die();
//        $lookupTable = $this->getServiceLocator()->get('Content\ContentForm\Model\AttributesTable')->fetchLookupTable();
//        $viewResult = new ViewModel(['test'=>'test']);
//        return $viewResult;
    }

} 
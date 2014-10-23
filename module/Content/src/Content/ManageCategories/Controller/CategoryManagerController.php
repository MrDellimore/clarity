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
    /**
     * @var object CategoryTable|Object
     * */

    protected $cats;

    /**
     * Description: This action populates the datatable with all skus belonging to a certain category id.
     * @return object
     * */
    public function indexAction(){
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $this->cats = $this->getCategoryTable();
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
            $catProducts =  $this->cats->fetchCategoryProducts($sku , (int)$limit, (int)$cat);
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

    }

    /**
     * Description: From the front end when certain checkboxes are checked and remove button is clicked on,
     * this action method will be posted sku and category id
     * @params checkedProducts array
     * @return object
     * */
    public function removeProductCategoriesAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $request = $this->getRequest();
        $results = '';
//        echo $userLogin['userid'];
        if($request->isPost()){

            $checkedProducts = $request->getPost();
            foreach ($checkedProducts as $managedCategories ) {
                ksort($managedCategories);
                foreach ( $managedCategories as $key => $product ) {
                    $results .= $this->getCategoryTable()->removeCats($product['sku'], (int)$product['cat_id'], (int)$userLogin['userid']);
                }
            }
            $result = json_encode(
                array(
                    'result' => $results)
            );
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $result;
        }
    }

    public function searchProductsAction()
    {
        $request = $this->getRequest();
        if($request -> isPost()){
            $queryData = $request->getPost();
            $draw = $queryData['draw'];
            $sku = $queryData['search']['value'];
            $limit = $queryData['length'];

            if( $limit == '-1' ) {
                $limit = 100;
            }
            $products = $this->getCategoryTable()->populateProducts($sku, (int)$limit);
            $result = json_encode(
                array(
                    'draw' => $draw,
                    'recordsTotal' => 1000,
                    'recordsFiltered' => $limit,
                    //results
                    'data' => $products));
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $response;
        }
    }

    public function getCategoryTable(){
        if (!$this->cats) {
            $sm = $this->getServiceLocator();
            $this->cats = $sm->get('Content\ManageCategories\Model\CategoryTable');
        }
        return $this->cats;
    }

} 
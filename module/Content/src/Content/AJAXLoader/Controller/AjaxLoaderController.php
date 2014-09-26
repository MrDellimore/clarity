<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/27/14
 * Time: 4:50 PM
 */

namespace Content\AJAXLoader\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Content\ContentForm\Entity\Products;
use Zend\Stdlib\Hydrator\ClassMethods as cHydrator;
use Zend\Session\Container;


class AjaxLoaderController extends AbstractActionController
{
    protected $searchTable;

//    public function magentoAction()
//    {
//        $loginSession= new Container('login');
//        $userLogin = $loginSession->sessionDataforUser;
//        if(empty($userLogin)){
//            return $this->redirect()->toRoute('auth', array('action'=>'index') );
//        }
//        $request = $this->getRequest();
//
//        if($request->isPost()){
//            $apiData = $request->getPost();
//            $draw = $apiData['draw'];
//            $sku = $apiData['search']['value'];
//            $limit = $apiData['length'];
//
//            if($limit == '-1'){
//                $limit = 100;
//            }
//            $mage = $this->getServiceLocator()->get('Api\Magento\Model\MagentoTable');
//            $skuData = $mage->fetchChangedProducts($sku,$limit );
//            $result = json_encode(
//                array(
//                    'draw' => $draw,
//                    'recordsTotal' => 1000,
//                    'recordsFiltered' => $limit,
//                    //results
//                    'data' => $skuData)
//            );
//            $event    = $this->getEvent();
//            $response = $event->getResponse();
//            $response->setContent($result);
//            return $response;
//        }
//    }


    public function quicksearchAction()
    {
        $result = '';
        $request = $this->getRequest();

        if($request -> isPost()){
            $queryData = $request->getPost();
            $draw = $queryData['draw'];
            $sku = $queryData['search']['value'];
            $limit = $queryData['myKey'];

            if($limit == '-1'){
                $limit = 100;
            }


            $search = $this->getSearchTable();

            $searchResult = $search->skulookup($sku,$limit);
            $searchResult = $this->updatequicksearch($searchResult);


            $result = json_encode(
                array(
                    'draw' => $draw,
                    'recordsTotal' => 1000,
                    'recordsFiltered' => $limit,
                    //results
                    'data' => $searchResult));
        }
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);

        return $response;

    }
    public function updatequicksearch(Array $r){
        foreach($r as $key => $value){
            $r[$key]['sku'] = '<a href = "/content/product/'.$value['sku'].'">'.$value['sku'].'</a>';

            $r[$key]['status'] = $r[$key]['status'] == '0' ?'<span class="label label-sm label-danger">Disabled</span>' : '<span class="label label-sm label-success">Enabled</span>';
            $r[$key]['site'] = $r[$key]['site'] == 3 ? 'aSavings' : 'Focus';
            switch ($r[$key]['visibility']){
                case '1':
                    $r[$key]['visibility'] ='Not Visible Indivdually';
                case '2':
                    $r[$key]['visibility'] ='Catalog';
                case '3':
                    $r[$key]['visibility'] ='Search';
                case '4':
                    $r[$key]['visibility'] ='Catalog, Search';
            }

        }
        return $r;
    }

    public function getSearchTable(){
        if (!$this->searchTable) {
            $sm = $this->getServiceLocator();
            $this->searchTable = $sm->get('Content\ContentForm\Model\SearchTable');
        }
        return $this->searchTable;
    }

    public function loadRelatedAction()
    {
        $form = $this->getServiceLocator()->get('Content\ContentForm\Model\ProductsTable');
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
            $loadedAccessories = $this->updateaccessories($loadedAccessories);
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

    public function loadAccessoriesAction()
    {
        $form = $this->getServiceLocator()->get('Content\ContentForm\Model\ProductsTable');
        $request = $this->getRequest();
        if($request->isPost()) {
            $loadAccessories = $request->getPost();
            $draw = $loadAccessories['draw'];
            $sku = $loadAccessories['search']['value'];
            $limit = $loadAccessories['length'];
            $setAccessories = $loadAccessories['related'];
            $positions = $loadAccessories['position'];
            $firstElements = array();

//Search Results
            if($limit == '-1'){
                $limit = 100;
            }
            $loadedAccessories = $form->lookupAccessories($sku, (int)$limit,'sku');
            //todo add to query where sku not in (array of linked skus)
            $loadedAccessories = $this->updateaccessories($loadedAccessories);

            if(isset($setAccessories)){
            //Items set already
                foreach($setAccessories as $key => $value){
                    $element = $form->lookupAccessories($value['value'],1,'id');
                    $element[0]['sort'] = $positions[$key]['value'];
                    array_push($firstElements,$element);
                }
                //sort firstelements
                $sort=array();
                foreach($firstElements as $value){
                    $sort[$value[0]['entityid']] = $value[0]['sort'];
                }
                arsort($sort);
                $sorted=array();
                foreach($sort as $key=>$value){
                    foreach($firstElements as $value2){
                        if($key == $value2[0]['entityid']){
                            array_push($sorted,current($value2));
                        }
                    }
                }
                $sorted = $this->updateSetaccessories($sorted);


                //prepend Array
                foreach($sorted as $value){
                    array_unshift($loadedAccessories,$value);
                }
            }

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

    public function updateaccessories(Array $r){
        foreach($r as $key => $value){
            $r[$key]['Sku'] = $r[$key]['Sku']."<br /><h6>".$r[$key]['entityid']."</h6>";
            $r[$key]['status'] = $r[$key]['status'] == '0' ?'<span class="label label-sm label-danger">Disabled</span>' : '<span class="label label-sm label-success">Enabled</span>';
            $r[$key]['sort'] = '<input type="text" name="sort" size="1" disabled>';
            $r[$key]['edit'] = "<a href='javascript:;' id=\"addCross\">Add</a>";
        }
        return $r;
    }

    public function updateSetaccessories(Array $r){
        foreach($r as $key => $value){
            $r[$key]['Sku'] = $r[$key]['Sku']."<br /><h6>".$r[$key]['entityid']."</h6>";
            $r[$key]['status'] = $r[$key]['status'] == '0' ?'<span class="label label-sm label-danger">Disabled</span>' : '<span class="label label-sm label-success">Enabled</span>';
            $r[$key]['sort'] = '<input class="pos" type="text" size="1" value="'.$r[$key]['sort'].'">';
            $r[$key]['edit'] = "<a href='javascript:;'  id=\"removeCross\">Delete</a>";
        }
        return $r;
    }

    public function loadCategoriesAction()
    {
        $form = $this->getServiceLocator()->get('Content\ContentForm\Model\ProductsTable');
        $categoryList = $form->fetchCategoriesStructure();

        foreach($categoryList as $key => $value){


            if($value['text'] == "Root"){
                $categoryList[$key]['parent'] ='#';
                $categoryList[$key]['state'] =array('opened' => true);

            }
        }


        $result = json_encode($categoryList);


        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);
        return $response;
    }

    public function submitFormAction()
    {

        $request = $this->getRequest();
        if($request->isPost()) {
            $postData = new Products();
            $oldData = new Products();

            $formData = (array) $request->getPost();


            //fix dates on post...

            //Hydrate into Old Data object
            $hydrator = new cHydrator;
            $hydrator->hydrate($formData['oldData'],$oldData);
            unset($formData['oldData']);

            //Hydrate into Post Data object
            $hydrator = new cHydrator;
            $hydrator->hydrate($formData,$postData);





            //Find dirty and new entities
            $comp = $this->getServiceLocator()->get('Content\ContentForm\Model\EntityCompare');
            $dirtyData = $comp->dirtCheck($oldData, $postData);
            $newData = $comp->newCheck($oldData, $postData);
            $rinseData = $comp->rinseCheck($oldData, $postData);






            //update/insert data
            $form = $this->getServiceLocator()->get('Content\ContentForm\Model\ProductsTable');
            $result = $form->dirtyHandle($dirtyData, $oldData);
            $result .= $form->newHandle($newData, $oldData);
            $result .= $form->rinseHandle($rinseData);



            if($result == ''){
                $result = 'No changes to sku made.';
            }

            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);

            return $response;

        }
    }
    public function brandLoadAction()
    {
        $form = $this->getServiceLocator()->get('Content\ContentForm\Model\ProductsTable');
        $brandList = $form->brandDropDown();
        $result = json_encode($brandList);
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);
        return $response;
    }

    public function manufacturerLoadAction(){

        $form = $this->getServiceLocator()->get('Content\ContentForm\Model\ProductsTable');
        $manufacturerlist = $form->manufacturerDropDown();

        $result = json_encode($manufacturerlist);
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);

        return $response;
    }

    public function imageSaveAction(){

        $request = $this->getRequest();

        if($request -> isPost()){
            $imageData = $request->getFiles()->toArray();

            //save image
            $imageHandler = $this->getServiceLocator()->get('Content\ContentForm\Model\ImageTable');
            $imageResponse = $imageHandler->saveImageFile($imageData);

            $result = json_encode($imageResponse);
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $response;
        }

    }
} 
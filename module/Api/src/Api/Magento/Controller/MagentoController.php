<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/17/14
 * Time: 4:04 PM
 */

namespace Api\Magento\Controller;

use Zend\Db\Exception\UnexpectedValueException;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Content\ContentForm\Tables\Spex;
use Zend\View\Helper\FlashMessenger;


class MagentoController  extends AbstractActionController
{
    use Spex;

    protected $magentoTable;

    protected $mageSoap;

    protected $skuData;

    protected $dirtyAttributeSkus = array();


    public function magentoAction()
    {
        return new ViewModel([]);
    }

    public function kpiUpdateCountAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $kpi = $this->getServiceLocator()->get('Api\Magento\Model\KeyPerformanceIndicator');
        $updateCount = $kpi->updateCount();
        $categoryCount = $kpi->fetchCategoryCount();

        $result = json_encode([
                'updateCount'       =>      $updateCount,
                'categoryCount'     =>      $categoryCount,
                ]
        );
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);
        return $response;
    }

    public function kpiImageCountAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $kpi = $this->getServiceLocator()->get('Api\Magento\Model\KeyPerformanceIndicator');
        $imageCount = $kpi->fetchImageCount();
        $result = json_encode(
            array(
                'imageCount' => $imageCount)
        );
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);
        return $response;
    }

    public function newImagesAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $request = $this->getRequest();

        if($request->isPost()){
            $imageData = $request->getPost();
            $draw = $imageData['draw'];
            $sku = $imageData['search']['value'];
            $limit = (int)$imageData['length'];

            if($limit == '-1'){
                $limit = 100;
            }
            $kpi = $this->getServiceLocator()->get('Api\Magento\Model\KeyPerformanceIndicator');
            $images = $this->getMagentoTable()->fetchNewImages($sku,$limit);//fetchImages
//            $updateCount = $kpi->updateCount();
            $result = json_encode(
                array(
                    'draw' => $draw,
                    'recordsTotal' => 1000,
                    'recordsFiltered' => $limit,
                    //results
                    'data' => $images)
            );
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $response;
        }
    }

    public function updateItemsAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $request = $this->getRequest();

        if($request->isPost()){
            $apiData = $request->getPost();
            $draw = $apiData['draw'];
            $sku = $apiData['search']['value'];
            $limit = $apiData['length'];

            if($limit == '-1'){
                $limit = 100;
            }
            $skuData = $this->getMagentoTable()->fetchChangedProducts($sku,$limit);
//            $updateCount = $kpi->updateCount();
            $result = json_encode(
                array(
                    'draw' => $draw,
                    'recordsTotal' => 1000,
                    'recordsFiltered' => $limit,
                    //results
                    'data' => $skuData)
            );
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $response;
        }
    }


    public function updateCategoriesAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $request = $this->getRequest();

        if($request->isPost()){
            $apiData = $request->getPost();
            $draw = $apiData['draw'];
            $sku = $apiData['search']['value'];
            $limit = $apiData['length'];

            if($limit == '-1'){
                $limit = 100;
            }
            $categories = $this->getMagentoTable()->fetchChangedCategories($sku,$limit);
//            $updateCount = $kpi->updateCount();
            $result = json_encode(
                array(
                    'draw' => $draw,
                    'recordsTotal' => 1000,
                    'recordsFiltered' => $limit,
                    //results
                    'data' => $categories)
            );
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $response;
        }
    }


    public function updateRelatedAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $request = $this->getRequest();

        if($request->isPost()){
            $apiData = $request->getPost();
            $draw = $apiData['draw'];
            $sku = $apiData['search']['value'];
            $limit = $apiData['length'];

            if($limit == '-1'){
                $limit = 100;
            }
            $linkedProduct = $this->getMagentoTable()->fetchLinkedProducts($sku,$limit);
//            $updateCount = $kpi->updateCount();
            $result = json_encode(
                array(
                    'draw' => $draw,
                    'recordsTotal' => 1000,
                    'recordsFiltered' => $limit,
                    //results
                    'data' => $linkedProduct)
            );
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $response;
        }
    }

    protected function soapItemAction()
    {
        $categorySoapResponse = $itemSoapResponse = $resp = $linkedSoapResponse = Null;
        $updateCategories = $updateFields = $linkedFields = '';
        $groupedProd = $categorizedProd = $changedProducts = [];
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
//        echo '<pre>';
        /*Fetch categories*/
        $request = $this->getRequest();

        if ( $request->isPost() ) {
            $checkboxSku = $request->getPost();
            if( !count( $checkboxSku ) ) {
                return $this->redirect()->toRoute('apis');
            }
            if( !empty($checkboxSku['skuItem']) ) {
                $groupedProd = $this->getMagentoTable()->groupSku($checkboxSku['skuItem']);
                $changedProducts = $this->getMagentoTable()->fetchDirtyProducts($groupedProd);
            }
            if( !empty($checkboxSku['skuCategory']) ) {
                $categorizedProd = $this->getMagentoTable()->groupCategories($checkboxSku['skuCategory']);
            }
            /*Fetch products that have changed due to content team.*/
//            var_dump($normalizedProd);
        }
//        echo get_class($this->getMagentoSoap());
//        die();
//        $categories = $this->getMagentoTable()->fetchChangedCategories();
//        $mage = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap');
//        $mage = $this->getMagentoSoap();

        /*Fetch Related Products
        TODO have to figure out why some entity ids like 676 are not removed.
        */
        $linkedProds = $this->getMagentoTable()->fetchLinkedProducts();

//        var_dump($categories, $changedProducts, $linkedProds);
//        die();

        if( !empty($categorizedProd) ) {
            /*Make api call to delete and update Sku with new categories*/
//            $categorySoapResponse = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap')->soapCategoriesUpdate($categories);
            $categorySoapResponse = $this->getMagentoSoap()->soapCategoriesUpdate($categorizedProd);
            foreach ( $categorySoapResponse as $index => $catResponse ) {
                foreach ( $catResponse as $key => $soapResponse ) {
                    if( $soapResponse ) {
                        $updateCategories .= $this->getMagentoTable()->updateProductCategoriesToClean($categorizedProd[$key]);
                    }
                }
            }
        }
//        $session = new Container('dirty_skus');
//        $changedProducts= $session->dirtyProduct;

        if( !empty($changedProducts) ) {
            /*Update Mage with up-to-date products*/
//            $mage = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap');
//            $updateProducts = $this->getServiceLocator()->get('Api\Magento\Model\MagentoTable');
            $itemSoapResponse = $this->getMagentoSoap()->soapUpdateProducts($changedProducts);
//            $updatedIds = $updateProducts->checkUpdates($normalizedProd);
            foreach ( $itemSoapResponse as $index => $itemResponse ) {
                foreach ( $itemResponse as $key => $soapResponse ) {
                    if( $soapResponse ){
                        $updateFields .= $this->getMagentoTable()->updateToClean($changedProducts[$key]);
//                        $updateFields .= $this->getMagentoTable()->updateToClean($updatedIds[$key]);
                    }
                }
            }
        }
//        die();
        if( !empty($linkedProds) ) {
            /*Update Mage with up-to-date linked products*/
            $linkedSoapResponse = $this->getMagentoSoap()->soapLinkedProducts($linkedProds);
            foreach ( $linkedSoapResponse as $index => $linkedResponse ) {
                foreach ( $linkedResponse as $key => $soapResponse ) {
                    if( $soapResponse ) {
                        $linkedFields = $this->getMagentoTable()->updateLinkedProductstoClean($linkedProds[$key]);
                    }
                }
            }
        }
        $result = '';

        if ( $updateCategories || $updateFields || $linkedFields ) {
            $result = $updateCategories .'<br />'.$updateFields.'<br />'.$linkedFields;
//            return $this->redirect()->toRoute('apis');
        }
        if( empty($result) ) {
            $result = 'Nothing has been uploaded.';
        }
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);

        return $response;
    }

    public function soapNewItemsAction()
    {
        $url = $this->url()->fromRoute('api-magento-new-items');
//        echo '<pre>';
//        var_dump($url);
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $response = Null;
//        echo '<pre>';
        $newProducts = $this->getMagentoTable()->fetchNewItems();
        if( $newProductResponse = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap')->soapAddProducts($newProducts) ) {
            $newProducts = $this->getMagentoTable()->adjustProductKeys($newProducts);
            foreach( $newProductResponse as $index => $newResponse ) {
                foreach( $newResponse as $key => $newEntityId ) {
                    if( $newEntityId ) {
                        $response = $this->getMagentoTable()->updateNewItemsToClean($newProducts[$key], $newEntityId);
                    }
                }
            }
//            die();
            if( $response ) {
                $url .= '?status=true';
                return $this->redirect()->toRoute('apis');
            }
        }
        $url .= '?status=false';
        return $this->redirect()->toRoute('apis');
    }

    public function soapImagesAction()
    {
        $images = [];
        $result = '';
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $request = $this->getRequest();
        if ( $request->isPost() ) {
            $checkboxImages = $request->getPost();

            if( !count( $checkboxImages ) ) {
                return $this->redirect()->toRoute('apis');
            }
            $images = $this->getMagentoTable()->orderImages($checkboxImages['skuImage']);
//            var_dump($images);
//            die();
        }
//        die();
        if($image = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap')->soapMedia($images)) {
            foreach($image as $key => $img){
                foreach($img as $ind => $imgName){
                    if(preg_match('/jpg/',$imgName)){
                        $result .= $this->getMagentoTable()->updateImagesToClean($images[$ind]);
                    }
                }
            }
        }
        if( empty($result) ) {
            $result = 'Nothing has been uploaded.';
        }
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);

        return $response;
    }

    public function getMagentoTable()
    {
        if (!$this->magentoTable) {
            $sm = $this->getServiceLocator();
            $this->magentoTable = $sm->get('Api\Magento\Model\MagentoTable');
        }
        return $this->magentoTable;
    }

    public function getMagentoSoap()
    {
        if (!$this->mageSoap) {
            $sm = $this->getServiceLocator();
            $this->mageSoap = $sm->get('Api\\Magento\\Model\\MageSoap');
        }
        return $this->mageSoap;
    }
}
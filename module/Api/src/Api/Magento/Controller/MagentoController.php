<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/17/14
 * Time: 4:04 PM
 */

namespace Api\Magento\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Content\ContentForm\Tables\Spex;

class MagentoController extends AbstractActionController
{
    /**
     * Trait
     */
    use Spex;

    /**
     * @var object
     */
    protected $magentoTable;

    /**
     * @var object
     */
    protected $mageSoap;

    /**
     * @var array
     */
    protected $skuData;

    /**
     *
     * @return \Zend\Http\Response|ViewModel
     */
    public function magentoAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        return new ViewModel([]);
    }

    /**
     * This action returns an integer to a KPI with how many updates to push to Mage DB.
     * @return \Zend\Http\Response|\Zend\Stdlib\ResponseInterface
     */
    public function kpiUpdateCountAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $kpi = $this->getServiceLocator()->get('Api\Magento\Model\KeyPerformanceIndicator');
        $attributeCount = $kpi->updateCount();
        $categoryCount = $kpi->fetchCategoryCount();
        $linkedCount = $kpi->fetchLinkedCount();
        $updateCount = (int)$attributeCount + (int) $categoryCount + (int) $linkedCount;
        $result = json_encode(['updateCount' => (int)$updateCount]);
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);
        return $response;
    }

    /**
     * This action returns an integer to a KPI with how many new products/skus to push to Mage DB.
     * @return \Zend\Http\Response|\Zend\Stdlib\ResponseInterface
     */
    public function kpiNewProductCountAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $kpi = $this->getServiceLocator()->get('Api\Magento\Model\KeyPerformanceIndicator');
        $newProdCount = $kpi->fetchNewCount();
        $result = json_encode(['newProdCount' => $newProdCount]);
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);
        return $response;
    }

    /**
     * This action returns an integer to a KPI with how many new images to push to Mage DB.
     * @return \Zend\Http\Response|\Zend\Stdlib\ResponseInterface
     */
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

    /**
     * This action is used for the data table. It will list all of the new images to create in mage db.
     * @return \Zend\Http\Response|\Zend\Stdlib\ResponseInterface
     */
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
            $images = $this->getMagentoTable()->fetchNewImages($sku,$limit);
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

    /**
     * This action is used for the data table. It will list all of the new products/skus to create in mage db.
     * @return \Zend\Http\Response|\Zend\Stdlib\ResponseInterface
     */
    public function newProductsAction()
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
            $productTable = $this->getServiceLocator()->get('Content\ContentForm\Model\ProductsTable');
            $newProducts = $this->getMagentoTable()->fetchNewItems($sku, $limit, $productTable);
            $result = json_encode(
                array(
                    'draw' => $draw,
                    'recordsTotal' => 1000,
                    'recordsFiltered' => $limit,
                    //results
                    'data' => $newProducts)
            );
            $event    = $this->getEvent();
            $response = $event->getResponse();
            $response->setContent($result);
            return $response;
        }
    }

    /**
     * This action is used for the data table. It will list all of products attributes to be updated in mage db.
     * @return \Zend\Http\Response|\Zend\Stdlib\ResponseInterface
     */
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
            $limit = (int)$apiData['length'];

            if($limit == '-1'){
                $limit = 100;
            }
            $productTable = $this->getServiceLocator()->get('Content\ContentForm\Model\ProductsTable');
            $skuData = $this->getMagentoTable()->fetchChangedProducts( $sku, $limit, $productTable );
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

    /**
     * This action is used for the data table. It will list all of the Categories that have been deleted or added for a
     * particular sku for mage db.
     * @return \Zend\Http\Response|\Zend\Stdlib\ResponseInterface
     */
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
            $productTable = $this->getServiceLocator()->get('Content\ContentForm\Model\ProductsTable');
            $categories = $this->getMagentoTable()->fetchChangedCategories($sku, $limit, $productTable);
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

    /**
     * This action is used for the data table. It will list all of the Related Products that have been deleted or added for a
     * particular sku for mage db.
     * @return \Zend\Http\Response|\Zend\Stdlib\ResponseInterface
     */
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
            $productTable = $this->getServiceLocator()->get('Content\ContentForm\Model\ProductsTable');
            $linkedProduct = $this->getMagentoTable()->fetchLinkedProducts($sku, $limit, $productTable);
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

    /**
     * Will make api call for updates to be done based on what check boxes have been selected in UI.
     * @return \Zend\Http\Response|\Zend\Stdlib\ResponseInterface $response object
     */
    protected function soapItemAction()
    {
        $categorySoapResponse = $itemSoapResponse = $linkedSoapResponse = Null;
        $updateCategories = $updateFields = $linkedFields = '';
        $groupedProd = $categorizedProd = $linkedProducts = [];
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $request = $this->getRequest();

        if ( $request->isPost() ) {
            $checkboxSku = $request->getPost();
            if( !count( $checkboxSku ) ) {
                return $this->redirect()->toRoute('apis');
            }
            if( !empty($checkboxSku['skuItem']) ) {
//                if multiple attributes for the same sku have been selected this will stack them in the same array.
                $groupedProd = $this->getMagentoTable()->groupSku($checkboxSku['skuItem']);
            }
            if( !empty($checkboxSku['skuCategory']) ) {
//                This could be done in javascipt. It just makes the array 0-based and increments accordingly.
                $categorizedProd = $this->getMagentoTable()->groupCategories($checkboxSku['skuCategory']);
            }

            if( !empty($checkboxSku['skuLink']) ) {
//                This could be done in javascipt. It just makes the array 0-based and increments accordingly.
                $linkedProducts = $this->getMagentoTable()->groupRelated($checkboxSku['skuLink']);
            }
        }

        /*
        TODO have to figure out why some entity ids like 676 are not removed.
        */
        /*Make api call to delete and update Sku with new categories*/
        if( !empty($categorizedProd) ) {
            $categorySoapResponse = $this->getMagentoSoap()->soapCategoriesUpdate($categorizedProd);

            foreach ( $categorySoapResponse as $index => $catResponse ) {
                foreach ( $catResponse as $key => $soapResponse ) {
                    if( $soapResponse ) {
                        if ( $index == 0 ) {
                            $updateCategories .= $this->getMagentoTable()->updateProductCategoriesToClean($categorizedProd[$key]);
                        } else {
                            $updateCategories .= $this->getMagentoTable()->updateProductCategoriesToClean($categorizedProd[(int)$index.$key]);
                        }
                    }
                }
            }
        }
        /*Update Mage with up-to-date products*/
        if( !empty($groupedProd) ) {
            $itemSoapResponse = $this->getMagentoSoap()->soapUpdateProducts($groupedProd);
//            This method below can probably be taken out since qty will not be updated through this api. Andrew has
//            a job that does this in Management Studio. But for now I'll leave it because it doesn't hurt it being here.
            $groupedProd = $this->getMagentoTable()->adjustUpdateProductKeys($groupedProd);
            foreach ( $itemSoapResponse as $index => $itemResponse ) {
                foreach ( $itemResponse as $key => $soapResponse ) {
                    if( $soapResponse ) {
                        if ( $index == 0 ) {
                            $updateFields .= $this->getMagentoTable()->updateToClean($groupedProd[$key]);
                        } else {
                            $updateFields .= $this->getMagentoTable()->updateToClean($groupedProd[(int)$index.$key]);
                        }
                    }
                }
            }
        }
        if( !empty($linkedProducts) ) {
            /*Update Mage with up-to-date linked products*/
            $linkedSoapResponse = $this->getMagentoSoap()->soapLinkedProducts($linkedProducts);
            foreach ( $linkedSoapResponse as $index => $linkedResponse ) {
                foreach ( $linkedResponse as $key => $soapResponse ) {
                    if( $soapResponse ) {
                        if ( $index == 0 ) {
                            $linkedFields .= $this->getMagentoTable()->updateLinkedProductstoClean($linkedProducts[$key]);
                        } else {
                            $linkedFields .= $this->getMagentoTable()->updateLinkedProductstoClean($linkedProducts[(int)$index.$key]);
                        }
                    }
                }
            }
        }
        $result = '';

        if ( $updateCategories || $updateFields || $linkedFields ) {
            $result = $updateCategories .'<br />'.$updateFields.'<br />'.$linkedFields;
        }
        if( empty($result) ) {
            $result = 'Nothing has been uploaded.';
        }
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);

        return $response;
    }

    /**
     * Will make api call for new products/skus to be done based on what check boxes have been selected in UI.
     * <code>
     * if ( $index == 0 ) I have this condition in certain places after my api calls because the api returns a batch
     * in multiples of 10 to me because I supply it a batch in multiples of 10.
     * </code>
     * @return \Zend\Http\Response|\Zend\Stdlib\ResponseInterface $response object
     */
    public function soapNewItemsAction()
    {
        $newProducts = [];
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $result = '';
        $request = $this->getRequest();
        if ( $request->isPost() ) {
            $checkboxNewSku = $request->getPost();
            if( !count( $checkboxNewSku ) ) {
                return $this->redirect()->toRoute('apis');
            }
            if( !empty($checkboxNewSku['skuNewProduct']) ) {
//                This method groupNewSku can be taken out and done in JS instead. It just reorders the array to 0-based array.
                $groupedNewProducts = $this->getMagentoTable()->groupNewSku($checkboxNewSku['skuNewProduct']);
                $newProducts = $this->getMagentoTable()->fetchNewProducts($groupedNewProducts);
            }
        }
        if ( !empty($newProducts) ) {
            if( $newProductResponse = $this->getMagentoSoap()->soapAddProducts($newProducts) ) {
                $newProducts = $this->getMagentoTable()->adjustProductKeys($newProducts);
                foreach( $newProductResponse as $index => $newResponse ) {
                    foreach( $newResponse as $key => $newEntityId ) {
                        if( $newEntityId ) {
                            if ( $index == 0 ) {
                                $result .= $this->getMagentoTable()->updateNewItemsToClean($newProducts[$key], $newEntityId);
                            } else {
                                $result .= $this->getMagentoTable()->updateNewItemsToClean($newProducts[(int)$index.$key], $newEntityId);
                            }
                        }
                    }
                }
            }
        } else {
            $result = "Error";
        }
        if( empty($result) ) {
            $result = 'Nothing has been uploaded';
        }
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);

        return $response;
    }

    /**
     *
     * @return \Zend\Http\Response|\Zend\Stdlib\ResponseInterface $response object
     */
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
//            this method orderImages can be done JS instead. It just reorders the array to 0-based array..
            $images = $this->getMagentoTable()->orderImages($checkboxImages['skuImage']);
        }
        if($image = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap')->soapMedia($images)) {
            foreach($image as $key => $img){
                foreach($img as $ind => $imgName){
//                    The api will return an image name with .jpg. Otherwise iw will return false or an error.
                    if(preg_match('/jpg/',$imgName)){
                        if ( $key == 0 ) {
                            $result .= $this->getMagentoTable()->updateImagesToClean($images[$ind]);
                        } else {
                            $result .= $this->getMagentoTable()->updateImagesToClean($images[(int)$key.$ind]);
                        }
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

    /**
     * @return object
     */
    public function getMagentoTable()
    {
        if (!$this->magentoTable) {
            $sm = $this->getServiceLocator();
            $this->magentoTable = $sm->get('Api\Magento\Model\MagentoTable');
        }
        return $this->magentoTable;
    }

    /**
     * @return object
     */
    public function getMagentoSoap()
    {
        if (!$this->mageSoap) {
            $sm = $this->getServiceLocator();
            $this->mageSoap = $sm->get('Api\Magento\Model\MageSoap');
        }
        return $this->mageSoap;
    }
}
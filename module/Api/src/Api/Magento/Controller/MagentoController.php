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
//use Content\ContentForm\Controller\ProductsController;
use Content\ContentForm\Tables\Spex;
use Zend\View\Helper\FlashMessenger;


class MagentoController  extends AbstractActionController
{
    use Spex;

    protected $magentoTable;

    protected $skuData;

    protected $dirtyAttributeSkus = array();

    public function magentoAction()
    {
//        $mtime = microtime() ;
//        $mtime = explode(" ",$mtime);
//        $mtime = $mtime[1] + $mtime[0];
//        $starttime = $mtime * 1000;
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $this->skuData = array();
        $this->skuData = $this->getMagentoTable()->fetchChangedProducts();
//        echo '<pre>';
//        var_dump($this->skuData);
//        die();
        $cleanCount = $this->getMagentoTable()->fetchCleanCount();
        $newCount = $this->getMagentoTable()->fetchNewCount();
        $images = $this->getMagentoTable()->fetchImageCount();
        $tableHeaders = array('ID','SKU','Attribute Field','New Attribute Value','Last Modified Date','Changed By');
        $session = new Container('dirty_skus');
        $dirtySkus = array();
        $session->dirtyProduct = $this->skuData;
//        $mtime = microtime() ;
//        $mtime = explode(" ",$mtime);
//        var_dump($mtime);
//        $mtime = $mtime[1] + $mtime[0];
//        $finishtime = $mtime * 1000;
//        $totalTime = round(($finishtime-$starttime),4). ' ms';
//        $this->getMagentoTable()->setStopwatch($totalTime);
        return new ViewModel(
            array(
//                'loadTime'  =>  $totalTime,
                'updateHeaders' => $tableHeaders,
                'sku'   =>  $this->skuData,
//                'cleanCount'    => $cleanCount,
                'newCount'    => $newCount,
                'newImages'    => $images,
                'dirtyCount' => $this->getMagentoTable()->getDirtyItems()
            )
        );
    }

    protected function soapItemAction()
    {
        $categorySoapResponse = $itemSoapResponse = $resp = $linkedSoapResponse = Null;
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $session = new Container('dirty_skus');
        $changedProducts= $session->dirtyProduct;

//        $this->getMagentoTable()->groupProducts($changedProducts);
        /*Fetch categories*/
//        $categories = $this->getMagentoTable()->fetchCategoriesSoap();

        /*Fetch Related Products*/
//        $linkedProds = $this->getMagentoTable()->fetchLinkedProducts();

        if(!empty($categories)){
            /*Make api call to delete and update Sku with new categories*/
            $categorySoapResponse = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap')->soapCategoriesUpdate($categories);
        }

        if(!empty($linkedProds)){
            /*Update Mage with up-to-date linked products*/
            $linkedSoapResponse = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap')->soapLinkedProducts($linkedProds);
        }

        if(!empty($changedProducts)){
            /*Update Mage with up-to-date products*/
            $itemSoapResponse = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap')->soapUpdateProducts($changedProducts);
        }
        if( $categorySoapResponse || $itemSoapResponse || $linkedSoapResponse ){
//echo 'ahah';die();
            foreach( $itemSoapResponse as $key => $soapResponse ) {
                foreach( $soapResponse as $index => $soapRes ) {
                    if( preg_match('/Product/', $soapRes)) {
                        $resp = $soapResponse;
                    }
//            SQLSTATE[40001]: Serialization failure: 1213 Deadlock found when trying to get lock; try restarting transaction
//            I suppose this happens when there is too much traffic. I think once content team moves over to zend it will not deadlock anymore.
                    if( preg_match('/Serialization failure/',$soapRes )){
                        $resp = $soapResponse ;
                    }
                    if(true === $soapRes){
                        $resp = $soapRes;
                    }
                }
            }
        }

            if ( $resp === true ||
                (!is_null($categorySoapResponse) &&  $categorySoapResponse === true) ||
                (!is_null($linkedSoapResponse) &&  $linkedSoapResponse === true)
            ){
//                echo 'haha';
//                die();
//                $updateCategories = $this->getMagentoTable()->updateProductCategoriesToClean($categories);
//                $linkedFields = $this->getMagentoTable()->updateLinkedProductstoClean($linkedProds);
                $updateFields = $this->getMagentoTable()->updateToClean($changedProducts);
                if( $updateFields || $updateCategories || $linkedFields ){
                    return $this->redirect()->toRoute('apis');
                }
            }
            if( preg_match('/Category not exist/',$categorySoapResponse) ){
//            'Category not exist' for $categorySoapResponse
                trigger_error('Category does not exist for Magento Admin');
                throw new \UnexpectedValueException('Category does not exist in Magento Admin');
            }
//        }
    }

    public function soapNewItemsAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $newProducts = $this->getMagentoTable()->fetchNewItems();
        if( $newProductResponse = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap')->soapAddProducts($newProducts) ){
//            var_dump($newProductResponse);
//            die();
            foreach ( $newProductResponse as $key => $response ) {
                foreach ( $response as $index => $resp ) {
                    if( !isset($resp['faultCode']) ) {
                        $this->getMagentoTable()->updateNewItemsToClean($newProducts, $newProductResponse);
                        return $this->redirect()->toRoute('apis', array('action'=>'magento'));
                    }
                    if (isset($resp['faultCode'] ) ) {
                        switch ( (int)$resp['faultCode'] ) {
                            case 1:
                                error_log( $resp['faultMessage'] . ': Sku already exists in Mage Database.' );
                                break;
                            case 3:
                                error_log( $resp['faultMessage'] );
                                break;
                            case 100:
                                error_log( $resp['faultMessage'] );
                                break;
                            case 102:
                                error_log( $resp['faultMessage'] );
                                break;
                            case 104:
                                error_log( $resp['faultMessage'] );
                                break;
                            case 105:
                                error_log( $resp['faultMessage'] );
                                break;
                            case 106:
                                error_log( $resp['faultMessage'] );
                                break;
                        }
                        $this->flashMessenger()->addMessage("An error has occurred please contact IT.");
                        return $this->redirect()->toRoute('apis', array('action'=>'magento'));
                    }
                }
            }
        }
    }

    public function soapImagesAction()
    {
        $loginSession= new Container('login');
        $userLogin = $loginSession->sessionDataforUser;
        if(empty($userLogin)){
            return $this->redirect()->toRoute('auth', array('action'=>'index') );
        }
        $images = $this->getMagentoTable()->fetchImages();
        if($image = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap')->soapMedia($images)) {
            foreach($image as $key => $img){
                foreach($img as $ind => $imgName){
                    if(preg_match('/jpg/',$imgName)){
                        if($updateRes = $this->getMagentoTable()->updateImagesToClean($images)){
                            return $this->redirect()->toRoute('apis',['action'=>'magento']);
                        }
                    }
                }
            }
        }
        return $this->redirect()->toRoute('apis',['action'=>'magento']);
    }

    public function getMagentoTable()
    {
        if (!$this->magentoTable) {
            $sm = $this->getServiceLocator();
            $this->magentoTable = $sm->get('Api\Magento\Model\MagentoTable');
        }
        return $this->magentoTable;
    }
}
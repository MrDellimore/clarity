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


class MagentoController  extends AbstractActionController {

    protected $magentoTable;

    protected $skuData;

    protected $dirtyAttributeSkus = array();

    public function magentoAction()
    {
        $this->skuData = array();
//        $this->skuData = $this->getMagentoTable()->grabSkuData();
        $this->skuData = $this->getMagentoTable()->lookupDirt();
        $cleanCount = $this->getMagentoTable()->lookupClean();
        $session = new Container('dirty_skus');
        $dirtySkus = array();
        $session->dirtyProduct = $this->skuData;
//        var_dump($session->dirtyProduct);
//        foreach( $this->skuData['data'] as $key => $skus){
//            $sku = $this->skuData['data'][$key]['sku'];
//            $dirtySkus = array_push($dirtySkus, (array)$this->skuData['data'][$key]['sku']);
//        }
//        echo "<pre>";
//        var_dump($this->skuData );
//        $dirtyCount = 0;
//        $sku = '';
//        foreach($this->skuData['data'] as $key => $value ){
//            $entityID = $this->skuData['data'][$key]['id'];
//            $sku = $this->skuData['data'][$key]['sku'];
//            $this->dirtyAttributeSkus = array_merge($this->dirtyAttributeSkus,$this->getMagentoTable()->grabDirtyAttributes($entityID, $sku));
//            $dirtyCount++;
//        }
//        array_push($this->dirtyAttributeSkus, $sku);
//        foreach( $this->skuData['data'] as $key => $skus){
//            $sku = $this->skuData['data'][$key]['sku'];
//            $dirtySkus = array_push($dirtySkus, $sku);
//            $dirtySkuData = array_merge($dirtySkus,$this->dirtyAttributeSkus);
//        }
//        var_dump($this->dirtyAttributeSkus);
//        $this->getMagentoTable()->setDirtyCount($dirtyCount);
//        $session->dirtySkus = $this->dirtyAttributeSkus;
//        echo "<pre>";
//        var_dump($this->dirtyAttributeSkus);
        return new ViewModel(
            array(
                'sku'   =>  $this->skuData,
                'cleanCount'    => $cleanCount,
                'dirtyCount' => $this->getMagentoTable()->getDirtyCount()
            )
        );
    }

    public function soapAction()
    {
//        var_dump($this->skuData);
//        die();
        $session = new Container('dirty_skus');
//        $skuData = array();
        $dirtyData = $session->dirtyProduct;
//        var_dump( $dirtyData);
//        die();


//        var_dump($skuData );
        if( true ){
            $res = true;
//        if( $response = $this->getMagentoTable()->soapContent($dirtyData) ){
            /*
            foreach($response as $soapResponse){
                if( preg_match('/Product/', $soapResponse)){
                    $res = $soapResponse;
                }
//            SQLSTATE[40001]: Serialization failure: 1213 Deadlock found when trying to get lock; try restarting transaction
//            I suppose this happens when there is too much traffic. I think once content team moves over to zend it will not deadlock anymore.
                if( preg_match('/Serialization failure/',$soapResponse )){
                    $res = $soapResponse ;
                }
                if(true === $soapResponse){
                    $res = $soapResponse;
                }
            }
            */
            if($res === true){
//                echo 'success';
                $this->getMagentoTable()->updateToClean($dirtyData);
                return $this->redirect()->toRoute('apis', array('action'=>'magento'));
            }
//            var_dump($response);
//            die();


        }
//        die();
//        return 'hello world';

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
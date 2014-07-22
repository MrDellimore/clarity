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

    protected $skuData = array();

    protected $dirtyAttributeSkus = array();

    public function magentoAction()
    {
        $this->skuData = $this->getMagentoTable()->grabSkuData();
        $session = new Container('dirty_skus');
        $dirtySkus = array();
//        foreach( $this->skuData['data'] as $key => $skus){
//            $sku = $this->skuData['data'][$key]['sku'];
//            $dirtySkus = array_push($dirtySkus, (array)$this->skuData['data'][$key]['sku']);
//        }
//        echo "<pre>";
//        var_dump($this->skuData );
        $dirtyCount = 0;
//        $dirtySkus = array();
        foreach($this->skuData['data'] as $key => $value ){
            $entityID = $this->skuData['data'][$key]['id'];
            $sku = $this->skuData['data'][$key]['sku'];
            $dirtySkus['sku'] = $sku;
            $this->dirtyAttributeSkus = array_merge($this->dirtyAttributeSkus,$this->getMagentoTable()->grabDirtyAttributes($entityID, $sku));
//            array_push($dirtySkus,$this->getMagentoTable()->grabDirtyAttributes($entityID, $sku));
            $dirtyCount++;
        }
        echo "<pre>";
        var_dump($dirtySkus);
//        array_push($this->dirtyAttributeSkus, $sku);
//        foreach( $this->skuData['data'] as $key => $skus){
//            $sku = $this->skuData['data'][$key]['sku'];
//            $dirtySkus = array_push($dirtySkus, $sku);
//            $dirtySkuData = array_merge($dirtySkus,$this->dirtyAttributeSkus);
//        }
//        var_dump($this->dirtyAttributeSkus);
        $this->getMagentoTable()->setDirtyCount($dirtyCount);
        $session->dirtySkus = $this->dirtyAttributeSkus;
//        echo "<pre>";
//        var_dump($this->dirtyAttributeSkus);
        return new ViewModel(
            array(
                'sku'   =>  $this->dirtyAttributeSkus,

                'dirtyCount' => $this->getMagentoTable()->getDirtyCount()
            )
        );
    }

    public function soapAction()
    {
        $session = new Container('dirty_skus');
        $skuData = array();
        $skuData = $session->dirtySkus;
//        echo "<pre>";
//        var_dump($skuData);
//        die();


//        var_dump($skuData );
        if( $response = $this->getMagentoTable()->soapContent($skuData) ){
            if( preg_match('/Product/', $response))
            {}
//            SQLSTATE[40001]: Serialization failure: 1213 Deadlock found when trying to get lock; try restarting transaction
//            I suppose this happens when there is too much traffic. I think once content team moves over to zend it will not deadlock anymore.
            if( preg_match('/Serialization failure/',$response ))
            {}
            if(true === $response){
                $this->getMagentoTable()->updateToClean($skuData);
            }
        }
        return 'hello world';
//        return $this->redirect()->toRoute('apis', array('action'=>'magento'));

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
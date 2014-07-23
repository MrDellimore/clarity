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
        $this->skuData = $this->getMagentoTable()->lookupDirt();
        $cleanCount = $this->getMagentoTable()->lookupClean();
        $session = new Container('dirty_skus');
        $dirtySkus = array();
        $session->dirtyProduct = $this->skuData;
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
        $session = new Container('dirty_skus');
        $dirtyData = $session->dirtyProduct;
        if( $response = $this->getMagentoTable()->soapContent($dirtyData) ){

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

            if($res === true){
              if($this->getMagentoTable()->updateToClean($dirtyData)){
                  return $this->redirect()->toRoute('apis', array('action'=>'magento'));
              }
            }
        }
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
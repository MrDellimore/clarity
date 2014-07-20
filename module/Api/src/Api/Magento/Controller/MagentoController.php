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

    public function magentoAction()
    {
        $this->skuData = $this->getMagentoTable()->grabSkuData();
        $session = new Container('dirty_skus');
        $session->skus = $this->skuData;
//        var_dump($this->skuData );
        return new ViewModel(array('sku'=>$this->skuData));
    }

    public function soapAction()
    {
        $session = new Container('dirty_skus');
        $skuData = array();
        $skuData = $session->skus;
        $this->getMagentoTable()->soapContent($skuData);
        return $this->redirect()->toRoute('apis', array('action'=>'magento'));

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
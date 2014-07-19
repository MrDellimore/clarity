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

class MagentoController  extends AbstractActionController {

    protected $magentoTable;

    public function magentoAction()
    {
        if (!$this->magentoTable) {
            $sm = $this->getServiceLocator();
            $this->magentoTable = $sm->get('Api\Magento\Model\MagentoTable');
        }

        $skuData = $this->magentoTable->grabSkus();
//        $dirtyFields = 0;
//        if('1' == $skuData['state']) {
//
//        }
//        echo "<pre>";
//        var_dump($skuData);
        return new ViewModel(array('sku'=>$skuData));
//        die();
//        $this->magentoTable->soapContent();
//        return new ViewModel(array(
//            'id'    => number_format((float)$skuData['id']),
//            'sku'    => number_format((float)$skuData['sku']),
//            'lastModedifiedDate'    => number_format((float)$skuData['lastModedifiedDate']),
//            'dirtyFields' => number_format((float)$skuData['dirty']),
//            'cleanFields'   =>  number_format((float)$skuData['clean']),
//            'newFields' => number_format((float)$skuData['new']),
//        ));
    }
}
/*
<tr>
                                <?php if($index == 'id') {
    foreach($value as $state => $entityID){
        if('dirty' == $state ) {
            foreach($entityID as $id) {
                ?>

                <td><?=$id?></td>
            <?php }
        }

    }
    ?>

<?php                                      }
                                      if('sku' == $index) {
                                          foreach($value as $state => $sku) {
                                              if('dirty' == $state ) {
                                                  foreach($sku as $skuValue) {
                                                      ?>
                                                      <td><?=$skuValue?></td>
                                                  <?php
                                                  }
                                              }
                                          }
                                      }
                                      if('lastModedifiedDate' == $index){
                                          foreach($value as $state => $lastDateModified){
                                              if('dirty' == $state) {
                                                  foreach($lastDateModified as $date)
                                                      ?>

                                                      <td><?=$date?></td>
                                              <?php
                                              }
                                          }
                          */
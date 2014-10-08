<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 10/2/14
 * Time: 6:26 PM
 */

namespace Api\Magento\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Api\Magento\Model\Ssh2CronTabManager as CronTab;
use Zend\Console\Request as ConsoleRequest;

class ConsoleMagentoController  extends AbstractActionController{

    protected $console;

    protected $mage;

    protected $soap;

    public function soapProductsAction()
    {
        $request = $this->getRequest();
        if ( !$request instanceof ConsoleRequest ) {
            throw new \RuntimeException('You can only use this action from a console!');
        }
//        $type = $request->getParam('type');
        $cron = new CronTab();
        $cronJobs = [
            '* * * * * php public/index.php soapCreateItems',
            '* * * * * php public/index.php soapUpdateItems',
            '* * * * * php public/index.php soapCreateImages',
        ];
        $cron->append_cronjob($cronJobs);
//        $this->console = $this->getServiceLocator()->get('Api\Magento\Model\ConsoleMagentoTable');
//        $this->mage = $this->getServiceLocator()->get('Api\Magento\Model\MagentoTable');
//        $this->soap = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap');

//        system('php public/index.php soap call ' . $type . ' product');
//        switch($type){
//            case 'create':
//                $newItems = $console->fetchNewItems();
//                $soap->soapAddProducts($newItems);
//                var_dump($newItems);
//                break;
//            case 'image':
//                echo 'image';
//                break;
//            case 'update':
////                $changedProducts = $console->changedProducts();
////                $linked = $mage->fetchLinkedProducts();
////                $categories = $mage->fetchChangedCategories();
////                $soap->soapCategoriesUpdate($categories);
////                $soap->soapLinkedProducts($linked);
////                $soap->soapChangedProducts($changedProducts);
////                die();
//                break;
//        }


//        $cron->append_cronjob('54 10 3 10 5 /app/clarity/test.php &> /dev/null');
//        var_dump($cron)
//echo 'haha';
//        $shell = 'ls';
//        $shellResult = system($shell, $ret);
//        echo "shell Result " . $shellResult . "<hr /> return " . $ret ;
    }

    public function soapCreateProductsAction()
    {

        $console = $this->getServiceLocator()->get('Api\Magento\Model\ConsoleMagentoTable');

        $this->mage = $this->getServiceLocator()->get('Api\Magento\Model\MagentoTable');

        $this->soap = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap');

        $newItems = $console->fetchNewItems();
        if( !empty($newItems) ) {
            if ( $newProductResponse = $this->soap->soapAddProducts($newItems) ) {
                var_dump($newProductResponse);
                $newProducts = $this->mage->adjustProductKeys($newItems);
                foreach( $newProductResponse as $index => $newResponse ) {
                    foreach( $newResponse as $key => $newEntityId ) {
                        if( $newEntityId ) {
                            $this->mage->updateNewItemsToClean($newProducts[$key], $newEntityId);
                        }
                    }
                }
            }
        }
    }

    public function soapUpdateProductsAction()
    {
        $this->console = $this->getServiceLocator()->get('Api\Magento\Model\ConsoleMagentoTable');
        $this->mage = $this->getServiceLocator()->get('Api\Magento\Model\MagentoTable');
        $this->soap = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap');

        $changedProducts = $this->console->changedProducts();
//        var_dump($changedProducts );
//        die();
        $linked = $this->mage->fetchLinkedProducts();
//        var_dump($linked);
//        die();
        $categories = $this->mage->fetchChangedCategories();

        if( !empty($changedProducts) ) {
            $changeResponse = $this->soap->soapChangedProducts($changedProducts);
            foreach ( $changeResponse as $itemResponse ) {
                foreach ( $itemResponse as $key => $soapResponse ) {
                    if( $soapResponse ) {
                        $this->console->updateToClean($changedProducts[$key]);
                    }
                }
            }
        }
        if( !empty($linked) ) {
           $linkedResponse = $this->soap->soapLinkedProducts($linked);
            foreach ( $linkedResponse as $linkResponse ) {
                foreach ( $linkResponse as $key => $soapResponse ) {
                    if( $soapResponse ) {
                        $this->mage->updateLinkedProductstoClean($linked[$key]);
                    }
                }
            }
        }
        if( !empty($categories) ) {
//            var_dump($categories);
//            die();
            $categoryResponse = $this->soap->soapCategoriesUpdate($categories);
            var_dump($categoryResponse);
//            die();
            foreach ( $categoryResponse as $catResponse ) {
                foreach ( $catResponse as $key => $soapResponse ) {
                    if( $soapResponse ) {
                        $this->mage->updateProductCategoriesToClean($categories[$key]);
                    }
                }
            }
        }
        //var_dump($changedProducts);
        die();

    }

    public function soapCreateMediaAction()
    {
        $this->mage = $this->getServiceLocator()->get('Api\Magento\Model\MagentoTable');
        $this->soap = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap');
        $newImages = $this->mage->fetchNewImages();
        if( !empty($newImages) ) {
            foreach( $newImages as $key => $img ) {
                preg_match( '/<img(.*)src(.*)=(.*)"(.*)"/U' , $img['filename'], $match );
                $newImages[$key]['filename'] = array_pop($match);
            }
            if ( $image = $this->soap->soapMedia($newImages) ) {
//            if($image = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap')->soapMedia($images)) {
                foreach($image as $key => $img){
                    foreach($img as $ind => $imgName){
                        if(preg_match('/jpg/',$imgName)){
                            $this->mage->updateImagesToClean($newImages[$ind]);
                        }
                    }
                }
            }
        }
//        var_dump($newImages);
//        die();

    }
} 
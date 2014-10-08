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
        ];
        $cron->append_cronjob($cronJobs);
        $this->console = $this->getServiceLocator()->get('Api\Magento\Model\ConsoleMagentoTable');
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

//        $newItems = $this->console->fetchNewItems();
        $newItems = $console->fetchNewItems();
        echo 'ahah';
        die();
        echo 'ahah';
        var_dump($newItems);


//        $this->soap->soapAddProducts($newItems);

    }

    public function soapUpdateProductsAction()
    {

        $console = $this->getServiceLocator()->get('Api\Magento\Model\ConsoleMagentoTable');

        $this->mage = $this->getServiceLocator()->get('Api\Magento\Model\MagentoTable');

        $this->soap = $this->getServiceLocator()->get('Api\Magento\Model\MageSoap');


        $changedProducts = $console->changedProducts();
//        $linked = $this->mage->fetchLinkedProducts();
//        $categories = $this->mage->fetchChangedCategories();
//        $this->soap->soapCategoriesUpdate($categories);
//        $this->soap->soapLinkedProducts($linked);
//        $this->soap->soapChangedProducts($changedProducts);
        var_dump($changedProducts);
        die();

    }
} 
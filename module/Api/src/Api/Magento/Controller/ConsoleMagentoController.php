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

class ConsoleMagentoController  extends AbstractActionController{

    public function soapCreateProductsAction()
    {
        $cron = new CronTab();
        $cron->append_cronjob('54 10 3 10 5 /app/clarity/test.php &> /dev/null');

//        var_dump($cron)
//echo 'haha';
//        $shell = 'ls';
//        $shellResult = system($shell, $ret);
//        echo "shell Result " . $shellResult . "<hr /> return " . $ret ;
    }
} 
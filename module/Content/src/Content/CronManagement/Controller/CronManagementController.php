<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 10/7/14
 * Time: 11:06 AM
 */

namespace Content\CronManagement\Controller;

use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class CronManagementController {

    public function indexAction(){

        return new ViewModel(['test'=>'test']);
    }

} 
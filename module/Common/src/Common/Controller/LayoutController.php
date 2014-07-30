<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Common\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LayoutController extends AbstractActionController
{
    public function layoutAction()
    {
//        $loginSession= new Container('login');
//        $userData = $loginSession->sessionDataforUser;
        $this->layout('layout/layout');
//        var_dump($userData);
//        die();
        $view =  new ViewModel( );
//        $view->setTemplate('common/layout');
        return $view;

    }
}

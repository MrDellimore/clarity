<?php
/**
 * Created by PhpStorm.
 * User: adellimore
 * Date: 6/9/14
 * Time: 6:25 PM
 */

namespace Content\ContentForm\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;


class SearchController extends AbstractActionController
{
    public function indexAction(){

      $viewResult = new ViewModel();
      return $viewResult;

    }

    //quick search action that was ajax was here

} 
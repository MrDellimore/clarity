<?php

namespace Logging\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LoggingController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
}
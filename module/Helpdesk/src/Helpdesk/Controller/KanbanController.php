<?php

namespace Helpdesk\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class KanbanController extends AbstractActionController{

    public function indexAction(){
        $viewResult = new ViewModel();


        return $viewResult;
    }
}


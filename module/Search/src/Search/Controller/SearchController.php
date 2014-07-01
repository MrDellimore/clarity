<?php
/**
 * Created by PhpStorm.
 * User: adellimore
 * Date: 6/9/14
 * Time: 6:25 PM
 */

namespace Search\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class SearchController extends AbstractActionController{

    protected $searchTable;

    public function indexAction(){
      $search = $this->getSearchTable();

      $initialSearch = $search->populate();
      $viewResult = new ViewModel(array('result' => $initialSearch));


      return $viewResult;

    }

    public function getSearchTable(){
        if (!$this->searchTable) {
            $sm = $this->getServiceLocator();
            $this->searchTable = $sm->get('Search\Model\SearchTable');
        }
        return $this->searchTable;
    }



} 
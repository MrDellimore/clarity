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

    public function quicksearchAction(){


        $result = '';
        $request = $this->getRequest();

        if($request -> isPost()){
            $queryData = $request->getPost();

            $draw = $queryData['draw'];
            $sku = $queryData['search']['value'];
            $limit = $queryData['length'];

            if($limit == '-1'){
                $limit = 100;
            }


        $search = $this->getSearchTable();

        $searchResult = $search->skulookup($sku,$limit);
        $searchResult = $this->updatequicksearch($searchResult);


        $result = json_encode(
            array(
                'draw' => $draw,
                'recordsTotal' => 1000,
                'recordsFiltered' => $limit,
                //results
                'data' => $searchResult));
        }
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);

        return $response;

    }

    public function updatequicksearch(Array $r){
        foreach($r as $key => $value){
            $r[$key]['sku'] = '<a href = "/form/'.$value['sku'].'">'.$value['sku'].'</a>';
        }
        return $r;
    }

    public function getSearchTable(){
        if (!$this->searchTable) {
            $sm = $this->getServiceLocator();
            $this->searchTable = $sm->get('Search\Model\SearchTable');
        }
        return $this->searchTable;
    }



} 
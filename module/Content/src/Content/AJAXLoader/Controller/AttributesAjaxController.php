<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/27/14
 * Time: 4:50 PM
 */

namespace Content\AJAXLoader\Controller;

use Zend\Mvc\Controller\AbstractActionController;
//use Zend\Stdlib\Hydrator\ClassMethods as cHydrator;

class AttributesAjaxController extends AbstractActionController
{
    protected $searchTable;

    public function quicksearchAction()
    {
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
            $r[$key]['sku'] = '<a href = "/content/product/'.$value['sku'].'">'.$value['sku'].'</a>';
        }
        return $r;
    }

    public function getSearchTable(){
        if (!$this->searchTable) {
            $sm = $this->getServiceLocator();
            $this->searchTable = $sm->get('Content\ContentForm\Model\SearchTable');
        }
        return $this->searchTable;
    }
}
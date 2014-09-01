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

    public function attributesQuickSearchAction()
    {
        $result = '';
        $request = $this->getRequest();

        if($request -> isPost()){
            $queryData = $request->getPost();
            $draw = $queryData['draw'];
            $attributeCode = $queryData['search']['value'];
            $limit = $queryData['length'];

            if( $limit == '-1' ){
                $limit = 100;
            }
            $lookupTable = $this->getServiceLocator()->get('Content\ManageAttributes\Model\AttributesTable')->fetchAttributes($attributeCode);

            $result = json_encode(
                array(
                    'draw' => $draw,
                    'recordsTotal' => 1000,
                    'recordsFiltered' => $limit,
                    //results
                    'data' => $lookupTable));
        }
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);

        return $response;

    }


    public function optionsQuickSearchAction()
    {
        $result = '';
        $request = $this->getRequest();

        if($request->isPost()){
            $queryData = $request->getPost();
            $draw = $queryData['draw'];
            $optionValue = $queryData['search']['value'];
            $limit = $queryData['length'];
            $attributeId = (int)trim($queryData['attributeId']);
            if($limit == '-1') {
                $limit = 100;
            }
            $optionsTable = $this->getServiceLocator()->get('Content\ManageAttributes\Model\OptionTable')->fetchOptions($optionValue, 1641);
//            $optionsTable = $this->getServiceLocator()->get('Content\ManageAttributes\Model\OptionTable')->fetchOptions($optionValue, $attributeId);
            $result = json_encode(
                array(
                    'draw' => $draw,
                    'recordsTotal' => 1000,
                    'recordsFiltered' => $limit,
                    //results
                    'data' => $optionsTable));
        }
        $event    = $this->getEvent();
        $response = $event->getResponse();
        $response->setContent($result);

        return $response;

    }

}
<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 8/27/14
 * Time: 4:50 PM
 */

namespace Content\AJAXLoader\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\ClassMethods;
use Content\ManageAttributes\Entity\Attributes;


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
//            $attributeId = (int)trim($queryData['attributeId']);

//            var_dump($queryData);

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
//            if(empty($attributeId)){
//                $attributeId = 1641;
//                echo 'haha';
//            }
//            echo 'this is att id '. $attId . gettype($attId) ;
//            echo 'this is attribute id '. $attributeId. gettype($attributeId);
//            $optionsTable = $this->getServiceLocator()->get('Content\ManageAttributes\Model\OptionTable')->fetchOptions($optionValue, $attId);
            $optionsTable = $this->getServiceLocator()->get('Content\ManageAttributes\Model\OptionTable')->fetchOptions($optionValue, $attributeId);
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

    public function attributesSubmitFormAction()
    {
        $request = $this->getRequest();
        if($request->isPost()) {
            $attributes = new Attributes();
//            $container = new Container('intranet');
            $postAttributes = (array) $request->getPost();
            //fix dates on post...




            //Hydrate into object
            $hydrator = new ClassMethods;
            $hydrator->hydrate($postAttributes,$attributes);



            //Find dirty and new entities
//            $comp = $this->getServiceLocator()->get('Content\ContentForm\Model\EntityCompare');
//            $dirtyData = $comp->dirtCheck($container->data, $postData);
//            $newData = $comp->newCheck($container->data, $postData);
//            $rinseData = $comp->rinseCheck($container->data, $postData);


            // update/insert data
//            $form = $this->getServiceLocator()->get('Content\ContentForm\Model\ProductsTable');
//            $result = $form->dirtyHandle($dirtyData, $container->data);
//            $result .= $form->newHandle($newData);
//            $result .= $form->rinseHandle($rinseData);

//            if($result == ''){
//                $result = 'No changes to sku made.';
//            }

//            $event    = $this->getEvent();
//            $response = $event->getResponse();
//            $response->setContent($result);

//            return $response;

        }

    }

}
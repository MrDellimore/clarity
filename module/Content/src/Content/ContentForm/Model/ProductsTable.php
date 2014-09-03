<?php

namespace Content\ContentForm\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Session\Container;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Operator;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManager;
use Zend\Log\Writer\Db;
use Zend\EventManager\EventManagerInterface;
use Zend\Log\Logger;
use Content\ContentForm\Entity\Products as Form;

class ProductsTable{

    protected $sku;
    protected $select = Null;
    protected $sql;
    protected $skuFields = array();
    protected $form;
    protected $imageTable;

    protected $mapping = array();

    protected $columnMap = array();

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    use EventManagerAwareTrait;


    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    public function lookupForm($entityid){
        $select = $this->sql->select();
        $select->from('product');
        $select->columns(array('id' => 'entity_id', 'sku' => 'productid'));


        $select->where(array('product.entity_id' => $entityid));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }

        //add to resultset additional Data
        $result = $resultSet->toArray();
        $result = current($result);


        //Fetch Title
        $newAttibute = $this->fetchAttribute($entityid,'varchar','96','title');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Price
        $newAttibute = $this->fetchAttribute($entityid,'decimal','99','price');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Inventory
        $newAttibute = $this->fetchAttribute($entityid,'int','1','Inventory');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Status
        $newAttibute = $this->fetchAttribute($entityid,'int','273','Status');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Visibility
        $newAttibute = $this->fetchAttribute($entityid,'int','526','Visibility');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Condition
        $newAttibute = $this->fetchAttribute($entityid,'int','1655','Condition');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Tax Class
        $newAttibute = $this->fetchAttribute($entityid,'int','274','taxclass');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Stock Status
        $newAttibute = $this->fetchAttribute($entityid,'int','1661','stockStatus');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch URLkey
        $newAttibute = $this->fetchAttribute($entityid,'varchar','481','urlKey');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Cost
        $newAttibute = $this->fetchAttribute($entityid,'decimal','100','cost');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Rebate Price
        $newAttibute = $this->fetchAttribute($entityid,'decimal','1590','rebate');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Mail in Rebate Price
        $newAttibute = $this->fetchAttribute($entityid,'decimal','1593','mailinRebate');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Special Price
        $newAttibute = $this->fetchAttribute($entityid,'decimal','567','specialPrice');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Special Start Date
        $newAttibute = $this->fetchAttribute($entityid,'datetime','568','specialEndDate');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Special End Date
        $newAttibute = $this->fetchAttribute($entityid,'datetime','569','specialStartDate');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Rebate Start Date
        $newAttibute = $this->fetchAttribute($entityid,'datetime','1591','rebateEndDate');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Rebate End Date
        $newAttibute = $this->fetchAttribute($entityid,'datetime','1592','rebateStartDate');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Mail in Start Date
        $newAttibute = $this->fetchAttribute($entityid,'datetime','1594','mailinEndDate');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Mail in  End Date
        $newAttibute = $this->fetchAttribute($entityid,'datetime','1595','mailinStartDate');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch metaTitle
        $newAttibute = $this->fetchAttribute($entityid,'varchar','103','metaTitle');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch metaDescription
        $newAttibute = $this->fetchAttribute($entityid,'varchar','105','metaDescription');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Description
        $newAttibute = $this->fetchAttribute($entityid,'text','97','description');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch inBox
        $newAttibute = $this->fetchAttribute($entityid,'text','1633','inBox');
       // die(print_r($newAttibute));
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch includesFree
        $newAttibute = $this->fetchAttribute($entityid,'text','1679','includesFree');

        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Short Description
        $newAttibute = $this->fetchAttribute($entityid,'text','506','shortDescription');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch metaKeywords
        $newAttibute = $this->fetchAttribute($entityid,'varchar','104','metaKeywords');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch metaTitle
        $newAttibute = $this->fetchAttribute($entityid,'varchar','103','metaTitle');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch originalContent
        $newAttibute = $this->fetchAttribute($entityid,'varchar','1658','orginalContent');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch contentReviewed
        $newAttibute = $this->fetchAttribute($entityid,'varchar','1676','contentReviewed');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch metaDescrition
        $newAttibute = $this->fetchAttribute($entityid,'text','105','metaDescription');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Manufacturer Option
        $newAttibute = $this->fetchAttribute($entityid,'int','102','manufacturer');
        $newOption = $this->fetchOption(current($newAttibute),'102','manufacturer');
        $result[array_keys($newAttibute)[0]] = array('option' => current($newAttibute), 'value' => current($newOption));

        //Fetch Brand Option
        $newAttibute = $this->fetchAttribute($entityid,'int','1641','brand');
        $newOption = $this->fetchOption(current($newAttibute),'1641','brand');
        $result[array_keys($newAttibute)[0]] = array('option' => current($newAttibute), 'value' => current($newOption));


        //Fetch Images
        $images = $this->fetchImages($entityid);
        $result['imageGallery'] = $images;

        //Fetch Category
        $categories = $this->fetchCategories($entityid);
        $result['categories'] = $categories;


        return $result;
    }

    public function fetchAttribute($entityid,$tableType,$attributeid,$property){
        $select = $this->sql->select();

        $select->from('productattribute_'.$tableType);

        $select->columns(array($property => 'value'));
        $select->where(array('entity_id' => $entityid, 'attribute_id' => $attributeid));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $result = $resultSet->toArray();

        //check if array passed or value given
        if(!(is_array($result)) || current($result)[$property] == ''){
            $result = array($property => null);
        }
        else{
            $result = current($result);
        }

        return $result;
    }


    public function fetchOption($option,$attributeid,$property){
        $select = $this->sql->select();

        $select->from('productattribute_option');

        $select->columns(array($property => 'value'));
        $select->where(array('option_id' => $option, 'attribute_id' => $attributeid));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $result = $resultSet->toArray();

        //check if array passed or value given
        if(!(is_array($result)) || current($result)[$property] == ''){
            $result = array($property => null);
        }
        else{
            $result = current($result);
        }

        return $result;
    }

    public function fetchImages($entityid){
        $select = $this->sql->select();

        $select->from('productattribute_images');
        $select->columns(array( 'id' => 'value_id','label' => 'label','position' => 'position','entityid' =>'entity_id',
                                'domain' => 'domain', 'filename' =>'filename',
                                'disabled' => 'disabled','default'=> 'default'));
        $select->where(array('entity_id' => $entityid, 'disabled' => 0));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $result = $resultSet->toArray();

        return $result;

    }

    public function fetchCategories($entityid){
        $select = $this->sql->select();
        $select->from('productcategory');
        $select->columns(array('entityid' => 'entity_id', 'id' =>'category_id'));

        $filter = new Where();
        $filter->equalTo('entity_id', $entityid);
        $pred = new Operator('dataState', Operator::OPERATOR_NOT_EQUAL_TO, 3);
        $filter->addPredicate($pred);

        $select->where($filter);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $result = $resultSet->toArray();

        return $result;
    }


    public function fetchCategoriesStructure(){
        $select = $this->sql->select();
        $select->from('category');
        $select->columns(array('id'=>'category_id','parent'=>'parent_id','text'=>'title'));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $result = $resultSet->toArray();

        return $result;
    }


    /**
     * @param $sku
     * @throws \Exception
     * @return int
     */

    public function validateSku($sku){
        $select = $this->sql->select()->from('product')->where(['productid' => $sku]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet->toArray()[0]['entity_id'];
    }


    /**
     * Manufacturer Drop Down list
     */
    public function manufacturerDropDown(){
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->from('productattribute_option');

        $select->columns(array('value'=>'option_id','mfc' => 'value'));

        $select->where(array('attribute_id' => '102'));

        $select->order('mfc');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }


        return $resultSet->toArray();
    }

    public function brandDropDown(){
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->from('productattribute_option');

        $select->columns(array('value'=>'option_id','brand' => 'value'));

        $select->where(array('attribute_id' => '1641'));

        $select->order('brand');

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }


        return $resultSet->toArray();
    }

    public function lookupAccessories($searchValue, $limit)
    {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('product');
        $select->columns(array('entityID'=>'entity_id','Sku' => 'productid'));
        $titleJoin = new Expression('t.entity_id = product.entity_id and t.attribute_id = 96');
        $priceJoin = new Expression('p.entity_id = product.entity_id and p.attribute_id = 99');
        $quantityJoin = new Expression('q.entity_id = product.entity_id and q.attribute_id = 1');

        $select->join(array('t' => 'productattribute_varchar'), $titleJoin ,array('title' => 'value'));

        $select->join(array('p' => 'productattribute_decimal'), $priceJoin ,array('price' => 'value'));

        $select->join(array('q' => 'productattribute_int'), $quantityJoin ,array('quantity' => 'value'));
        $where = new Where();
        $where->like('product.productid',$searchValue.'%');
        $select->where($where);
        $select->limit($limit);

        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $searchResults = $resultSet->toArray();
        return $searchResults;
    }



    /**
     * Handle isDirty Form entities
     */
    public function dirtyHandle(Form $form, Form $oldData){
        //Find Dirty properties and call corresponding updates
        $startMessage = 'The following fields have been updated :<br>';
        $updateditems = '';

        //update sku
        //update Title
        if(!(is_null($form->getTitle()))) {
            $property = 'title';
            $this->updateAttribute($form->getId(),$form->getTitle(),'96','varchar');
            $this->insertLogging($form->getId(),$oldData->getSku(), $form->getTitle(), $oldData->getTitle(), /*$oldData->getManufacturer(),*/ $property);//,'96','varchar');
            $updateditems .= 'Title<br>';
        }
        //update description
        if(!(is_null($form->getDescription()))) {
            $property = 'description';
            $this->updateAttribute($form->getId(),$form->getDescription(),'97','text');
            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getDescription(), $oldData->getDescription(), /*$oldData->getManufacturer(),*/ $property);//'97','text');
            $updateditems .= 'Description<br>';
        }
        //update inventory
        //update url Key
        //update status
        if(!(is_null($form->getStatus()))) {
            $property = 'status';
            $this->updateAttribute($form->getId(),$form->getStatus(),'273','int');
            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getStatus(), $oldData->getStatus(), /*$oldData->getManufacturer(),*/ $property);//,'273','int');
            $updateditems .= 'Status<br>';
        }
        //update manufacturer
        //update visibility
        if(!(is_null($form->getVisibility()))) {
            $property = 'visibility';
            $this->updateAttribute($form->getId(),$form->getVisibility(),'526','int');
            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getVisibility(), $oldData->getVisibility(),/*$oldData->getManufacturer(),*/ $property);//,'526','int');
            $updateditems .= 'Visibility<br>';
        }
        //update condition
        //update tax class
        //update stock status
        if(!(is_null($form->getStockStatus()))) {
            $property = 'stock status';
            $this->updateAttribute($form->getId(),$form->getStockStatus(),'1661','int');
            $this->insertLogging($form->getId(),$form->getStockStatus(), $oldData->getStockStatus(),$oldData->getManufacturer(), $property);//,'1661','int');
            $updateditems .= 'Stock Status<br>';
        }
        //update price
        //update cost
        //update rebate price
        //update rebatestartenddate
        //update special price
        //update special startenddate
        //update main in rebate price
        //update weight
        //update shipping
        //update text
        //update In Box
        if(!(is_null($form->getInBox()))) {
            $property = 'inbox';
            $this->updateAttribute($form->getId(),$form->getInBox(),'1633','text');
            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getInBox(), $oldData->getInBox(),/*$oldData->getManufacturer(),*/ $property);//,'1633','text');
            $updateditems .= 'In Box<br>';
        }

        //update Includes Free
        if(!(is_null($form->getIncludesFree()))) {
            $property = 'includes free';
            $this->updateAttribute($form->getId(),$form->getIncludesFree(),'1679','text');
            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getIncludesFree(), $oldData->getIncludesFree(), /*$oldData->getManufacturer(),*/ $property);//,'1679','text');ws
            $updateditems .= 'Includes Free<br>';
        }

        //update Meta Description
        if(!(is_null($form->getMetaDescription()))) {
            $property = 'meta description';
            $this->updateAttribute($form->getId(),$form->getMetaDescription(),'105','varchar');
            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getMetaDescription(), $oldData->getMetaDescription(), /*$oldData->getManufacturer(),*/ $property);//,'105','varchar');
            $updateditems .= 'Meta Description<br>';
        }

        //update Original Content
        if(!(is_null($form->getOriginalContent()))) {
            $property = 'original content';
            $this->updateAttribute($form->getId(),$form->getOriginalContent(),'1659','int');
            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getOriginalContent(), $oldData->getOriginalContent(), /*$oldData->getManufacturer(),*/ $property);//,'1659','int');
            $updateditems .= 'Original Content<br>';
        }

        //update Content Reviewed
        if(!(is_null($form->getContentReviewed()))) {
            $property = 'content reviewed';
            $this->updateAttribute($form->getId(),$form->getContentReviewed(),'1676','int');
            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getContentReviewed(), $oldData->getContentReviewed(), /*$oldData->getManufacturer(),*/ $property);//,'1676','int');
            $updateditems .= 'Content Reviewed<br>';
        }

        //update Short Description
        if(!(is_null($form->getShortDescription()))) {
            $property = 'short description';
            $this->updateAttribute($form->getId(),$form->getShortDescription(),'506','text');
            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getShortDescription(), $oldData->getShortDescription(), /*$oldData->getManufacturer(),*/ $property);//,'506','text');
            $updateditems .= 'Visibility<br>';
        }

        //update Images
        if(!(is_null($form->getImageGallery()))) {
            $imageHandler = new ImageTable($this->adapter);


            foreach($form->getImageGallery() as  $value){
                $result=$imageHandler->updateImage($value);
                $updateditems .= $result;
            }
        }


        if($updateditems != ''){
            $updateditems = $startMessage.$updateditems;
        }

        return $updateditems;

    }

    /**
     * Handle isNew Form entities
     */
    public function newHandle(Form $form){

        $inserteditems='';

        //Create new Image
        if(!(is_null($form->getImageGallery()))) {
            $imageHandler = new ImageTable($this->adapter);
            $images = $form->getImageGallery();
            foreach($images as  $value){
                $result=$imageHandler->createImage($value,$form->getId());
                $inserteditems .= $result;
            }
        }

        //Add new Category
        if(!(is_null($form->getCategories()))) {
            $categoryHandler = new CategoryTable($this->adapter);
            $category = $form->getCategories();
            foreach($category as  $value){
                $result=$categoryHandler->addCategory($value,$form->getId());
                $inserteditems .= $result;
            }
        }


        return $inserteditems;
    }

    public function updateAttribute($entityid,$value,$attributeid,$tableType){

        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];
        $update = $this->sql->update('productattribute_'.$tableType)
                            ->set(array('value' => $value,'dataState' => '1', 'changedby' => $user, 'lastModifiedDate'=>date('Y-m-d h:i:s')))
                            ->where(array('entity_id ='.$entityid, 'attribute_id ='.$attributeid));
        $statement = $this->sql->prepareStatementForSqlObject($update);
        return $statement->execute();

    }

    public function insertAttributes($entityid,$value,$attributeid,$tableType){

    }


    public function rinseHandle(Form $form){
        $rinsedItems = '';
        if(!(is_null($form->getCategories()))) {

            $categoryHandler = new CategoryTable($this->adapter);
            $category = $form->getCategories();

            foreach($category as  $value){
                $result=$categoryHandler->removeCategory($value,$form->getId());
                $rinsedItems .= $result;
            }
        }
        return $rinsedItems;
    }


    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->addIdentifiers(array(
            get_called_class()
        ));
        $this->eventManager = $eventManager;
    }

    public function getEventManager()
    {
        if (null === $this->eventManager) {
            $this->setEventManager(new EventManager());
        }

        return $this->eventManager;
    }

    public function insertLogging($entityid, $sku ,$newValue, $oldValue, /*$manufacturer, */$property)//, $attributeid,$tableType)
    {
        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];

        $fieldValueMap = array(
            'entity_id' =>  $entityid,
            'sku'   =>  $sku,
            'oldvalue'  =>  $oldValue,
            'newvalue'  =>  $newValue,
//            'manufacturer'  =>  current(array_keys($manufacturer)),
            'datechanged'   => date('Y-m-d h:i:s'),
            'changedby' =>  $user,
            'property'  =>  $property,
        );

        $eventWritables = array('dbAdapter'=> $this->adapter, 'extra'=> $fieldValueMap);//'fields' => $mapping,
        $this->getEventManager()->trigger('constructLog', null, array('makeFields'=>$eventWritables));
    }
}

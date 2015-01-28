<?php

namespace Content\ContentForm\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Session\Container;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate;
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
        $select->columns(array('id' => 'entity_id', 'sku' => 'productid','status' => 'status','price' => 'price','Inventory' => 'quantity','website' =>'website'));


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
//        $newAttibute = $this->fetchAttribute($entityid,'decimal','99','price');
//        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Inventory
//        $newAttibute = $this->fetchAttribute($entityid,'int','1','Inventory');
//        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Status
//        $newAttibute = $this->fetchAttribute($entityid,'int','273','Status');
//        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Tax Class
        $newAttibute = $this->fetchAttribute($entityid,'int','274','taxclass');
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
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch includesFree
        $newAttibute = $this->fetchAttribute($entityid,'text','1679','includesFree');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch Short Description
        $newAttibute = $this->fetchAttribute($entityid,'text','506','shortDescription');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch metaKeywords
        $newAttibute = $this->fetchAttribute($entityid,'text','104','metaKeywords');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch metaTitle
        $newAttibute = $this->fetchAttribute($entityid,'varchar','103','metaTitle');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);

        //Fetch originalContent
        $newAttibute = $this->fetchAttribute($entityid,'int','1659','originalContent');
        $result[array_keys($newAttibute)[0]]['option'] = current($newAttibute);

        //Fetch contentReviewed
        $newAttibute = $this->fetchAttribute($entityid,'int','1676','contentReviewed');
        $result[array_keys($newAttibute)[0]]['option'] = current($newAttibute);

        //Fetch metaDescrition
        $newAttibute = $this->fetchAttribute($entityid,'varchar','105','metaDescription');
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

        //Fetch Accessories
        $accessories = $this->fetchAccessories($entityid);
        $result['accessories'] = $accessories;

        //Fetch CrossSells
        $crossSell = $this->fetchCrossSell($entityid);
        $result['crossSells'] = $crossSell;

        //Fetch Prime Focal Length Option
        $newAttibute = $this->fetchAttribute($entityid,'int','1713','primeFocalLength');
        $newOption = $this->fetchOption(current($newAttibute),'1713','primeFocalLength');
        $result[array_keys($newAttibute)[0]] = array('option' => current($newAttibute), 'value' => current($newOption));

        //Fetch Zoom Focal Length Option
        $newAttibute = $this->fetchAttribute($entityid,'int','1731','zoomFocalLength');
        $newOption = $this->fetchOption(current($newAttibute),'1731','zoomFocalLength');
        $result[array_keys($newAttibute)[0]] = array('option' => current($newAttibute), 'value' => current($newOption));

        //Fetch Aperture Option
        $newAttibute = $this->fetchAttribute($entityid,'int','1715','aperture');
        $newOption = $this->fetchOption(current($newAttibute),'1715','aperture');
        $result[array_keys($newAttibute)[0]] = array('option' => current($newAttibute), 'value' => current($newOption));

        //Fetch Camera Style Option
        $newAttibute = $this->fetchAttribute($entityid,'int','1717','cameraStyle');
        $newOption = $this->fetchOption(current($newAttibute),'1717','cameraStyle');
        $result[array_keys($newAttibute)[0]] = array('option' => current($newAttibute), 'value' => current($newOption));

        //Fetch Color Option
        $newAttibute = $this->fetchAttribute($entityid,'int','272','color');
        $newOption = $this->fetchOption(current($newAttibute),'272','color');
        $result[array_keys($newAttibute)[0]] = array('option' => current($newAttibute), 'value' => current($newOption));

        //Fetch Visibility Option
        $newAttibute = $this->fetchAttribute($entityid,'int','526','Visibility');
        $newOption = $this->fetchOption(current($newAttibute),'526','Visibility');
        $result[array_keys($newAttibute)[0]] = array('option' => current($newAttibute), 'value' => current($newOption));

        //Fetch Condition
        $newAttibute = $this->fetchAttribute($entityid,'int','1655','Condition');
        $newOption = $this->fetchOption(current($newAttibute),'1655','Condition');
        $result[array_keys($newAttibute)[0]] = array('option' => current($newAttibute), 'value' => current($newOption));

        //Fetch Custom Stock Status
        $newAttibute = $this->fetchAttribute($entityid,'int','1661','stockStatus');
        $newOption = $this->fetchOption(current($newAttibute),'1661','stockStatus');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);
        $result[array_keys($newAttibute)[0]] = array('option' => current($newAttibute), 'value' => current($newOption));

        //Fetch Tax Class
        $newAttibute = $this->fetchAttribute($entityid,'int','274','taxClass');
        $newOption = $this->fetchOption(current($newAttibute),'274','taxClass');
        $result[array_keys($newAttibute)[0]] = current($newAttibute);
        $result[array_keys($newAttibute)[0]] = array('option' => current($newAttibute), 'value' => current($newOption));


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
        $select->columns(array( 'id' => 'value_id','label' => 'label','position' => 'position',
                                'entityid' =>'entity_id',
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

    public function fetchAccessories($entityid){
        $select = $this->sql->select();
        $select->from('productlink');
        $select->columns(array('id'=>'link_id','entity_id'=>'entity_id','linkedSku'=>'linked_entity_id','position' => 'position'));

        $filter = new Where();
        $filter->equalTo('entity_id', $entityid);
        $filter->equalTo('link_type_id', '1');
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

    public function fetchCrossSell($entityid){
        $select = $this->sql->select();
        $select->from('productlink');
        $select->columns(array('id'=>'link_id','entity_id'=>'entity_id','linkedSku'=>'linked_entity_id','position' => 'position'));
        $select->where(array('entity_id' => $entityid, 'link_type_id' =>'1'));

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
        $select = $this->sql->select()->from('product')->columns(array('entity_id'));
        $select->where(['productid' => $sku]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $entityid = $resultSet->toArray();
        $entityid = current($entityid);

        return $entityid;
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

    public function lookupAccessories($searchValue, $limit,$searchTerm,$setSkus = array()){
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from('product');
        $select->columns(array('entityid'=>'entity_id','Sku' => 'productid'));
        $titleJoin = new Expression('t.entity_id = product.entity_id and t.attribute_id = 96');
        $priceJoin = new Expression('p.entity_id = product.entity_id and p.attribute_id = 99');
        $quantityJoin = new Expression('q.entity_id = product.entity_id and q.attribute_id = 1');
        $statusJoin = new Expression('s.entity_id = product.entity_id and s.attribute_id = 273');

        $select->join(array('t' => 'productattribute_varchar'), $titleJoin ,array('title' => 'value'), Select::JOIN_LEFT);

        $select->join(array('p' => 'productattribute_decimal'), $priceJoin ,array('price' => 'value'), Select::JOIN_LEFT);

        $select->join(array('q' => 'productattribute_int'), $quantityJoin ,array('quantity' => 'value'), Select::JOIN_LEFT);

        $select->join(array('s' => 'productattribute_int'), $statusJoin ,array('status' => 'value'), Select::JOIN_LEFT);

        $where = new Where();
        if($searchTerm == 'id'){
            $searchTerm = 'product.entity_id';
        }
        else {
            $searchTerm = 'product.productid';
        }

        $where->like($searchTerm,$searchValue.'%');
        $where->orPredicate(new Predicate\Like('t.value','%'.$searchValue.'%'));
        if (!(empty($setSkus))){
            $where->notIn("product.entity_id", $setSkus);
        }

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

        //update sku NEVER!
//update Title
        if(!(is_null($form->getTitle()))) {
            $property = 'title';
            $this->updateAttribute($form->getId(),$form->getTitle(),'96','varchar');
//            $this->insertLogging($form->getId(),$oldData->getSku(), $form->getTitle(), $oldData->getTitle(), $property);
            $updateditems .= 'Title<br>';
        }
//update description
        if(!(is_null($form->getDescription()))) {
            $property = 'description';
            $this->updateAttribute($form->getId(),$form->getDescription(),'97','text');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getDescription(), $oldData->getDescription(), /*$oldData->getManufacturer(),*/ $property);//'97','text');
            $updateditems .= 'Description<br>';
        }
//update in box
        if(!(is_null($form->getInBox()))) {
            $property = 'inbox';
            $this->updateAttribute($form->getId(),$form->getInBox(),'1633','text');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getInBox(), $oldData->getInBox(),/*$oldData->getManufacturer(),*/ $property);//,'1633','text');
            $updateditems .= 'In Box<br>';
        }
//update Includes Free
        if(!(is_null($form->getIncludesFree()))) {
            $property = 'includes free';
            $this->updateAttribute($form->getId(),$form->getIncludesFree(),'1679','text');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getIncludesFree(), $oldData->getIncludesFree(), /*$oldData->getManufacturer(),*/ $property);//,'1679','text');ws
            $updateditems .= 'Includes Free<br>';
        }
//update MetaTitle
        if(!(is_null($form->getMetaTitle()))) {
            $property = 'MetaTitle';
            $this->updateAttribute($form->getId(),$form->getMetaTitle(),'103','varchar');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getMetaTitle(), $oldData->getMetaTitle(),$property);
            $updateditems .= $property.'<br>';
        }
//update MetaKeywords
        if(!(is_null($form->getMetaKeywords()))) {
            $property = 'MetaKeywords';
            $this->updateAttribute($form->getId(),$form->getMetaKeywords(),'104','text');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getMetaKeywords(), $oldData->getMetaKeywords(),$property);
            $updateditems .= $property.'<br>';
        }
//update Meta Description
        if(!(is_null($form->getMetaDescription()))) {
            $property = 'MetaDescription';
            $this->updateAttribute($form->getId(),$form->getMetaDescription(),'105','varchar');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getMetaDescription(), $oldData->getMetaDescription(), /*$oldData->getManufacturer(),*/ $property);//,'105','varchar');
            $updateditems .= 'Meta Description<br>';
        }
//update status
        if(!(is_null($form->getStatus()))) {
            $property = 'status';
            $this->updateAttribute($form->getId(),$form->getStatus(),'273','int');
            $this->updateProductTable($form->getId(),$form->getStatus(),$property);
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getStatus(), $oldData->getStatus(), $property);
            $updateditems .= 'Status<br>';
        }
//update website
        if(!(is_null($form->getWebsite()))) {
            $property = 'website';
            $this->updateProductTable($form->getId(),$form->getWebsite(),$property);
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getWebsite(), $oldData->getWebsite(), $property);
            $updateditems .= $property.'<br>';
        }
//update special price
//        if(!(is_null($form->getSpecialPrice()))) {
//            $property = 'Special Price';
//            $this->updateAttribute($form->getId(),$form->getSpecialPrice(),'567','decimal');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getSpecialPrice(), $oldData->getSpecialPrice(), $property);
//            $updateditems .= $property.'<br>';
//        }
//update manufacturer
        if(array_key_exists('option',$form->getManufacturer())) {
            $property = 'Manufacturer';
            $this->updateAttribute($form->getId(),$form->getManufacturer()['option'],'102','int');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getManufacturer()['option'], $oldData->getManufacturer()['option'], $property);
            $updateditems .= 'Manufacturer<br>';
        }
//update Brand
        if(array_key_exists('option',$form->getBrand())) {
            $property = 'Brand';
            $this->updateAttribute($form->getId(),$form->getBrand()['option'],'1641','int');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getBrand()['option'], $oldData->getBrand()['option'], $property);
            $updateditems .= $property.'<br>';
        }
//update visibility
        if(array_key_exists('option', $form->getVisibility())) {
            $property = 'Visibility';
            $this->updateAttribute($form->getId(),$form->getVisibility()['option'],'526','int');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getVisibility()['option'], $oldData->getVisibility()['option'], $property);
            $updateditems .= 'Visibility<br>';
        }
//update tax class
        if(array_key_exists('option', $form->getTaxClass())) {
            $property = 'Tax Class';
            $this->updateAttribute($form->getId(),$form->getTaxClass()['option'],'274','int');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getTaxClass()['option'], $oldData->getTaxClass()['option'], $property);
            $updateditems .= 'Tax Class<br>';
        }
//update Condition
        if(array_key_exists('option', $form->getCondition())) {
            $property = 'Condition';
            $this->updateAttribute($form->getId(),$form->getCondition()['option'],'1655','int');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getCondition()['option'], $oldData->getCondition()['option'], $property);
            $updateditems .= 'Condition<br>';
        }
//update Custom Stock Status
        if(array_key_exists('option', $form->getStockStatus())) {
            $property = 'Custom Stock Status';
            $this->updateAttribute($form->getId(),$form->getStockStatus()['option'],'1661','int');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getStockStatus()['option'], $oldData->getStockStatus()['option'], $property);
            $updateditems .= 'Custom Stock Status<br>';
        }
//update Original Content
        if(array_key_exists('option',$form->getOriginalContent())) {
            $property = 'original content';
            $this->updateAttribute($form->getId(),$form->getOriginalContent()['option'],'1659','int');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getOriginalContent()['option'], $oldData->getOriginalContent()['option'], $property);
            $updateditems .= 'Original Content<br>';
        }
//update Content Reviewed
        if(array_key_exists('option',$form->getContentReviewed())) {
            $property = 'content reviewed';
            $this->updateAttribute($form->getId(),$form->getContentReviewed()['option'],'1676','int');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getContentReviewed()['option'], $oldData->getContentReviewed()['option'], /*$oldData->getManufacturer(),*/ $property);//,'1676','int');
            $updateditems .= 'Content Reviewed<br>';
        }
//update Short Description
        if(!(is_null($form->getShortDescription()))) {
            $property = 'short description';
            $this->updateAttribute($form->getId(),$form->getShortDescription(),'506','text');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getShortDescription(), $oldData->getShortDescription(), $property);
            $updateditems .= 'Short Description<br>';
        }
//update Zoom Focal Length
        if(array_key_exists('option', $form->getZoomFocalLength())) {
            $property = 'Zoom Focal Length';
            $this->updateAttribute($form->getId(),$form->getZoomFocalLength()['option'],'1731','int');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getZoomFocalLength()['option'], $oldData->getZoomFocalLength()['option'], $property);
            $updateditems .= 'Zoom Focal Length<br>';
        }
//update Prime Focal Length
        if(array_key_exists('option', $form->getPrimeFocalLength())) {
            $property = 'Prime Focal Length';
            $this->updateAttribute($form->getId(),$form->getPrimeFocalLength()['option'],'1713','int');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getPrimeFocalLength()['option'], $oldData->getPrimeFocalLength()['option'], $property);
            $updateditems .= 'Prime Focal Length<br>';
        }
//update Aperture
        if(array_key_exists('option', $form->getAperture())) {
            $property = 'Aperture';
            $this->updateAttribute($form->getId(),$form->getAperture()['option'],'1715','int');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getAperture()['option'], $oldData->getAperture()['option'], $property);
            $updateditems .= 'Aperture<br>';
        }
//update Camera Style
        if(array_key_exists('option', $form->getCameraStyle())) {
            $property = 'Camera Style';
            $this->updateAttribute($form->getId(),$form->getCameraStyle()['option'],'1717','int');
//            $this->insertLogging($form->getId(), $oldData->getSku(), $form->getCameraStyle()['option'], $oldData->getCameraStyle()['option'], $property);
            $updateditems .= 'Camera Style<br>';
        }


//update Images
        if(!(is_null($form->getImageGallery()))) {
            $imageHandler = new ImageTable($this->adapter);
            foreach($form->getImageGallery() as  $value){
                $imageHandler->updateImage($value);
                //$updateditems .= $result;
                //$this->insertLogging($form->getId(), $oldData->getSku(), $form->getAperture()['option'], $oldData->getAperture()['option'], $property);
            }
            if(count($form->getImageGallery())>0){
                $updateditems .= count($form->getImageGallery()) .' Images Updated';
            }
        }

//update accessories
        if(!(is_null($form->getAccessories()))) {
            $accessoryHandler = new AccessoryTable($this->adapter);

            foreach($form->getAccessories() as  $value){
                $accessoryHandler->updateAccessory($value);
            }
            if(count($form->getAccessories())>0){
                $updateditems .= count($form->getAccessories()) .' Accessories Updated';
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
    public function newHandle(Form $form,Form $oldData){

        $inserteditems= '';
        $startMessage = 'The following fields have been inserted :<br>';
//Title
        if(!(is_null($form->getTitle()))) {
            $property = 'Title';
            $this->insertAttribute($oldData->getId(),$form->gettitle(),'96','varchar');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getTitle(), "",$property);
            $inserteditems .= $property.'<br>';
        }
//Includes Free
        if(!(is_null($form->getIncludesFree()))) {
            $property = 'Includes Free';
            $this->insertAttribute($oldData->getId(),$form->getIncludesFree(),'1679','text');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getIncludesFree(), "",$property);
            $inserteditems .= $property.'<br>';
        }
//Description
        if(!(is_null($form->getDescription()))) {
            $property = 'Description';
            $this->insertAttribute($oldData->getId(),$form->getDescription(),'97','text');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getDescription(), "",$property);
            $inserteditems .= $property.'<br>';
        }
//Short Description
        if(!(is_null($form->getShortDescription()))) {
            $property = 'Short Description';
            $this->insertAttribute($oldData->getId(),$form->getShortDescription(),'506','text');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getShortDescription(), "",$property);
            $inserteditems .= $property.'<br>';
        }
//in box
        if(!(is_null($form->getInBox()))) {
            $property = 'In Box';
            $this->insertAttribute($oldData->getId(),$form->getInBox(),'1633','text');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getInBox(), "",$property);
            $inserteditems .= $property.'<br>';
        }
//Special Price
//        if(!(is_null($form->getSpecialPrice()))) {
//            $property = 'Special Price';
//            $this->insertAttribute($oldData->getId(),$form->getSpecialPrice(),'567','decimal');
//            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getSpecialPrice(), "",$property);
//            $inserteditems .= $property.'<br>';
//        }
//Metakeywords
        if(!(is_null($form->getMetaKeywords()))) {
            $property = 'MetaKeywords';
            $this->insertAttribute($oldData->getId(),$form->getMetaKeywords(),'104','text');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getMetaKeywords(), "",$property);
            $inserteditems .= $property.'<br>';
        }
//MetaTitle
        if(!(is_null($form->getMetaTitle()))) {
            $property = 'MetaTitle';
            $this->insertAttribute($oldData->getId(),$form->getMetaTitle(),'103','varchar');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getMetaTitle(), "",$property);
            $inserteditems .= $property.'<br>';
        }
//MetaDescription
        if(!(is_null($form->getMetaDescription()))) {
            $property = 'MetaDescription';
            $this->insertAttribute($oldData->getId(),$form->getMetaDescription(),'105','varchar');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getMetaDescription(), "",$property);
            $inserteditems .= $property.'<br>';
        }
//Manufacturer
        if(array_key_exists('option', $form->getManufacturer())) {
            $property = 'Manufacturer';
            $this->insertAttribute($oldData->getId(),$form->getManufacturer()['option'],'102','int');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getManufacturer()['option'], "",$property);
            $inserteditems .= $property.'<br>';
        }
//Brand
        if(array_key_exists('option', $form->getBrand())) {
            $property = 'Brand';
            $this->insertAttribute($oldData->getId(),$form->getBrand()['option'],'1641','int');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getBrand()['option'], "",$property);
            $inserteditems .= $property.'<br>';
        }
//Status
        if(!(is_null($form->getStatus()))) {
            $property = 'Status';
            $this->insertAttribute($oldData->getId(),$form->getStatus(),'273','int');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getStatus(), "",$property);
            $inserteditems .= $property.'<br>';
        }
//Custom Stock Status
        if(array_key_exists('option', $form->getStockStatus())) {
            $property = 'Custom Stock Status';
            $this->insertAttribute($oldData->getId(),$form->getStockStatus()['option'],'1661','int');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getStockStatus()['option'], "",$property);
            $inserteditems .= $property.'<br>';
        }
//Tax Class
        if(array_key_exists('option', $form->getTaxClass())) {
            $property = 'Tax Class';
            $this->insertAttribute($oldData->getId(),$form->getTaxClass()['option'],'274','int');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getTaxClass()['option'], "",$property);
            $inserteditems .= $property.'<br>';
        }
//Condition
        if(array_key_exists('option', $form->getCondition())) {
            $property = 'Condition';
            $this->insertAttribute($oldData->getId(),$form->getCondition()['option'],'1655','int');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getCondition()['option'], "",$property);
            $inserteditems .= $property.'<br>';
        }
//Visibility
        if(array_key_exists('option', $form->getVisibility())) {
            $property = 'Visibility';
            $this->insertAttribute($oldData->getId(),$form->getVisibility()['option'],'526','int');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getVisibility()['option'], "",$property);
            $inserteditems .= $property.'<br>';
        }
//Zoom Focal Length
        if(array_key_exists('option', $form->getZoomFocalLength())) {
            $property = 'Zoom Focal Length';
            $this->insertAttribute($oldData->getId(),$form->getZoomFocalLength()['option'],'1731','int');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getZoomFocalLength()['option'], "",$property);
            $inserteditems .= $property.'<br>';
        }
//Prime focal length
        if(array_key_exists('option', $form->getPrimeFocalLength())) {
            $property = 'Prime Focal Length';
            $this->insertAttribute($oldData->getId(),$form->getPrimeFocalLength()['option'],'1713','int');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getPrimeFocalLength()['option'], "",$property);
            $inserteditems .= $property.'<br>';
        }
//Camera Style
        if(array_key_exists('option', $form->getCameraStyle())) {
            $property = 'Camera Style';
            $this->insertAttribute($oldData->getId(),$form->getCameraStyle()['option'],'1717','int');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getCameraStyle()['option'], "",$property);
            $inserteditems .= $property.'<br>';
        }
//Apeture
        if(array_key_exists('option', $form->getAperture())) {
            $property = 'Apeture';
            $this->insertAttribute($oldData->getId(),$form->getAperture()['option'],'1715','int');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getAperture()['option'], "",$property);
            $inserteditems .= $property.'<br>';
        }
//Original Content
        if(array_key_exists('option', $form->getOriginalContent())) {
            $property = 'Original Content';
            $this->insertAttribute($oldData->getId(),$form->getOriginalContent()['option'],'1659','int');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getOriginalContent()['option'], "",$property);
            $inserteditems .= $property.'<br>';
        }
//Content Reviewed
        if(array_key_exists('option', $form->getContentReviewed())) {
            $property = 'Content Reviewed';
            $this->insertAttribute($oldData->getId(),$form->getContentReviewed()['option'],'1676','int');
            $this->insertLogging($oldData->getId(),$oldData->getSku(), $form->getContentReviewed()['option'], "",$property);
            $inserteditems .= $property.'<br>';
        }

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

//Add new Accessory
        if(!(is_null($form->getAccessories()))) {
            $accessoryHandler = new AccessoryTable($this->adapter);
            $accessory = $form->getAccessories();
            foreach($accessory as  $value){
                $accessoryHandler->addAccessory($value);
            }
            if(count($accessory)>0){
                $inserteditems .= count($accessory). ' Accessories Added';
            }
        }


        if($inserteditems != ''){
            $inserteditems = $startMessage.$inserteditems;
        }


        return $inserteditems;
    }

    /**
     * update status in product table
     */
    public function updateProductTable($entityid,$value,$property){
        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];
        $update = $this->sql->update('product')
            ->set(array($property => $value, 'changedby' => $user, 'lastModifiedDate'=>date('Y-m-d h:i:s')))
            ->where(array('entity_id ='.$entityid));
        $statement = $this->sql->prepareStatementForSqlObject($update);
        return $statement->execute();
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

    public function insertAttribute($entityid,$value,$attributeid,$tableType){
        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];

        $insert = $this->sql->insert('productattribute_'.$tableType);
        $insert->columns(array('entity_id','category_id','dataState','changedby'));
        $insert->values(array(
            'entity_id' => $entityid,
            'attribute_id' => $attributeid,
            'value' => $value,
            'dataState' => 1,
            'changedby' => $user
        ));

        $statement = $this->sql->prepareStatementForSqlObject($insert);
        $statement->execute();

    }


    public function rinseHandle(Form $form){
        $rinsedItems = '';

//remove categories
        if(!(is_null($form->getCategories()))) {

            $categoryHandler = new CategoryTable($this->adapter);
            $category = $form->getCategories();

            foreach($category as  $value){
                $result=$categoryHandler->removeCategory($value,$form->getId());
                $rinsedItems .= $result;
            }
        }

//remove Accessories
        if(!(is_null($form->getAccessories()))) {

            $accessoriesHandler = new AccessoryTable($this->adapter);
            $accessory = $form->getAccessories();

            foreach($accessory as  $value){
                $accessoriesHandler->removeAccessory($value);
            }
            if(count($accessory)>0){
                $rinsedItems .= count($accessory).' Accessories Removed';
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

    public function insertLogging($entityid, $sku ,$newValue, $oldValue, $property)
    {
        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];

        $fieldValueMap = array(
            'entity_id' =>  $entityid,
            'sku'   =>  $sku,
            'oldvalue'  =>  $oldValue,
            'newvalue'  =>  $newValue,
            'datechanged'   => date('Y-m-d h:i:s'),
            'changedby' =>  $user,
            'property'  =>  $property,
        );

//        $eventWritables = array('dbAdapter'=> $this->adapter, 'extra'=> $fieldValueMap);
//        $this->getEventManager()->trigger('construct_sku_log', null, array('makeFields'=>$eventWritables));
    }
}

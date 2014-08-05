<?php

namespace Search\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Session\Container;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;
//use Zend\Db\Sql\Select;
//use Search\Helper\FormatFields;

class FormTable{

    protected $sku;
    protected $select = Null;
    protected $sql;
    protected $skuFields = array();
    protected $form;

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
        $result[array_keys($newAttibute)[0]] = array(current($newAttibute)=>current($newOption));

        //Fetch Brand Option
        $newAttibute = $this->fetchAttribute($entityid,'int','1641','brand');
        $newOption = $this->fetchOption(current($newAttibute),'1641','brand');
        $result[array_keys($newAttibute)[0]] = array(current($newAttibute)=>current($newOption));

        return $result;
    }

    public function fetchAttribute($entityid,$tableType, $attributeid ,$property){
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

/*
 * This should be refactored to less granular
 */

    public function executeQuery(){
        $statement = $this->sql->prepareStatementForSqlObject($this->select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
//        This is my query.
//        var_dump($resultSet);
        return $resultSet;
    }

    public function isSkuValid(ResultSet $result){
        if(!$result->valid()){
            return False;
        }
        return true;
    }

    public function isSelect(){
        if( is_null($this->select) ) {
            $this->select = $this->sql->select();
            $this->selectQuery();
        }
        return $this->select;

    }

    public function selectQuery(){
        $this->select->from('product')
            ->where(
                array(
                    'productid' => $this->sku
                )
            );
    }

    /**
     * @param $sku
     * @throws \Exception
     * @return int
     */
    public function validateSku($sku){
        $this->sku = $sku;
        $this->isSelect();
        $resultSet = $this->executeQuery();
        if( !$this->isSkuValid($resultSet) ){
            return false;
        }
        $skuList = array();
        $skuList = $resultSet->current();
        return $skuList['entity_id'];
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

        var_dump($statement);
        $result = $statement->execute();
        echo 'haha';

        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
//        var_dump($resultSet);
//        echo 'haha';
        return $select->getSqlString();
        die();

        $searchResults = $resultSet->toArray();
        return $searchResults;
    }



    /**
     * Handle isDirty Form entities
     */
    public function dirtyHandle(Form $form){
        //Find Dirty properties and call corresponding updates
        $startMessage = 'The following fields have been updated :<br>';
        $updateditems = '';

        //update sku
        //update Title
        if(!(is_null($form->getTitle()))) {
            $this->updateAttribute($form->getId(),$form->getTitle(),'96','varchar');
            $updateditems .= 'Title<br>';
        }
        //update description
        if(!(is_null($form->getDescription()))) {
            $this->updateAttribute($form->getId(),$form->getDescription(),'97','text');
            $updateditems .= 'Description<br>';
        }
        //update inventory
        //update url Key
        //update status
        if(!(is_null($form->getStatus()))) {
            $this->updateAttribute($form->getId(),$form->getStatus(),'273','int');
            $updateditems .= 'Status<br>';
        }
        //update manufacturer
        //update visibility
        if(!(is_null($form->getVisibility()))) {
            $this->updateAttribute($form->getId(),$form->getVisibility(),'526','int');
            $updateditems .= 'Visibility<br>';
        }
        //update condition
        //update tax class
        //update stock status
        if(!(is_null($form->getStockStatus()))) {
            $this->updateAttribute($form->getId(),$form->getStockStatus(),'1661','int');
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
            $this->updateAttribute($form->getId(),$form->getInBox(),'1633','text');
            $updateditems .= 'In Box<br>';
        }

        //update Includes Free
        if(!(is_null($form->getIncludesFree()))) {
            $this->updateAttribute($form->getId(),$form->getIncludesFree(),'1679','text');
            $updateditems .= 'Includes Free<br>';
        }

        //update Meta Description
        if(!(is_null($form->getMetaDescription()))) {
            $this->updateAttribute($form->getId(),$form->getMetaDescription(),'105','varchar');
            $updateditems .= 'Meta Description<br>';
        }

        //update Original Content
        if(!(is_null($form->getOriginalContent()))) {
            $this->updateAttribute($form->getId(),$form->getOriginalContent(),'1659','int');
            $updateditems .= 'Original Content<br>';
        }

        //update Content Reviewed
        if(!(is_null($form->getContentReviewed()))) {
            $this->updateAttribute($form->getId(),$form->getContentReviewed(),'1676','int');
            $updateditems .= 'Content Reviewed<br>';
        }

        //update Short Description
        if(!(is_null($form->getShortDescription()))) {
            $this->updateAttribute($form->getId(),$form->getShortDescription(),'506','text');
            $updateditems .= 'Visibility<br>';
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
        //Find New properties and call corresponding inserts
    }

    public function updateAttribute($entityid,$value,$attributeid,$tableType){

        $loginSession= new Container('login');
        $userData = $loginSession->sessionDataforUser;
        $user = $userData['userid'];

        $update = $this->sql->update('productattribute_'.$tableType)->set(array('value' => $value,'dataState' => '1', 'changedby' => $user))->where(array('entity_id ='.$entityid, 'attribute_id ='.$attributeid));
        $statement = $this->sql->prepareStatementForSqlObject($update);
        return $statement->execute();

    }
}

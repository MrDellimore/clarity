<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 7/17/14
 * Time: 5:10 PM
 */

namespace Api\Magento\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;
use Content\ContentForm\Tables\Spex;
use Zend\Soap\Client;
use Zend\Db\Sql\Expression;

class MagentoTable {

    use EventManagerAwareTrait;

    protected $adapter;

    protected $totaltime;

    protected $sql;

    protected $dirtyCount;

    protected $attributeDirtyCount = 0;

    protected $dirtyItems;

    protected $imgPk = array();

    /*$catalogInventoryStockItemUpdateEntity*/
    protected $stockData  = [
        'qty'=>'qty',
        'is_in_stock'=> 'is_in_stock',
    ];

    use Spex;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    public function fetchImages()
    {

        return $this->productAttribute($this->sql,array(),array('dataState'=>2),'images')->toArray();
    }

    public function fetchCleanCount()
    {
        $select = $this->sql->select();
        $select->from('product');
        $select->columns(array('id' => 'entity_id', 'ldate'=>'lastModifiedDate', 'item' => 'productid'));
        $select->where(array( 'dataState' => '0'));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet->count();
    }

    public function fetchNewCount()
    {
        $select = $this->sql->select();
        $select->from('product');
        $select->where(array( 'dataState' => '2'));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet->count();
    }

    public function fetchImageCount()
    {
        $select = $this->sql->select()->from('productattribute_images')->where(['dataState'=>2]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        return $resultSet->count();
    }

    public function fetchDirtyProducts($changedProducts = Null)
    {
        $select = $this->sql->select();
        $select->from('product');
        $select->columns(['id' => 'entity_id', 'sku' => 'productid', 'website' => 'website']);
//        $select->join(array('u' => 'users'),'u.userid = product.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'));
        $filter = new Where;
        $filter->in('product.entity_id',$changedProducts);
        $filter->equalTo('product.dataState',1);
//        $select->where(array( 'dataState' => '1'));
        $select->where($filter);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $product = $resultSet->toArray();
        $soapUpdate = [];
        $startCount = 0;
//        foreach( $changedProducts as $key => $checkedProducts )
//        var_dump($product);
            foreach( $product as $index => $prd){
                    $lookup = $this->productAttributeLookup($this->sql);
                    foreach($lookup as $key => $attributes){
                        $dataType = (string)$attributes['dataType'];
                        $attributeId = (int)$attributes['attId'];
                        $attributeCode = (string)$attributes['attCode'];
        //TODO have to add for changing product sku when the soap call goes through in case admin wants to change the sku in mage.
                        $productAttributeSelect = $this->sql->select()->from('productattribute_'.$dataType)
                                                                      ->columns([$attributeCode=>'value'])
                                                                      ->where(['attribute_id'=>$attributeId, 'entity_id'=>$prd['id'], 'dataState'=>1]);
                        $prdStatement = $this->sql->prepareStatementForSqlObject($productAttributeSelect);
                        $prdResult = $prdStatement->execute();

                        $attSet = new ResultSet;
                        if ($result instanceof ResultInterface && $result->isQueryResult()) {
                            $attSet->initialize($prdResult);
                        }
                        $productAttributeResults = $attSet->toArray();
                        foreach( $productAttributeResults as $ind => $value ){
                            $soapUpdate[$startCount]['id'] = $prd['id'];
                            $soapUpdate[$startCount]['sku'] = $prd['sku'];
        //                    $soapUpdate[$startCount]['website'] = [$prd['website']];
                            $soapUpdate[$startCount][$attributeCode] =$value[$attributeCode];
                        }
                    }
                    $startCount++;
            }
//        var_dump($soapUpdate);
//        die();
        return $soapUpdate;
    }

    public function fetchChangedProducts()
    {
        $select = $this->sql->select();
        $select->from('product');
        $select->columns(array('id' => 'entity_id', 'sku' => 'productid', 'ldate'=>'lastModifiedDate', 'item' => 'productid'));
        $select->join(array('u' => 'users'),'u.userid = product.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'));

        $select->where(array( 'dataState' => '1'));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $dirtyCount = $resultSet->count();
        $this->setDirtyCount($dirtyCount);
        $result = $resultSet->toArray();

        $results = $this->productAttributeLookup($this->sql);
        foreach($results as $key => $attributes){
            $dataType = $attributes['dataType'];
            $attributeId = $attributes['attId'];
            $attributeCode = $attributes['attCode'] === 'name' ? 'title' : $attributes['attCode'];
            $newAttribute = $this->fetchAttribute( $dataType,$attributeId,$attributeCode);
            if(is_array($newAttribute)){
                foreach($newAttribute as $newAtt){
                    $result[] = $newAtt;
                }
            }
        }

        $this->setDirtyItems($this->getDirtyCount(), $this->getAggregateAttributeDirtyCount());
        return $result;
    }

//    TODO have to change this to fetchChangedProductsCount instead.
    public function setDirtyItems($dirtyProducts, $dirtyAttributes)
    {
        $this->dirtyItems = $dirtyProducts + $dirtyAttributes;
    }

    public function getDirtyItems()
    {
        return $this->dirtyItems;
    }


    public function getAggregateAttributeDirtyCount()
    {
        return $this->attributeDirtyCount;
    }

    public function setAggregateAttributeDirtyCount($attributeDirtyCount)
    {
        $this->attributeDirtyCount += $attributeDirtyCount;
    }

    public function fetchLinkedProducts()
    {
        $select = $this->sql->select();
        $filter = new Where();
        $filter->in('productlink.dataState',array(2,3));
        $select->from('productlink')
            ->columns(array('entityId'=>'entity_id','linkedEntityId'=>'linked_entity_id', 'dataState'=>'dataState'))
            ->join( array('t'=>'productlink_type'), 't.link_type_id=productlink.link_type_id',array('type'=>'code'))
            ->join( array('p'=>'product'), 'p.entity_id=productlink.entity_id',array('sku'=>'productid'), Select::JOIN_LEFT)
//               ->where(array('productcategory.dataState'=>2,'productcategory.dataState'=>3),PredicateSet::OP_OR);
            ->where($filter);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        //TODO have to implement a count feature for this.
//        $resultSet->count()
        $linkedProducts = $resultSet->toArray();
//        var_dump($linkedProducts);
//        die();
        return $linkedProducts;
    }

    public function updateLinkedProductstoClean($linkedProducts)
    {
//        var_dump($linkedProducts);
        $dataState = (int)$linkedProducts['dataState'];
        if ( $dataState === 3 ) {
            $delete = $this->sql->delete('productlink');
            $delete->where(array('entity_id'=>$linkedProducts['entityId'], 'linked_entity_id'=>$linkedProducts['linkedEntityId']));
            $statement = $this->sql->prepareStatementForSqlObject($delete);
            $result = $statement->execute();
        } else {
            $update = $this->sql->update('productlink');
            $update->set(array('dataState'=>0))
                ->where(array('entity_id'=>$linkedProducts['entityId'], 'linked_entity_id'=>$linkedProducts['linkedEntityId']));
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $result = $statement->execute();
        }
        return $result;
    }

    public function updateProductCategoriesToClean($cats)
    {
        $dataState = (int)$cats['dataState'];
        if( $dataState === 2 ){
            $update = $this->sql->update('productcategory')->set(['dataState'=>0])->where(['entity_id'=>$cats['entityId'], 'category_id'=>$cats['categortyId']]);
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $result = $statement->execute();
        }
        if( $dataState === 3 ){
            $delete = $this->sql->delete('productcategory');
            $delete->where(['entity_id'=>$cats['entityId'], 'category_id'=>$cats['categortyId']]);
            $statement = $this->sql->prepareStatementForSqlObject($delete);
            $result = $statement->execute();
        }
        return $result;
    }

    public function fetchAttribute($tableType, $attributeid, $property)
    {
        $select = $this->sql->select();

        $select->from('productattribute_'.$tableType);

        $select->columns(array('id'=>'entity_id', $property => 'value', 'ldate' => 'lastModifiedDate'));
        $select->join(array('p' => 'product'),'p.entity_id = productattribute_'.$tableType. ' .entity_id ' ,array('item' => 'productid'));
        $select->join(array('u' => 'users'),'u.userid = productattribute_'.$tableType. ' .changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'));
        $select->where(array( 'attribute_id' => $attributeid, 'productattribute_'.$tableType. '.dataState'=> '1'));

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $this->setAggregateAttributeDirtyCount($resultSet->count());
        $result = $resultSet->toArray();

        //check if array passed or value given
        if(!(is_array($result)) || current($result)[$property] == ''){
            $result = null;

        }

        return $result;
    }


    public function setDirtyCount($dirtyCount)
    {
        $this->dirtyCount = $dirtyCount;
    }

    public function getDirtyCount()
    {
        return $this->dirtyCount;
    }


    public function fetchChangedCategories()
    {
        $select = $this->sql->select();
        $filter = new Where();
        $filter->in('productcategory.dataState',array(2,3));
        $select->from('productcategory')
               ->columns(array('entityId'=>'entity_id','categortyId'=>'category_id', 'dataState'=>'dataState'))
               ->join( array('p'=>'product'), 'p.entity_id=productcategory.entity_id',array('sku'=>'productid'))
               ->where($filter);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        //TODO have to implement a count feature for this.
        $categories = $resultSet->toArray();
//        var_dump($categories);
//        die();
        return $categories;
    }

    public function updateImagesToClean($images)
    {
        $result ='';
        foreach($images as $image){
            $update = $this->sql->update('productattribute_images')->set(['dataState'=>0])->where(['value_id'=>$image['value_id']]);
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $result = $statement->execute();
        }
        return $result;
    }

    public function updateToClean($changedProducts)
    {
        $results = '';
        $attributes = array_keys($changedProducts);
        $entityId = $changedProducts['id'];
        $sku = $changedProducts['sku'];
        $updateProduct = $this->sql->update('product')->set(['dataState'=>0])->where(['productid'=>$sku]);
        $statement = $this->sql->prepareStatementForSqlObject($updateProduct);
        $statement->execute();
        array_shift($attributes);
        array_shift($attributes);
        array_shift($changedProducts);
        array_shift($changedProducts);
        foreach( $attributes as $att ) {
            $lookup = $this->productAttributeLookup($this->sql, ['attribute_code'=>$att]);
            $attributeId = $lookup[0]['attId'];
            $dataType = $lookup[0]['dataType'];
            $updateProductAttribute = $this->sql->update('productattribute_'.$dataType)->set(['dataState'=>0])->where(['entity_id'=>$entityId,'attribute_id'=>$attributeId]);
            $prdAttStatement = $this->sql->prepareStatementForSqlObject($updateProductAttribute);
            $prdAttStatement->execute();
        }
        $results .= $sku . " has been updated in Magento Admin";
        return $results;
    }


    public function fetchNewItems()
    {
        //fetches all attribute codes from look up table and looks them up in corresponding attribute tables only if they are new.
        $soapBundle = $optionValues = [];
        $select = $this->sql->select()->from('product')->columns([
            'entityId'      =>  'entity_id',
            'sku'           =>  'productid',
            'productType'   =>  'product_type',
            'website'       =>  'website',
            'dateCreated'   =>  'creationdate',
        ]);
        //->where(array('product.dataState'=>0))->quantifier(Select::QUANTIFIER_DISTINCT);
        $filter = new Where;
        $filter->in('product.dataState',array(2));
        $select->where($filter);

        $statusIntJoin = new Expression('i.entity_id = product.entity_id and i.attribute_id = 273');
        $select->join(['i'=>'productattribute_int'],$statusIntJoin ,['status'=>'value'] ,Select::JOIN_LEFT);
//        $statusOptionJoin = new Expression('o.attribute_id = i.attribute_id and o.value = i.option');
//        $select->join(['o'=>'productattribute_option'],$statusOptionJoin ,['Status'=>'value'] ,Select::JOIN_LEFT);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $products = $resultSet->toArray();
//          $productSku = [
//              'PDPPBRBR','PLPPBRBR','PLPPGYBK','PMPPBRBR','PMPPGYBK','SEPPBRGD','SEPPGYGM','SEPPGYMSV','SSPPGNGD','SSPPGYMSV'
//          ];
//        $productSku = ['AV22303B06'];
//        statically add products skus here if more are requested.
//        $skuCount = count($productSku);
//        echo $skuCount . '<br />';
        $startCount = 0;
//        for( $i = 0; $i < $skuCount; $i++ ) {
            foreach($products as $index => $value) {
//                if($productSku[$i] == $value['sku']) {
                    $entityId = $products[$index]['entityId'];
                    $attributes = $this->productAttributeLookup($this->sql);
                    foreach( $attributes as $key => $attribute ) {
                        $tableType = (string)$attribute['dataType'];
                        $attributeId = (int)$attribute['attId'];
                        $attributeCode = $attribute['attCode'];
                        $selectAtts = $this->sql->select()->from('productattribute_'. $tableType)
                                                          ->columns([$attributeCode=>'value', 'attId'=>'attribute_id']);
//                                                          ->where(['entity_id'=>$entityId,'attribute_id'=>$attributeId]);
                        $filterAttributes = new Where;
//                        $filterAttributes->equalTo('productattribute_'.$tableType.'.dataState',0);
//                        $filterAttributes->equalTo('productattribute_'.$tableType.'.dataState',2);
                        $filterAttributes->equalTo('productattribute_'.$tableType.'.entity_id',$entityId);
                        $filterAttributes->equalTo('productattribute_'.$tableType.'.attribute_id',$attributeId);
                        $filterAttributes->in('productattribute_'.$tableType.'.dataState',array(2));
                        $selectAtts->where($filterAttributes);
                        $attStatement = $this->sql->prepareStatementForSqlObject($selectAtts);
                        $attResult = $attStatement->execute();
                        $attSet = new ResultSet;
                        if ($attResult instanceof ResultInterface && $attResult->isQueryResult()) {
                            $attSet->initialize($attResult);
                        }
                        $attributeValues = $attSet->toArray();
                        foreach($attributeValues as $keyValue => $valueOption) {
//                            if ( $products[$index]['sku'] == 'BOSE359037-1300' ) {
//                                echo $products[$index]['sku'] . ' ' . $attributeCode . '<br />';
//                            }
                            $soapBundle[$startCount]['sku'] = $products[$index]['sku'];
                            $soapBundle[$startCount]['website'] = $products[$index]['website'];
                            $soapBundle[$startCount]['status'] = (is_null($products[$index]['status'])) ? 2 : $products[$index]['status'];
                            if ( array_key_exists($attributeCode,$this->stockData) ) {
                                $soapBundle[$startCount]['stock_data'][$attributeCode] = $attributeValues[$keyValue][$attributeCode];
                            } else {
                                if( isset($attributeValues[$keyValue][$attributeCode]) ){
                                    $soapBundle[$startCount][$attributeCode] = $attributeValues[$keyValue][$attributeCode];
                                }
                            }
                        }
                    }
                    $startCount++;
//                }
            }
//        }
//        echo '<pre>';
//        var_dump($soapBundle);
//        die();
        return $soapBundle;
    }



    public function updateNewProduct( $oldEntity, $newEntity )
    {
        $select = $this->sql->select()->from('product')->columns(['entityId'=>'entity_id'])->where(['productid'=>$oldEntity['sku']]);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $response = $statement->execute();
        $resultSet = new ResultSet;
        if ($response instanceof ResultInterface && $response->isQueryResult()) {
            $resultSet->initialize($response);
        }
        $oEntityId = $resultSet->toArray();
        $oeid = $oEntityId[0]['entityId'];
        $updateProduct = $this->sql->update('product')->set(['entity_id'=>$newEntity, 'dataState'=>0 ])->where(['productid'=>$oldEntity['sku']]);
        $prdStmt = $this->sql->prepareStatementForSqlObject($updateProduct);
        $response = $prdStmt->execute();
        array_shift($oldEntity);
        array_shift($oldEntity);
        foreach( $oldEntity as $attributeCode => $attributeValue ) {

            $lookupVals = $this->productAttributeLookup($this->sql, ['attribute_code'=>$attributeCode] );
            if( !empty($lookupVals[0]) ) {
                $attributeId = $lookupVals[0]['attId'];
                $dataType = $lookupVals[0]['dataType'];
//                echo $dataType . ' ' . $attributeCode . ' ' . $newEntity . ' ' . $attributeId . ' ' . $oeid . '<br />';
                $update = $this->sql->update('productattribute_'.$dataType)->set(['entity_id'=>$newEntity, 'dataState'=>0])->where(['attribute_id'=>$attributeId, 'entity_id'=>$oeid]);
                $stmt = $this->sql->prepareStatementForSqlObject($update);
                $attributeResp = $stmt->execute();

//                $attSet = new ResultSet;
//                if ($attResponse instanceof ResultInterface && $attResponse->isQueryResult()) {
//                    $attSet->initialize($attResponse);
//                }
//                $attributeValues = $attSet->toArray();
            }
//            if( $dataType == 'int' ) {
//                $option = $this->sql->select()->from('productattribute_'.$dataType)->columns(['option'=>'value'])->where(['entity_id'=>$oeid,'attribute_id'=>$attributeId]);
//                $opStmt = $this->sql->prepareStatementForSqlObject($option);
//                $opResp = $opStmt->execute();
//                $opSet = new ResultSet;
//                if ($opResp instanceof ResultInterface && $opResp->isQueryResult()) {
//                    $opSet->initialize($opResp);
//                }
//                $op = $opSet->toArray();
//                if( !empty($op) ) {
//                    $opUpdate = $this->sql->update('productattribute_option')->set(['dataState'=>0])->where(['attribute_id'=>$attributeId, 'option_id'=>$op[0]['option']]);
//                    $opStmt = $this->sql->prepareStatementForSqlObject($opUpdate);
//                    $opStmt->execute();
//                }
//            }
        }
        return $attributeResp;
    }

    public function validateSkuExists($newProducts ,$mageEntityId)
    {
        $dupEntityIdExists = $this->sql->select()->from('product')->where(['entity_id'=>$mageEntityId]);
        $dupStatement = $this->sql->prepareStatementForSqlObject($dupEntityIdExists);
        $dupResponse = $dupStatement->execute();
        $dupSet = new ResultSet;
        if ($dupResponse instanceof ResultInterface && $dupResponse->isQueryResult()) {
            $dupSet->initialize($dupResponse);
        }
        $id = $dupSet->toArray();
        if( count($id) ) {
            $entityId = $this->adapter->query('Select max(entity_id) from product', Adapter::QUERY_MODE_EXECUTE);
            foreach( $entityId as $eid ) {
                foreach( $eid as $maxEntityID ) {
                    $newEntityId = $maxEntityID + 1;
                    $response = $this->updateNewProduct( $newProducts, $newEntityId );
                }
            }
        } else {
            $response = $this->updateNewProduct($newProducts, $mageEntityId);
        }
        return $response;
    }

    public function updateNewItemsToClean($newProducts, $mageEntityId)
    {
        return $this->validateSkuExists($newProducts, $mageEntityId);
    }

    public function adjustProductKeys($newProducts)
    {
        $shiftedStockData = [];
        foreach( $newProducts as $key => $acode ) {
            foreach( $acode as $index => $aValues ) {
                if( $index == 'stock_data' && isset($newProducts[$key]['stock_data'][current(array_keys($this->stockData))]) ) {
//                    TODO might have to add a foreach here for stock_data,since this will have multiple attributes within.
//                    if( isset($newProducts[$key]['stock_data'][current(array_keys($this->stockData))]) ) {
                        $oldEntityIds[$key][current(array_keys($this->stockData))] = $newProducts[$key]['stock_data'][current(array_keys($this->stockData))];
                        $shiftedAttribute = array_shift($this->stockData);
                        $shiftedStockData[$shiftedAttribute] =  $shiftedAttribute;
//                    }
                } else {
                    $oldEntityIds[$key][$index] = $newProducts[$key][$index];
                }
            }
            $this->stockData = $shiftedStockData + $this->stockData;
        }
        return $oldEntityIds;
    }

}
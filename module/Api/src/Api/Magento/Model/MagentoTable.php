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

    public function fetchNewImages($sku,$limit)
    {
        $select = $this->sql->select()
                  ->from('productattribute_images')
                  ->columns([
                            'valueid'       =>  'value_id',
                            'entityId'      =>  'entity_id',
                            'label'         =>  'label',
                            'filename'      =>  'filename',
                            'changedby'     =>  'changedby',
                            'position'      =>  'position',
                            'creation'      =>  'date_created',
                            ]);
        $select->join(['u'=>'users'], 'u.userid = productattribute_images.changedby',['fname'=>'firstname','lname'=>'lastname']);
        $select->join(['p'=>'product'], 'p.entity_id = productattribute_images.entity_id',['sku'=>'productid']);
        $filter = new Where;
        if ( $sku ){
            $filter->like('product.sku',$sku.'%');
        }
        $filter->equalTo('productattribute_images.dataState',2);
        $select->limit($limit);
        $select->where($filter);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $images = $resultSet->toArray();
        $soapCount = 0;
        $newImages = [];
        foreach( $images as $image ) {
            $newImages[$soapCount]['valueid'] = $image['valueid'];
            $newImages[$soapCount]['entityId'] = $image['entityId'];
            $newImages[$soapCount]['position'] = $image['position'];
            $newImages[$soapCount]['sku'] = $image['sku'];
            $newImages[$soapCount]['label'] = $image['label'];
            $newImages[$soapCount]['filename'] = "<img width='50' height='50' src='".$image['filename']."' />";
            $newImages[$soapCount]['creation'] = $image['creation'];
            $newImages[$soapCount]['fullname'] = $image['fname'] . ' ' . $image['lname'] ;
            $soapCount++;
        }

        return $newImages;
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

    public function orderImages($images)
    {
        $imgCount = 0;
        $soapImages = [];
        foreach ( $images as $key => $content ) {
            $soapImages[$imgCount]['imageid'] = $content['imageid'];
            $soapImages[$imgCount]['id'] = $content['id'];
            $soapImages[$imgCount]['filename'] = $content['filename'];
            $soapImages[$imgCount]['sku'] = $content['sku'];
            $soapImages[$imgCount]['label'] = $content['label'];
            $soapImages[$imgCount]['position'] = $content['position'];
            $imgCount++;
        }
        return $soapImages;
    }

    public function groupSku($checkboxSku)
    {
        $count = 0;
        $checkedIds = $checkedProperties = $grouped = $checkedValues = $checkedSku = [];
        foreach ( $checkboxSku as $key => $checkbox ) {
            $checkedIds[$count] = $checkbox['id'];
            $checkedProperties[$count] = $checkbox['property'];
            $checkedValues[$count] = $checkbox['newValue'];
            $checkedSku[$count] = $checkbox['sku'];
            $count++;
        }
        $uniqueIds = array_values(array_unique($checkedIds));
        foreach ($uniqueIds as $key => $uids) {
            $count = 0;
            $grouped[$key]['id'] = $uids;
            foreach ( $checkedIds as $index => $ids ) {
                if ( $uids == $ids ) {
                    $grouped[$key][$count]['property'] = $checkedProperties[$index];
                    $grouped[$key][$count]['newValue'] = $checkedValues[$index];
                    $grouped[$key][$count]['sku'] = $checkedSku[$index];
                    $count++;
                }
            }
        }
//        echo "\n";
//        var_dump($grouped);
//            die();
        return $grouped;
    }

    public function groupNewSku($checkboxNewSku)
    {
        $count = 0;
        $groupedNewSku = [];
        foreach ($checkboxNewSku as $key => $newSku) {
            $groupedNewSku[$count]['id'] = $newSku['id'];
            $groupedNewSku[$count]['sku'] = $newSku['sku'];
                    $count++;
        }
        return $groupedNewSku;
    }


    public function groupCategories($checkboxCategory)
    {
        $count = 0;
        $groupedCategories = [];
        foreach ($checkboxCategory as $key => $categories) {
            $groupedCategories[$count]['id'] = $categories['id'];
            $groupedCategories[$count]['categoryId'] = $categories['categoryId'];
            $groupedCategories[$count]['dataState'] = $categories['dataState'];
            $groupedCategories[$count]['sku'] = $categories['sku'];
            $count++;
        }
//        var_dump($groupedCategories);
//        die();
        return $groupedCategories;
    }

    public function groupRelated($checkboxCategory)
    {
        $count = 0;
        $groupedLinks = [];
        foreach ($checkboxCategory as $categories) {
            $groupedLinks[$count]['id'] = $categories['id'];
            $groupedLinks[$count]['dataState'] = $categories['dataState'];
            $groupedLinks[$count]['linkedId'] = $categories['linkedId'];
            $groupedLinks[$count]['type'] = $categories['type'];
            $groupedLinks[$count]['sku'] = $categories['sku'];
            $count++;
        }
//        var_dump($groupedCategories);
//        die();
        return $groupedLinks;
    }


    public function fetchChangedProducts($sku, $limit)
    {
        $soapBundle = [];
        $select = $this->sql->select();
        $select->from('product');
        $select->columns(array('id' => 'entity_id', 'ldate'=>'lastModifiedDate', 'item' => 'productid'));
        $select->join(array('u' => 'users'),'u.userid = product.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'));
        $filter = new Where;
        if( !empty($sku) ){
            $filter->like('product.productid',$sku.'%');
        }

        $select->where($filter);
        $select->limit((int)$limit);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $products = $resultSet->toArray();
        $results = $this->productAttributeLookup($this->sql);
        $soapCount = 0;
        foreach( $products as $index => $product ) {
            foreach($results as $key => $attributes){
                $dataType = $attributes['dataType'];
                $attributeId = $attributes['attId'];
                $attributeCode = $attributes['attCode'];// === 'name' ? 'title' : $attributes['attCode'];
                $productAttributes = $this->productAttribute($this->sql,[$attributeCode=>'value', 'ldate'=>'lastModifiedDate'],['attribute_id'=>$attributeId,'entity_id'=>$product['id'], 'dataState'=>1], $dataType)->toArray();
                if(!empty($productAttributes )) {
                    $soapBundle[$soapCount]['count'] = $soapCount;
                    $soapBundle[$soapCount]['id'] = $product['id'];
                    $soapBundle[$soapCount]['item'] = $product['item'];
                    $soapBundle[$soapCount]['oproperty'] = $attributeCode;
                    $property = preg_match('(_)',$attributeCode) ? str_replace('_',' ',$attributeCode) : $attributeCode;
                    $soapBundle[$soapCount]['property'] = ucfirst($property);
                    $soapBundle[$soapCount]['newValue'] = $productAttributes[0][$attributeCode];
                    $soapBundle[$soapCount]['ldate'] = $productAttributes[0]['ldate'];
                    $soapBundle[$soapCount]['fullName'] = $productAttributes[0]['fName']. ' ' . $productAttributes[0]['lName'];
                    $soapCount++;
                }
            }
        }
//        var_dump($soapBundle);
//        die();
        return $soapBundle;
    }


    public function fetchLinkedProducts($sku = null, $limit = null)
    {
        $select = $this->sql->select()->columns(['entityId'=>'entity_id','sku'=>'productid'])->from('product');
        $dataState = new Expression("l.entity_id=product.entity_id and l.dataState in(2,3)");
        $select->join(['l'=>'productlink'], $dataState,['entityId'=>'entity_id', 'linkedEntityId'=>'linked_entity_id', 'dataState'=>'dataState']);
        $select->join( ['t'=>'productlink_type'], 'l.link_type_id = t.link_type_id',['type'=>'code']);
        $select->join( array('pid'=>'product'), 'pid.entity_id=l.entity_id',array('sku'=>'productid'), Select::JOIN_LEFT);
        $select->join( array('plid'=>'product'), 'plid.entity_id=l.linked_entity_id',array('linkedSku'=>'productid'), Select::JOIN_LEFT);
        $select->join( array('u'=>'users'), 'u.userid = l.changedby',array('fname'=>'firstname', 'lname'=>'lastname'));

        $filter = new Where();
        if( $sku ) {
            $filter->like('product.productid',$sku.'%');
        }
        $select->where($filter);
        $select->limit((int)$limit);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        //TODO have to implement a count feature for this.
        $linkedProducts = $resultSet->toArray();
        $linker = [];
        $linkCount = 0;
        foreach ( $linkedProducts as $linked ) {
            $linker[$linkCount]['id']           = $linked['entityId'];
            $linker[$linkCount]['sku']          = $linked['sku'];
            $linker[$linkCount]['linkedId']     = $linked['linkedEntityId'];
            $linker[$linkCount]['linkedSku']     = $linked['linkedSku'];
            $linker[$linkCount]['dataState']    = $linked['dataState'];
            $linker[$linkCount]['state']        = ((int)$linked['dataState'] == 2) ? 'New' : 'Delete';
            $linker[$linkCount]['type']         = ucfirst(str_replace('_',' ',$linked['type']));
            $linker[$linkCount]['fullname']     = $linked['fname'] . ' ' . $linked['lname'];
            $linkCount++;
        }
//        var_dump($linkedProducts);
//        die();
        return $linker;
    }

    public function updateLinkedProductstoClean($linkedProducts)
    {
        $result = '';
        $dataState = (int)$linkedProducts['dataState'];
        if ( $dataState === 3 ) {
            $delete = $this->sql->delete('productlink');
            $delete->where(array('entity_id'=>$linkedProducts['id'], 'linked_entity_id'=>$linkedProducts['linkedId']));
            $statement = $this->sql->prepareStatementForSqlObject($delete);
            $statement->execute();
            $result .= $linkedProducts['id'] . ' is not longer linked to ' . $linkedProducts['linkedId'].'<br />';
        } else {
            $update = $this->sql->update('productlink');
            $update->set(array('dataState'=>0))
                ->where(array('entity_id'=>$linkedProducts['id'], 'linked_entity_id'=>$linkedProducts['linkedId']));
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $statement->execute();
            $result .= $linkedProducts['id'] . ' is linked to ' . $linkedProducts['linkedId'].'<br />';

        }
        return $result;
    }

    public function updateProductCategoriesToClean($cats)
    {
        $result = '';
        $dataState = (int)$cats['dataState'];
        if( $dataState === 2 ){
            $update = $this->sql->update('productcategory')->set(['dataState'=>0])->where(['entity_id'=>$cats['id'], 'category_id'=>$cats['categoryId']]);
            $statement = $this->sql->prepareStatementForSqlObject($update);
            $statement->execute();
            $result .= $cats['sku'] . " has been added to categories in Magento Admin<br />";
        }
        if( $dataState === 3 ){
            $delete = $this->sql->delete('productcategory');
            $delete->where(['entity_id'=>$cats['id'], 'category_id'=>$cats['categoryId']]);
            $statement = $this->sql->prepareStatementForSqlObject($delete);
            $statement->execute();
            $result .= $cats['sku'] . " has been deleted from categories in Magento Admin<br />";
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


    public function fetchChangedCategories($sku, $limit)
    {
        $soapCategories = [];
        $categoryCount = 0;
        $select = $this->sql->select()->from('product')->columns(['entityId'=>'entity_id', 'sku'=>'productid']);
        $dataState = new Expression("c.entity_id=product.entity_id and c.dataState in(2,3)");

        $select->join(['c'=>'productcategory'], $dataState,['categortyId'=>'category_id', 'dataState'=>'dataState']);

        $select->join(['u'=>'users'], 'u.userid = c.changedby',['fname'=>'firstname','lname'=>'lastname']);

        $select->join(['cat'=>'newcategory'] , 'cat.category_id = c.category_id', ['category'=>'title']);
        $filter = new Where();
        if( $sku ) {
            $filter->like('product.productid',$sku.'%');
        }
        $select->where($filter);
        $select->limit((int)$limit);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        //TODO have to implement a count feature for this.
        $categories = $resultSet->toArray();
        foreach ( $categories as $key => $category ) {
            $soapCategories[$categoryCount]['sku'] = $category['sku'];
            $soapCategories[$categoryCount]['id'] = $category['entityId'];
            $soapCategories[$categoryCount]['categortyId'] = $category['categortyId'];
            $soapCategories[$categoryCount]['category'] = $category['category'];
            $soapCategories[$categoryCount]['dataState'] = $category['dataState'];
            $soapCategories[$categoryCount]['state'] = ( $category['dataState'] == 2 ) ? 'New' : "Delete";
            $soapCategories[$categoryCount]['fullname'] = $category['fname']. ' ' . $category['lname'];
            $categoryCount++;
         }
//        var_dump($soapCategories);
//        die();
        return $soapCategories;
    }

    public function updateImagesToClean($images)
    {
        $result ='';
        $update = $this->sql->update('productattribute_images')->set(['dataState'=>0])->where(['filename'=>$images['filename']]);
        $statement = $this->sql->prepareStatementForSqlObject($update);
        $statement->execute();
        $result .= $images['sku'] .  " with image label " . $images['label'] . " has been updated in Mage Admin.<br />";
        return $result;
    }

    public function updateToClean($changedProducts)
    {
        $results = $sku = '';

        $entityId = $changedProducts['id'];
        array_shift($changedProducts);
        foreach ( $changedProducts as $attribute ) {
            $property = $attribute['property'];
            $sku = $attribute['sku'];
            $lookup = $this->productAttributeLookup($this->sql, ['attribute_code'=>$property]);
            $attributeId = $lookup[0]['attId'];
            $dataType = $lookup[0]['dataType'];
            $update = $this->sql->update('productattribute_'.$dataType)->set(['dataState'=>0])->where(['attribute_id'=>$attributeId, 'entity_id'=>$entityId]);
            $prdAttStatement = $this->sql->prepareStatementForSqlObject($update);
            $prdAttStatement->execute();
        }
        $results .= $sku . " has been updated in Magento Admin<br />";
        return $results;
    }

    public function fetchNewProducts($newProducts)
    {
        $soapBundle = [];
        $startCount = 0;

        foreach ( $newProducts as $key => $nProd ) {
            $select = $this->sql->select()->from('product')->columns([
                'productType'   =>  'product_type',
                'website'       =>  'website',
            ])->where(['entity_id'=>$nProd['id']]);
            $statement = $this->sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $resultSet = new ResultSet;
            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet->initialize($result);
            }
            $entityId = $nProd['id'];
            $sku = $nProd['sku'];
            $products = $resultSet->toArray();
            foreach($products as $index => $value) {
                $attributes = $this->productAttributeLookup($this->sql);
                foreach( $attributes as $key => $attribute ) {
                    $tableType = (string)$attribute['dataType'];
                    $attributeId = (int)$attribute['attId'];
                    $attributeCode = $attribute['attCode'];
                    $selectAtts = $this->sql->select()->from('productattribute_'. $tableType)
                        ->columns([$attributeCode=>'value', 'attId'=>'attribute_id']);
                    $filterAttributes = new Where;
                    $filterAttributes->equalTo('productattribute_'.$tableType.'.entity_id',$entityId);
                    $filterAttributes->equalTo('productattribute_'.$tableType.'.attribute_id',$attributeId);
                    $filterAttributes->equalTo('productattribute_'.$tableType.'.dataState',2);
                    $selectAtts->where($filterAttributes);
                    $attStatement = $this->sql->prepareStatementForSqlObject($selectAtts);
                    $attResult = $attStatement->execute();
                    $attSet = new ResultSet;
                    if ($attResult instanceof ResultInterface && $attResult->isQueryResult()) {
                        $attSet->initialize($attResult);
                    }
                    $attributeValues = $attSet->toArray();
                    foreach($attributeValues as $keyValue => $valueOption) {
                        $soapBundle[$startCount]['sku'] = $sku;
                        $soapBundle[$startCount]['website'] = $products[$index]['website'];
                        if ( array_key_exists($attributeCode,$this->stockData) ) {
                            $soapBundle[$startCount]['stock_data'][$attributeCode] = $valueOption[$attributeCode];
                        } else {
                            if( is_null($attributeValues[$keyValue][$attributeCode]) && $attributeCode ==  'status' ){
                                $soapBundle[$startCount][$attributeCode] = 2;
                            }
                            if( isset($attributeValues[$keyValue][$attributeCode]) ){
                                if ( $attributeCode ==  'status' ) {
                                    $soapBundle[$startCount][$attributeCode] = (int)$valueOption[$attributeCode];
                                } else {
                                    $soapBundle[$startCount][$attributeCode] = $valueOption[$attributeCode];
                                }
                            }

                        }
                    }
                }
            }
            $startCount++;
        }
//        echo '<pre>';
//        var_dump($soapBundle);
//        die();
        return $soapBundle;

    }

    public function fetchNewItems($sku,$limit)
    {
        //fetches all attribute codes from look up table and looks them up in corresponding attribute tables only if they are new.
        $soapBundle = $optionValues = [];
        $select = $this->sql->select()->from('product')->columns([
            'id'            =>  'entity_id',
            'sku'           =>  'productid',
            'productType'   =>  'product_type',
            'website'       =>  'website',
            'creation'      =>  'creationdate',
            'creator'      =>  'changedby',
        ]);
        $filter = new Where;
        $filter->in('product.dataState',array(2));

        $select->join(['u'=>'users'],'u.userid=product.changedby',['fname'=>'firstname','lname'=>'lastname'], Select::JOIN_LEFT);
        if( $sku ) {
            $filter->like('product.productid',$sku.'%');
        }
        $select->where($filter);
        $select->limit((int)$limit);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $products = $resultSet->toArray();
        $startCount = 0;
        foreach($products as $product) {
            $soapBundle[$startCount]['sku'] = $product['sku'];
            $soapBundle[$startCount]['id'] = (int)$product['id'];
            $soapBundle[$startCount]['creation'] = date('m-d-Y',strtotime($product['creation']));
            $soapBundle[$startCount]['fullname'] = $product['fname'] . ' ' . $product['lname'];
                    $startCount++;
        }
//        echo '<pre>';
//        var_dump($soapBundle);
//        die();
        return $soapBundle;
    }



    public function updateNewProduct( $oldEntity, $newEntity )
    {
        $result = '';
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
                $update = $this->sql->update('productattribute_'.$dataType)->set(['entity_id'=>$newEntity, 'dataState'=>0])->where(['attribute_id'=>$attributeId, 'entity_id'=>$oeid]);
                $stmt = $this->sql->prepareStatementForSqlObject($update);
                $attributeResp = $stmt->execute();
            }
        }
        return $attributeResp;
    }

    public function validateSkuExists($newProducts ,$mageEntityId)
    {
        $result  = '';
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
                    $result .= $newProducts['sku'] . ' has been added to Magento Admin with new ID ' . $newEntityId . '<br />';
                }
            }
        } else {
            $response = $this->updateNewProduct($newProducts, $mageEntityId);
            $result .= $newProducts['sku'] . ' has been added to Magento Admin with ID ' . $mageEntityId . '<br />';
        }
        return $result;
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
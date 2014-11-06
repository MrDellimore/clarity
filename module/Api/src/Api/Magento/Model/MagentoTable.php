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

    public function fetchNewImages($sku = Null, $limit = Null)
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
            $filter->like('p.productid',$sku.'%');
        }
        if ( $limit ) {
            $select->limit($limit);
        }
        $filter->equalTo('productattribute_images.dataState',2);
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
            $newImages[$soapCount]['filename'] = '<img width="50" height="50" src="'.$image['filename'].'" />';
            $newImages[$soapCount]['creation'] = $image['creation'];
            $newImages[$soapCount]['fullname'] = $image['fname'] . ' ' . $image['lname'] ;
            $soapCount++;
        }
//var_dump($newImages);
//        die();
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
//                    if ( in_array($checkedProperties[$index], $this->stockData) ) {
//                        $grouped[$key][$count]['property'] = ['stock_data'=>$checkedProperties[$index]];
//                    } else {
                    if ( $checkedProperties[$index] != 'qty' ) {
                        $grouped[$key][$count]['property'] = $checkedProperties[$index];
                    }
//                    }
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


    public function fetchChangedProducts($sku = Null , $limit= Null)
    {
        $soapBundle = [];
        $select = $this->sql->select();
        $select->from('product');
        $select->columns(array('id' => 'entity_id', 'ldate'=>'lastModifiedDate', 'item' => 'productid'));
        $select->join(array('u' => 'users'),'u.userid = product.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'), Select::JOIN_LEFT);
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

                if ( $attributeCode != 'qty' ) {
                    $selectAttribute = $this->sql->select()
                                                 ->from('productattribute_'.$dataType)
                                                 ->where(['attribute_id'=>$attributeId,'entity_id'=>$product['id'], 'dataState'=>1])
                                                 ->columns([$attributeCode=>'value', 'ldate'=>'lastModifiedDate']);
                    $selectAttribute->join(array('u' => 'users'),'u.userid = productattribute_'.$dataType.'.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'), Select::JOIN_LEFT);
                }
                $attStmt = $this->sql->prepareStatementForSqlObject($selectAttribute);
                $attResult = $attStmt->execute();
                $attSet = new ResultSet;
                if ($attResult instanceof ResultInterface && $attResult->isQueryResult()) {
                    $attSet->initialize($attResult);
                }
                $productAttributes = $attSet->toArray();
//                $productAttributes = $this->productAttribute($this->sql,[$attributeCode=>'value', 'ldate'=>'lastModifiedDate'],['attribute_id'=>$attributeId,'entity_id'=>$product['id'], 'dataState'=>1], $dataType)->toArray();
                if(!empty($productAttributes )) {
//                    $soapBundle[$soapCount]['count'] = $soapCount;
                    $soapBundle[$soapCount]['id'] = $product['id'];
                    $soapBundle[$soapCount]['item'] = $product['item'];
                    if ( $attributeCode == 'qty' ) {
                        continue;
                    } else {
                        $soapBundle[$soapCount]['oproperty'] = $attributeCode;
                        $property = preg_match('(_)',$attributeCode) ? str_replace('_',' ',$attributeCode) : $attributeCode;
                        $soapBundle[$soapCount]['property'] = ucfirst($property);
                        $soapBundle[$soapCount]['newValue'] = $productAttributes[0][$attributeCode];
                    }
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
            $select->where($filter);
        }
        if( $limit ) {
            $select->limit((int)$limit);
        }
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
//        var_dump($linkedProducts);
//        die();
        $dataState = (int)$linkedProducts['dataState'];
        if ( $dataState === 3 ) {
            $delete = $this->sql->delete('productlink');
            $delete->where(array('entity_id'=>$linkedProducts['id'], 'linked_entity_id'=>$linkedProducts['linkedId']));
            $statement = $this->sql->prepareStatementForSqlObject($delete);
            $statement->execute();
            $result .= $linkedProducts['id'] . ' is no longer linked to ' . $linkedProducts['linkedId'].'<br />';
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


    public function fetchChangedCategories($sku = null, $limit = null)
    {
        $soapCategories = [];
        $categoryCount = 0;
        $select = $this->sql->select()->from('product')->columns(['entityId'=>'entity_id', 'sku'=>'productid']);
        $dataState = new Expression("c.entity_id=product.entity_id and c.dataState in(2,3)");

        $select->join(['c'=>'productcategory'], $dataState,['categoryId'=>'category_id', 'dataState'=>'dataState']);

        $select->join(['u'=>'users'], 'u.userid = c.changedby',['fname'=>'firstname','lname'=>'lastname'], Select::JOIN_LEFT);

        $select->join(['cat'=>'newcategory'] , 'cat.category_id = c.category_id', ['category'=>'title']);
        $filter = new Where();
        if( $sku ) {
            $filter->like('product.productid',$sku.'%');
            $select->where($filter);
        }
        if( $limit ){
            $select->limit((int)$limit);
        }

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
            $soapCategories[$categoryCount]['categoryId'] = $category['categoryId'];
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
//            var_dump($products, 'haha');
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
//                    var_dump($attributeValues);
                    foreach($attributeValues as $keyValue => $valueOption) {
                        $soapBundle[$startCount]['id'] = $entityId;
                        $soapBundle[$startCount]['sku'] = $sku;
                        $soapBundle[$startCount]['website'] = $products[$index]['website'];
                        if ( array_key_exists($attributeCode,$this->stockData) ) {
                            $soapBundle[$startCount]['stock_data'][$attributeCode] = $valueOption[$attributeCode];
                        } else {
                            if( is_null($attributeValues[$keyValue][$attributeCode]) && $attributeCode ==  'status' ){
                                $soapBundle[$startCount][$attributeCode] = 0;
                            }
                            if( isset($attributeValues[$keyValue][$attributeCode]) ) {
                                $soapBundle[$startCount][$attributeCode] = $attributeCode == 'status' ? (int)$valueOption[$attributeCode] : $valueOption[$attributeCode] ;
//                                if ( $attributeCode ==  'status' ) {
//                                    $soapBundle[$startCount][$attributeCode] = (int)$valueOption[$attributeCode];
//                                } else {
//                                    $soapBundle[$startCount][$attributeCode] = $valueOption[$attributeCode];
//                                }
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
        $contentReviewed = new Expression("i.entity_id=product.entity_id and attribute_id = 1676 and value = 1");
        $select->join(['i'=>'productattribute_int'],$contentReviewed,['value'=>'value']);
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


//    public function updateNewProduct( $newProducts, $mageEntityId )
//    {
//        $sku = $newProducts['sku'];
//        $oldEntityId = $newProducts['id'];
//        array_shift($newProducts);
//        array_shift($newProducts);
//        array_shift($newProducts);
//        $updateProduct = $this->sql->update('product')->set(['entity_id'=>$mageEntityId, 'dataState'=>0 ])->where(['productid'=>$sku]);
//        $prdStmt = $this->sql->prepareStatementForSqlObject($updateProduct);
//        $response = $prdStmt->execute();
//        foreach( $newProducts as $attributeCode => $attributeValue ) {
//            $lookupVals = $this->productAttributeLookup($this->sql, ['attribute_code'=>$attributeCode] );
//            if( !empty($lookupVals[0]) ) {
//                $attributeId = $lookupVals[0]['attId'];
//                $dataType = $lookupVals[0]['dataType'];
//                $update = $this->sql->update('productattribute_'.$dataType)->set(['entity_id'=>$mageEntityId, 'dataState'=>0])->where(['attribute_id'=>$attributeId, 'entity_id'=>$oldEntityId]);//$oeid]);
//                $stmt = $this->sql->prepareStatementForSqlObject($update);
//                $attributeResp = $stmt->execute();
//            }
//        }
//        return true;
//    }
//
//    public function updateExistingProduct($newProducts, $maxEntityId, $existingSku, $existingEntityId, $mageEntityId)
//    {
////        Mage entity id exists already so update with max entity id.
//        $sku = $newProducts['sku'];
//        $oldEntityId = $newProducts['id'];
//        array_shift($newProducts);
//        array_shift($newProducts);
//        array_shift($newProducts);
//        $existingProduct = $this->sql->update('product')->set(['entity_id'=>$maxEntityId])->where(['productid'=>$existingSku]);
//        $existingStmt = $this->sql->prepareStatementForSqlObject($existingProduct);
//        $existingResponse = $existingStmt->execute();
//        $lookupExistingVals = $this->productAttributeLookup( $this->sql );
//        foreach ( $lookupExistingVals as $key => $attributes ){
//            $attributeId = $attributes[0]['attId'];
//            $dataType = $attributes[0]['dataType'];
//            $update = $this->sql->update('productattribute_'.$dataType)->set(['entity_id'=>$maxEntityId])->where(['attribute_id'=>$attributeId, 'entity_id'=>$existingEntityId]);
//            $stmt = $this->sql->prepareStatementForSqlObject($update);
//            $attributeResp = $stmt->execute();
//        }
//
//        $updateExistingCat = $this->sql->update('productcategory')->set(['entity_id'=>$maxEntityId])->where(['entity_id'=>$existingEntityId]);
//        $updateStatement = $this->sql->prepareStatementForSqlObject($updateExistingCat);
//        $updateResponse = $updateStatement->execute();
//        $updateExistingLink = $this->sql->update('productlink')->set(['entity_id'=>$maxEntityId])->where(['entity_id'=>$existingEntityId]);
//        $updateExistingStmt = $this->sql->prepareStatementForSqlObject($updateExistingLink);
//        $existingResponse = $updateExistingStmt->execute();
//
////        Update entity id with mage entity id.
//        $updateNew = $this->sql->update('product')->set(['entity_id'=>$mageEntityId, 'dataState'=>0])->where(['productid'=>$sku]);
//        $updateStmt = $this->sql->prepareStatementForSqlObject($updateNew);
//        $newResponse = $updateStmt->execute();
//        $lookupNewVals = $this->productAttributeLookup( $this->sql );
//        foreach ( $lookupNewVals as $key => $attributes ){
//            $attributeId = $attributes[0]['attId'];
//            $dataType = $attributes[0]['dataType'];
//            $update = $this->sql->update('productattribute_'.$dataType)->set(['entity_id'=>$mageEntityId ,'dataState'=>0])->where(['attribute_id'=>$attributeId, 'entity_id'=>$oldEntityId]);
//            $stmt = $this->sql->prepareStatementForSqlObject($update);
//            $attributeResp = $stmt->execute();
//        }
//        $existingEntityCategory = $this->sql->update('productcategory')->set(['entity_id'=>$mageEntityId])->where(['entity_id'=>$oldEntityId]);
//        $existingEntityCategoryStmt = $this->sql->prepareStatementForSqlObject($existingEntityCategory);
//        $existingResponse = $existingEntityCategoryStmt->execute();
//        $existingEntityLink = $this->sql->update('productlink')->set(['entity_id'=>$mageEntityId])->where(['entity_id'=>$oldEntityId]);
//        $existingEntityLinkStmt = $this->sql->prepareStatementForSqlObject($existingEntityLink);
//        $existingResponse = $existingEntityLinkStmt->execute();
////            $existingEntityImage = $this->sql->update('productattribute_imags')->set(['entity_id'=>$maxEntityId])->where(['entity_id'=>$oldEntityId]);
////            $existingEntityImageStmt = $this->sql->prepareStatementForSqlObject($existingEntityImage);
////            $existingResponse = $existingEntityImageStmt->execute();
//        return true;
//    }
//
//    public function updateNewItemsToClean($newProducts, $mageEntityId)
//    {
//        $result  = '';
//        $dupEntityIdExists = $this->sql->select()->from('product')->columns(['entityId'=>'entity_id','sku'=>'productid'])->where(['entity_id'=>$mageEntityId]);
//        $dupStatement = $this->sql->prepareStatementForSqlObject($dupEntityIdExists);
//        $dupResponse = $dupStatement->execute();
//        $dupSet = new ResultSet;
//        if ($dupResponse instanceof ResultInterface && $dupResponse->isQueryResult()) {
//            $dupSet->initialize($dupResponse);
//        }
//        $id = $dupSet->toArray();
//        if( count($id) ) {
//            $existingSku = $id[0]['sku'];
//            $existingEntityId = $id[0]['entityId'];
//            $entityId = $this->adapter->query('Select max(entity_id) from product', Adapter::QUERY_MODE_EXECUTE);
//            foreach( $entityId as $eid ) {
//                foreach( $eid as $maxEntityID ) {
//                    $maxEntityId = $maxEntityID + 1;
//                    $this->updateExistingProduct( $newProducts, $maxEntityId, $existingSku, $existingEntityId, $mageEntityId );
//                    $result .= $newProducts['sku'] . ' has been added to Magento Admin with new ID ' . $mageEntityId . '<br />';
//                }
//            }
//        } else {
//            $response = $this->updateNewProduct($newProducts, $mageEntityId);
//            $result .= $newProducts['sku'] . ' has been added to Magento Admin with ID ' . $mageEntityId . '<br />';
//        }
//        return $result;
//    }

    public function updateNewProduct( $newProducts, $mageEntityId )
    {
        $sku = $newProducts['sku'];
        $oldEntityId = $newProducts['id'];
//        var_dump($oldEntityId);
//        var_dump($newProducts);
        array_shift($newProducts);
        array_shift($newProducts);
        array_shift($newProducts);
        $updateProduct = $this->sql->update('product')->set(['entity_id'=>$mageEntityId, 'dataState'=>0 ])->where(['productid'=>$sku]);
        $prdStmt = $this->sql->prepareStatementForSqlObject($updateProduct);
        $response = $prdStmt->execute();
        foreach( $newProducts as $attributeCode => $attributeValue ) {
            $lookupVals = $this->productAttributeLookup($this->sql, ['attribute_code'=>$attributeCode] );
            if( !empty($lookupVals[0]) ) {
                $attributeId = $lookupVals[0]['attId'];
                $dataType = $lookupVals[0]['dataType'];
//                echo $attributeId . ' ' . $dataType. ' ' ;
                $update = $this->sql->update('productattribute_'.$dataType)->set(['entity_id'=>$mageEntityId, 'dataState'=>0])->where(['attribute_id'=>$attributeId, 'entity_id'=>$oldEntityId]);//$oeid]);
                $stmt = $this->sql->prepareStatementForSqlObject($update);
                $attributeResp = $stmt->execute();
            }
        }
        return true;
    }

    public function updateExistingProduct($newProducts, $maxEntityId, $existingSku, $existingEntityId, $mageEntityId)
    {
//        Mage entity id exists already so update with max entity id.
        $sku = $newProducts['sku'];
        $oldEntityId = $newProducts['id'];
        array_shift($newProducts);
        array_shift($newProducts);
        array_shift($newProducts);
        $existingProduct = $this->sql->update('product')->set(['entity_id'=>$maxEntityId])->where(['productid'=>$existingSku]);
        $existingStmt = $this->sql->prepareStatementForSqlObject($existingProduct);
        $existingResponse = $existingStmt->execute();

        $lookupExistingVals = $this->productAttributeLookup( $this->sql );
        foreach ( $lookupExistingVals as $key => $attributes ){
            $attributeId = $attributes['attId'];
            $dataType = $attributes['dataType'];
            $update = $this->sql->update('productattribute_'.$dataType)->set(['entity_id'=>$maxEntityId])->where(['attribute_id'=>$attributeId, 'entity_id'=>$existingEntityId]);
            $stmt = $this->sql->prepareStatementForSqlObject($update);
            $attributeResp = $stmt->execute();
        }
        $updateExistingCat = $this->sql->update('productcategory')->set(['entity_id'=>$maxEntityId])->where(['entity_id'=>$existingEntityId]);
        $updateStatement = $this->sql->prepareStatementForSqlObject($updateExistingCat);
        $updateResponse = $updateStatement->execute();
        $updateExistingLink = $this->sql->update('productlink')->set(['entity_id'=>$maxEntityId])->where(['entity_id'=>$existingEntityId]);
        $updateExistingStmt = $this->sql->prepareStatementForSqlObject($updateExistingLink);
        $existingResponse = $updateExistingStmt->execute();
        $updateExistingImage = $this->sql->update('productattribute_images')->set(['entity_id'=>$maxEntityId])->where(['entity_id'=>$existingEntityId]);
        $updateImageStatement = $this->sql->prepareStatementForSqlObject($updateExistingImage);
        $updateResponse = $updateImageStatement->execute();

//        Update entity id with mage entity id.
        $updateNew = $this->sql->update('product')->set(['entity_id'=>$mageEntityId, 'dataState'=>0])->where(['productid'=>$sku]);
        $updateStmt = $this->sql->prepareStatementForSqlObject($updateNew);
        $newResponse = $updateStmt->execute();
        $lookupNewVals = $this->productAttributeLookup( $this->sql );
        foreach ( $lookupNewVals as $key => $attributes ){
            $attributeId = $attributes['attId'];
            $dataType = $attributes['dataType'];
            $update = $this->sql->update('productattribute_'.$dataType)->set(['entity_id'=>$mageEntityId ,'dataState'=>0])->where(['attribute_id'=>$attributeId, 'entity_id'=>$oldEntityId]);
            $stmt = $this->sql->prepareStatementForSqlObject($update);
            $attributeResp = $stmt->execute();
        }
        $existingEntityCategory = $this->sql->update('productcategory')->set(['entity_id'=>$mageEntityId])->where(['entity_id'=>$oldEntityId]);
        $existingEntityCategoryStmt = $this->sql->prepareStatementForSqlObject($existingEntityCategory);
        $existingResponse = $existingEntityCategoryStmt->execute();
        $existingEntityLink = $this->sql->update('productlink')->set(['entity_id'=>$mageEntityId])->where(['entity_id'=>$oldEntityId]);
        $existingEntityLinkStmt = $this->sql->prepareStatementForSqlObject($existingEntityLink);
        $existingResponse = $existingEntityLinkStmt->execute();
        //Todo if this is uncommented need to change max entity id to mage entity id
////            $existingEntityImage = $this->sql->update('productattribute_images')->set(['entity_id'=>$maxEntityId])->where(['entity_id'=>$oldEntityId]);
////            $existingEntityImageStmt = $this->sql->prepareStatementForSqlObject($existingEntityImage);
////            $existingResponse = $existingEntityImageStmt->execute();
        return true;
    }

    public function updateNewItemsToClean($newProducts, $mageEntityId)
    {
        $result  = $maxEntityId = '';
        $dupEntityIdExists = $this->sql->select()->from('product')->columns(['entityId'=>'entity_id','sku'=>'productid'])->where(['entity_id'=>$mageEntityId]);
        $dupStatement = $this->sql->prepareStatementForSqlObject($dupEntityIdExists);
        $dupResponse = $dupStatement->execute();
        $dupSet = new ResultSet;
        if ($dupResponse instanceof ResultInterface && $dupResponse->isQueryResult()) {
            $dupSet->initialize($dupResponse);
        }
        $id = $dupSet->toArray();
        if( count($id) ) {
            $existingSku = $id[0]['sku'];
            $existingEntityId = $id[0]['entityId'];
            $entityId = $this->adapter->query('Select max(entity_id) from product', Adapter::QUERY_MODE_EXECUTE);
            foreach( $entityId as $eid ) {
                foreach( $eid as $maxEntityID ) {
                    $maxEntityId = $maxEntityID + 1;
                    $this->updateExistingProduct( $newProducts, $maxEntityId, $existingSku, $existingEntityId, $mageEntityId );
                }
            }
            $result .= $existingSku . ' has been updated in Spex with ' . $maxEntityId . '<br />';
            $result .= $newProducts['sku'] . ' has been added to Magento Admin with new ID ' . $mageEntityId . '<br />';
        } else {
            $response = $this->updateNewProduct($newProducts, $mageEntityId);
            $result .= $newProducts['sku'] . ' has been added to Magento Admin with ID ' . $mageEntityId . '<br />';
        }
        return $result;
    }

    /**
     * @Description: This method is different because of the checkboxes in the UI. I will probably have to refactor this
     * at some point in the future. For now it works perfectly. I have an index property that contains a string or an array.
     * The array is because of qty. Qty in Mage Soap API has to be in the stock_data array. In spex it doens't exist so I have
     * to account for this. When sent through the wire I have to insert it but when updating attributes tables I have to
     * take it out so that update statement for int table works properly.
     * @param $products
     * @return array | $productSkus
     * */

    public function adjustUpdateProductKeys($products)
    {
        $productSkus = [];
        foreach ( $products as $key => $atts ) {
            $productSkus[$key]['id'] = $atts['id'];
            array_shift($atts);
            foreach ($atts as $index => $properties ) {
                $productSkus[$key][$index]['sku'] = $properties['sku'];
                foreach ( $properties as $attributes => $value ) {
                    if( $attributes == 'property' ) {
                        if ( is_array($value) ) {
                            foreach( $value as $mageAtt => $spexAtt ) {
                                $productSkus[$key][$index]['property'] = $spexAtt;
                            }
                        } else {
                            $productSkus[$key][$index]['property'] = $value;
                        }
                    }
                    if ( $attributes == 'newValue' ) {
                        $productSkus[$key][$index]['newValue'] = $value;
                    }
                }
            }
        }
//        var_dump($productSkus);
//        die();
        return $productSkus;
    }

    /**
     * Note: the param was $newProducts but I changed it to $products to make it useful for new products and changed products since they both
     * require qty to be an assoc array under stock_data.
     * @Description: This method de-inserts/takes away the stock_data key so that when attributes are being cleaned it cleans the elements of stock_data.
     * Since stock_data doesn't actually exist as an attribute but qty does, it being attribute_id 1 in the int attributes table.
     * @param $products
     * @return array | $productSkus
     **/
    public function adjustProductKeys($products)
    {
        $shiftedStockData = $productSkus = [];
        foreach( $products as $key => $acode ) {
            foreach( $acode as $index => $aValues ) {
                if( $index == 'stock_data' && isset($products[$key]['stock_data'][current(array_keys($this->stockData))]) ) {
//                    TODO might have to add a foreach here for stock_data,since this will have multiple attributes within.
//                    if( isset($newProducts[$key]['stock_data'][current(array_keys($this->stockData))]) ) {
                    $productSkus[$key][current(array_keys($this->stockData))] = $products[$key]['stock_data'][current(array_keys($this->stockData))];
                    $shiftedAttribute = array_shift($this->stockData);
                    $shiftedStockData[$shiftedAttribute] =  $shiftedAttribute;
//                    }
                } else {
                    $productSkus[$key][$index] = $products[$key][$index];
                }
            }
            $this->stockData = $shiftedStockData + $this->stockData;
        }
//        var_dump($productSkus);
//        die();
        return $productSkus;
    }

}
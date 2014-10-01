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
        $checkedIds = $checkedProperties = $grouped = [];
        foreach ( $checkboxSku as $key => $checkbox ) {
            $checkedIds[$count] = $checkbox['id'];
            $checkedProperties[$count] = $checkbox['property'];
            $count++;
        }
        $uniqueIds = array_values(array_unique($checkedIds));
        foreach ($uniqueIds as $key => $uids) {
            $count = 0;
            $grouped[$key]['id'] = $uids;
            foreach ( $checkedIds as $index => $ids ) {
                if ( $uids == $ids ) {
                    $grouped[$key][$count]['property'] = $checkedProperties[$index];
                    $count++;
                }
            }
        }
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

    public function fetchDirtyProducts($changedProducts = Null)
    {
        $startCount = 0;
        $soapUpdate = [];
//        var_dump($changedProducts);
//        die();
//        foreach ( $changedProducts as $key => $products ) {
//            $entityID = $products['id'];
//            $select = $this->sql->select()->from('product')
//                ->columns([
//                    'id'        => 'entity_id',
//                    'sku'       => 'productid',
//                    'website'   => 'website'
//                ])->where([
//                    'entity_id'     =>  $products['id'],
//                    'dataState'     =>  1
//                ]);
//            $statement = $this->sql->prepareStatementForSqlObject($select);
//            $result = $statement->execute();
//            $resultSet = new ResultSet;
//            if ($result instanceof ResultInterface && $result->isQueryResult()) {
//                $resultSet->initialize($result);
//            }
//            $product = $resultSet->toArray();
//            array_shift($products);
            foreach( $changedProducts as $ind => $products ) {
                $entityID = $products['id'];
                array_shift($products);

                $product = $this->sql->select()
                    ->columns(['sku'=>'productid'])
                    ->from('product')
                    ->where(['entity_id'=>$entityID]);
                $prdStmt = $this->sql->prepareStatementForSqlObject($product);
                $prdResult = $prdStmt->execute();

                $prdSet = new ResultSet;
                if ($prdResult instanceof ResultInterface && $prdResult->isQueryResult()) {
                    $prdSet->initialize($prdResult);
                }
                $productId = $prdSet->toArray();


                foreach ( $products as $attributes ) {
                    $lookupAttribute = $this->sql->select()
                                                 ->columns(['attId'=>'attribute_id','dataType'=>'backend_type','attCode'=>'attribute_code'])
                                                 ->from('productattribute_lookup')
                                                 ->where(['attribute_code'=>$attributes['property']]);
                    $attrStmt = $this->sql->prepareStatementForSqlObject($lookupAttribute);
                    $attResult = $attrStmt->execute();

                    $attSet = new ResultSet;
                    if ($attResult instanceof ResultInterface && $attResult->isQueryResult()) {
                        $attSet->initialize($attResult);
                    }
                    $lookup = $attSet->toArray();
                    $soapUpdate[$startCount]['id'] = $entityID;

                    foreach( $lookup as $attribute ) {
                        $dataType = (string)$attribute['dataType'];
                        $attributeId = (int)$attribute['attId'];
                        $attributeCode = (string)$attribute['attCode'];
                        //TODO have to add for changing product sku when the soap call goes through in case admin wants to change the sku in mage.
                        $productAttributeSelect = $this->sql->select()->from('productattribute_'.$dataType)
                                                                      ->columns([
                                                                                    $attributeCode  =>  'value'
                                                                                ])
                                                                      ->where([
                                                                                    'attribute_id'  =>  $attributeId,
                                                                                    'entity_id'     =>  $entityID,
                                                                                    'dataState'     =>  1
                                                                            ]);
                        $prdStatement = $this->sql->prepareStatementForSqlObject($productAttributeSelect);
                        $prdResult = $prdStatement->execute();
                        $attributeSet = new ResultSet;
                        if ($prdResult instanceof ResultInterface && $prdResult->isQueryResult()) {
                            $attributeSet->initialize($prdResult);
                        }
                        $attributeResults = $attributeSet->toArray();
                        foreach( $attributeResults as $value ) {
        //                            $soapUpdate[$startCount]['id'] = /*$ids;*/$prd['id'];
                            $soapUpdate[$startCount]['sku'] = $productId[0]['sku'];
                            //                    $soapUpdate[$startCount]['website'] = [$prd['website']];
                            $soapUpdate[$startCount][$attributeCode] =$value[$attributeCode];
                            //                        var_dump($soapUpdate);
                            //                        $startCount++;

                        }
                    }
                }
                $startCount++;
            }

//        }
//        var_dump($soapUpdate);
//        die();
        return $soapUpdate;
    }

    public function fetchChangedProducts($sku, $limit)
    {
//        $updateCount = $kpi->updateCount();
        $soapBundle = [];
        $select = $this->sql->select();
        $select->from('product');
        $select->columns(array('id' => 'entity_id', 'ldate'=>'lastModifiedDate', 'item' => 'productid'));
        $select->join(array('u' => 'users'),'u.userid = product.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'));
        $filter = new Where;
        if( !empty($sku) ){
            $filter->like('product.productid',$sku.'%');
        }
//        $filter->equalTo('product.dataState',1);

        $select->where($filter);
        $select->limit((int)$limit);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
//        $dirtyCount = $resultSet->count();
//        $this->setDirtyCount($dirtyCount);
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
//                    if( is_array($property) ) {
//                        foreach( $property as $pty ) {
//                            $soapBundle[$soapCount]['property'] .= ucfirst($pty);
//                        }
//                    } else {
                    $soapBundle[$soapCount]['property'] = ucfirst($property);
                    $soapBundle[$soapCount]['newValue'] = $productAttributes[0][$attributeCode];
                    $soapBundle[$soapCount]['ldate'] = $productAttributes[0]['ldate'];
                    $soapBundle[$soapCount]['fullName'] = $productAttributes[0]['fName']. ' ' . $productAttributes[0]['lName'];
                    $soapCount++;

//                    }

                }
//                $newAttribute = $this->fetchAttribute( $dataType,$attributeId,$attributeCode);
//                if(is_array($newAttribute)){
//                    foreach($newAttribute as $newAtt){
//                        $product[] = $newAtt;
//                    }
//                }
            }
        }
//        var_dump($soapBundle);
//        die();

//        $this->setDirtyItems($this->getDirtyCount(), $this->getAggregateAttributeDirtyCount());
        return $soapBundle;
    }

//    TODO have to change this to fetchChangedProductsCount instead.
//    public function setDirtyItems($dirtyProducts, $dirtyAttributes)
//    {
//        $this->dirtyItems = $dirtyProducts + $dirtyAttributes;
//    }
//
//    public function getDirtyItems()
//    {
//        return $this->dirtyItems;
//    }
//
//
//    public function getAggregateAttributeDirtyCount()
//    {
//        return $this->attributeDirtyCount;
//    }
//
//    public function setAggregateAttributeDirtyCount($attributeDirtyCount)
//    {
//        $this->attributeDirtyCount += $attributeDirtyCount;
//    }

    public function fetchLinkedProducts($sku = null, $limit = null)
    {
//        $select = $this->sql->select()->from('product')->columns(['entityId'=>'entity_id', 'sku'=>'productid']);
//        $dataState = new Expression("c.entity_id=product.entity_id and c.dataState in(2,3)");
//
//        $select->join(['c'=>'productcategory'], $dataState,['categortyId'=>'category_id', 'dataState'=>'dataState']);
//
//        $select->join(['u'=>'users'], 'u.userid = c.changedby',['fname'=>'firstname','lname'=>'lastname']);
//
//        $select->join(['cat'=>'newcategory'] , 'cat.category_id = c.category_id', ['category'=>'title']);
//        $filter = new Where();
//        if( $sku ) {
//            $filter->like('product.productid',$sku.'%');
//        }
//        $select->where($filter);
//        $select->limit((int)$limit);



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
//        $filter->in('productlink.dataState',array(2,3));
////        $select->from('productlink')
//            ->columns(array('entityId'=>'entity_id','linkedEntityId'=>'linked_entity_id', 'dataState'=>'dataState'))
//            ->join( array('t'=>'productlink_type'), 't.link_type_id=productlink.link_type_id',array('type'=>'code'))
//            ->join( array('pid'=>'product'), 'pid.entity_id=productlink.entity_id',array('sku'=>'productid'), Select::JOIN_LEFT)
//            ->join( array('plid'=>'product'), 'plid.entity_id=productlink.linked_entity_id',array('linkedSku'=>'productid'), Select::JOIN_LEFT)
//            ->join( array('u'=>'users'), 'u.userid = productlink.changedby',array('fname'=>'firstname', 'lname'=>'lastname'))
////               ->where(array('productcategory.dataState'=>2,'productcategory.dataState'=>3),PredicateSet::OP_OR);
//            ->where($filter);
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
//        return $linkedProducts;
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


//    public function setDirtyCount($dirtyCount)
//    {
//        $this->dirtyCount = $dirtyCount;
//    }
//
//    public function getDirtyCount()
//    {
//        return $this->dirtyCount;
//    }


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

//        $select->join(['imageid'=>'productcategory'], $dataState,['imageid'=>'value_id']);
//        $select = $this->sql->select();
//        $filter = new Where();
//        $filter->in('productcategory.dataState',array(2,3));
//        if ( $sku ) {
////            $filter->like('product.productid',$sku.'%');
////            $likeSku = new Expression("p.entity_id=productcategory.entity_id or p.productid like '" . $sku . "%'");
////            $select->join( array('p'=>'product'), $likeSku,array('sku'=>'productid'));
//            $select->join( array('pr'=>'product'), "pr.productid like '" . $sku . "%'",array('sku'=>'productid'));
//
////            $select->join( array('prd'=>'product'), "prd.productid like '" .trim($sku)."%'",['sku'=>'productid']);
//        }
//
//        $select->from('productcategory')
//               ->columns(array('entityId'=>'entity_id','categortyId'=>'category_id', 'dataState'=>'dataState'))
//               ->join( array('p'=>'product'), 'p.entity_id=productcategory.entity_id',array('sku'=>'productid'))
//               ->join( ['u'=>'users'], 'u.userid = productcategory.changedby',['fname'=>'firstname','lname'=>'lastname'])
//               ->join( ['c'=>'newcategory'] , 'c.category_id = productcategory.category_id', ['category'=>'title'])
//               ->limit((int)$limit)
//               ->where($filter);
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
//        return $categories;
        return $soapCategories;
    }

    public function updateImagesToClean($images)
    {
        $result ='';
        $update = $this->sql->update('productattribute_images')->set(['dataState'=>0])->where(['value_id'=>$images['imageid']]);
        $statement = $this->sql->prepareStatementForSqlObject($update);
        $statement->execute();
        $result .= $images['sku'] .  " with image label " . $images['label'] . " has been updated in Mage Admin.<br />";
        return $result;
    }

//    public function checkUpdates($updates)
//    {
//        $updatedId = $sku = [];
//        foreach ($updates as $key => $update ) {
//            $entityId = $update['id'];
//            $updatedId[$key]['id'] = $entityId;
//            $updated = 0;
//            array_shift($update);
//            foreach ( $update as $ind => $product ) {
//                $updatedId[$key][$ind]['property'] = $product['property'];
//                $lookup = $this->productAttributeLookup($this->sql, ['attribute_code'=>$product['property']]);
//    //            var_dump($lookup);
//    //            $attId = $lookup[0]['attId'];
//                $dataType = $lookup[0]['dataType'];
//                $select = $this->sql->select()->from('productattribute_'.$dataType)->where(['entity_id'=>$entityId, 'dataState'=>1]);
//                $statement = $this->sql->prepareStatementForSqlObject($select);
//                $result = $statement->execute();
//                $resultSet = new ResultSet;
//                if ($result instanceof ResultInterface && $result->isQueryResult()) {
//                    $resultSet->initialize($result);
//                }
//                //TODO have to implement a count feature for this.
//                $updated += $resultSet->count();
//            }
//            $updatedId[$key]['updated'] = $updated;
//
//        }
//        foreach ($updatedId as $key => $product ) {
//            if ( !$product['updated'] ) {
//                $entityId = $product['id'];
//                $updateProduct = $this->sql->update('product')->set(['dataState'=>0])->where(['entity_id'=>$entityId]);
//                $statement = $this->sql->prepareStatementForSqlObject($updateProduct);
//                $statement->execute();
//            }
//        }
////        var_dump($updatedId);
////        die();
//        return $updatedId;
//
//    }

    public function updateToClean($changedProducts)
    {
        $results = '';
//        var_dump($changedProducts);
        $entityId = $changedProducts['id'];
        $sku = $changedProducts['sku'];
//        $updated = $changedProducts['updated'];
//        var_dump($changedProducts);
        array_shift($changedProducts);
        array_shift($changedProducts);
//        array_pop($changedProducts);
//        var_dump($changedProducts);
//        $select = $this->sql->select()->from('product')->columns(['sku'=>'productid'])->where(['entity_id'=>$entityId]);
//        $statement = $this->sql->prepareStatementForSqlObject($select);
//        $result = $statement->execute();
//        $resultSet = new ResultSet;
//        if ($result instanceof ResultInterface && $result->isQueryResult()) {
//            $resultSet->initialize($result);
//        }
//        $sku = $resultSet->toArray();
//        $attribute = '';
        foreach( $changedProducts as $key => $product ) {
//            $attribute = $key;
//            echo $key. ' ' ;
//            if( $updated ) {
//                foreach( $product as $att ) {
//                    echo $att . ' ' ;
                    $lookup = $this->productAttributeLookup($this->sql, ['attribute_code'=>$key]);
//            var_dump($lookup);
                    $attributeId = $lookup[0]['attId'];
                    $dataType = $lookup[0]['dataType'];
                    $updateProductAttribute = $this->sql->update('productattribute_'.$dataType)->set(['dataState'=>0])->where(['entity_id'=>$entityId,'attribute_id'=>$attributeId]);
                    $prdAttStatement = $this->sql->prepareStatementForSqlObject($updateProductAttribute);
                    $prdAttStatement->execute();
//                }
//            }
//            else {
//                $updateProduct = $this->sql->update('product')->set(['dataState'=>0])->where(['entity_id'=>$entityId]);
//                $statement = $this->sql->prepareStatementForSqlObject($updateProduct);
//                $statement->execute();
//            }
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
//                                                          ->where(['entity_id'=>$entityId,'attribute_id'=>$attributeId]);
                    $filterAttributes = new Where;
//                        $filterAttributes->equalTo('productattribute_'.$tableType.'.dataState',0);
//                        $filterAttributes->equalTo('productattribute_'.$tableType.'.dataState',2);
                    $filterAttributes->equalTo('productattribute_'.$tableType.'.entity_id',$entityId);
                    $filterAttributes->equalTo('productattribute_'.$tableType.'.attribute_id',$attributeId);
//                    $filterAttributes->in('productattribute_'.$tableType.'.dataState',array(2));
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
                                $soapBundle[$startCount][$attributeCode] = $valueOption[$attributeCode];
                            }

                        }
                    }
                }
//                $startCount++;
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
        //->where(array('product.dataState'=>0))->quantifier(Select::QUANTIFIER_DISTINCT);
        $filter = new Where;
        $filter->in('product.dataState',array(2));

        $select->join(['u'=>'users'],'u.userid=product.changedby',['fname'=>'firstname','lname'=>'lastname'], Select::JOIN_LEFT);
        if( $sku ) {
            $filter->like('product.productid',$sku.'%');
        }
        $select->where($filter);
        $select->limit((int)$limit);

//        $statusIntJoin = new Expression('i.entity_id = product.entity_id and i.attribute_id = 273');
//        $select->join(['i'=>'productattribute_int'],$statusIntJoin ,['status'=>'value'] ,Select::JOIN_LEFT);

//        $statusOptionJoin = new Expression('o.attribute_id = i.attribute_id and o.value = i.option');
//        $select->join(['o'=>'productattribute_option'],$statusOptionJoin ,['Status'=>'value'] ,Select::JOIN_LEFT);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $products = $resultSet->toArray();
//        echo '<pre>';
//        var_dump($products);
//          $productSku = [
//              'PDPPBRBR','PLPPBRBR','PLPPGYBK','PMPPBRBR','PMPPGYBK','SEPPBRGD','SEPPGYGM','SEPPGYMSV','SSPPGNGD','SSPPGYMSV'
//          ];
//        $productSku = ['AV22303B06'];
//        statically add products skus here if more are requested.
//        $skuCount = count($productSku);
//        echo $skuCount . '<br />';
        $startCount = 0;
//        for( $i = 0; $i < $skuCount; $i++ ) {
            foreach($products as $product) {
                $soapBundle[$startCount]['sku'] = $product['sku'];
                $soapBundle[$startCount]['id'] = (int)$product['id'];
                $soapBundle[$startCount]['creation'] = date('m-d-Y',strtotime($product['creation']));
                $soapBundle[$startCount]['fullname'] = $product['fname'] . ' ' . $product['lname'];
//                $entityId = $product['id'];
//                $attributes = $this->productAttributeLookup($this->sql);
//                foreach( $attributes as $attribute ) {
//                    $tableType = (string)$attribute['dataType'];
//                    $attributeId = (int)$attribute['attId'];
//                    $attributeCode = $attribute['attCode'];
//                    $selectAtts = $this->sql->select()->from('productattribute_'. $tableType)
//                                                      ->columns([$attributeCode=>'value', 'attId'=>'attribute_id']);
//                    $filterAttributes = new Where;
//                    $filterAttributes->equalTo('productattribute_'.$tableType.'.entity_id',$entityId);
//                    $filterAttributes->equalTo('productattribute_'.$tableType.'.attribute_id',$attributeId);
//                    $filterAttributes->in('productattribute_'.$tableType.'.dataState',array(2));
//                    $selectAtts->where($filterAttributes);
//                    $attStatement = $this->sql->prepareStatementForSqlObject($selectAtts);
//                    $attResult = $attStatement->execute();
//                    $attSet = new ResultSet;
//                    if ($attResult instanceof ResultInterface && $attResult->isQueryResult()) {
//                        $attSet->initialize($attResult);
//                    }
//                    $attributeValues = $attSet->toArray();
//
//                    foreach($attributeValues as $keyValue => $valueOption) {
//                        $soapBundle[$startCount]['website'] = $product['website'];
//                        if ( array_key_exists($attributeCode,$this->stockData) ) {
//                            $soapBundle[$startCount]['property'] = ['stock_data'=>ucfirst(str_replace('_', ' ' ,$attributeCode))] ;
//                            $soapBundle[$startCount]['value'] = $valueOption[$attributeCode];
//                        } else {
//                            if( isset($valueOption[$attributeCode]) ){
//                                $soapBundle[$startCount]['property'] = ucfirst(str_replace('_', ' ' ,$attributeCode));
//                                $soapBundle[$startCount]['value'] = $valueOption[$attributeCode];
//                                if( $attributeCode == 'status' && $valueOption[$attributeCode] == 2 ) {
//                                    $soapBundle[$startCount]['value'] = 'Disabled';
//                                }
//                                if( $attributeCode == 'status' && $valueOption[$attributeCode] == 1 ) {
//                                    $soapBundle[$startCount]['value'] = 'Enabled';
//                                }
//                            }
//                            if( is_null($valueOption[$attributeCode]) && $attributeCode == 'status') {
//                                $soapBundle[$startCount]['property'] = ucfirst(str_replace('_', ' ' ,$attributeCode));;
//                                $soapBundle[$startCount]['value'] = '2';
//                            }
//                        }
//
                        $startCount++;
//                    }
//
//                }
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
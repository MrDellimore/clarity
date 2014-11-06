<?php
/**
 * Created by PhpStorm.
 * User: wsalazar
 * Date: 10/6/14
 * Time: 2:13 PM
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

class ConsoleMagentoTable
{
    use Spex;

    protected $stockData  = [
        'qty'=>'qty',
        'is_in_stock'=> 'is_in_stock',
    ];

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->sql = new Sql($this->adapter);
    }

    public function updateToClean($changedProducts)
    {
        $result = '';
        $entityId = $changedProducts['id'];
        $sku = $changedProducts['sku'];
        array_shift($changedProducts);
        array_shift($changedProducts);
        foreach ( $changedProducts as $attribute => $attValue) {
            $lookup = $this->productAttributeLookup($this->sql, ['attribute_code'=>$attribute]);
            $attributeId = $lookup[0]['attId'];
            $dataType = $lookup[0]['dataType'];
            $update = $this->sql->update('productattribute_'.$dataType)->set(['dataState'=>0])->where(['attribute_id'=>$attributeId, 'entity_id'=>$entityId]);
            $prdAttStatement = $this->sql->prepareStatementForSqlObject($update);
            $prdAttStatement->execute();
        }
        $result .= $entityId . ' with Product ID ' . $sku . " has been pushed to Mage Admin .\n";
        return $result;
    }

    /**
     * @Description: what this Method does is stack all attributes under it's correspoding entity id.
     * @param $products
     * @return array | $grouped
     * */
    public function groupProducts($products)
    {
        $count = 0;
        $changedAttributes = $changedValue = $changedID = $changedSkus = $grouped = [];
        foreach ( $products as $product ) {
            $changedID[$count] = $product['id'];
            $changedSkus[$count] = $product['sku'];
            array_shift($product);
            array_shift($product);
            $keys = array_keys($product);
            foreach ( $keys as $attCount => $attribute ) {
                $changedAttributes[$count] = $attribute;
                $changedValue[$count] = $product[$attribute];
            }
            $count++;
        }
        $uniqueIds = array_values(array_unique($changedID));
        $count = 0;
        foreach ($uniqueIds as $key => $uids) {
            $grouped[$key]['id'] = $uids;
            $grouped[$key]['sku'] = $changedSkus[$count];
            foreach ( $changedID as $index => $ids ) {
                if ( $uids == $ids ) {
                        $grouped[$count][$changedAttributes[$index]] = $changedValue[$index];
                    }
//                $count++;

                }
            $count++;
            }
//        var_dump($grouped);
//            die();
        return $grouped;
    }

    /**
     * @Description: This method grabs all product attributes with a dataState of 1(changed). For attribute qty it adds another array called stock_data.
     * Be mindful of this particular attribute, otherwise it will not update in Mage. Please refer to http://www.magentocommerce.com/api/soap/catalog/catalogProduct/catalog_product.update.html
     * for more details. Look at catalogInventoryStockItemUpdateEntity for further explanation.
     * @param null
     * @return array | $soap
     */
    public function changedProducts()
    {
        $soap = [];
        $count = 0;
        $selectAttributes = '';
//        $select = $this->sql->select()->from('product')->columns(array('id' => 'entity_id', 'sku' => 'productid', 'ldate'=>'lastModifiedDate'));
//        $select->join(array('u' => 'users'),'u.userid = product.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'));
//        $select->where(array( 'dataState' => 1));

//        $statement = $this->sql->prepareStatementForSqlObject($select);
//        var_dump($this->sql->prepareStatementForSqlObject($select));
//        die();
//        $result = $statement->execute();
//        $resultSet = new ResultSet;
//        if ($result instanceof ResultInterface && $result->isQueryResult()) {
//            $resultSet->initialize($result);
//        }
//        $products = $resultSet->toArray();
        $lookup = $this->productAttributeLookup($this->sql);
//        foreach ($products as $product) {
            foreach( $lookup as $attribute ) {
//                $entityId = $product['id'];
//                $soap[$count]['id'] = $entityId;
//                $soap[$count]['sku'] = $product['sku'];
                $dataType = $attribute['dataType'];
                $attributeId = $attribute['attId'];
                $attributeCode = $attribute['attCode'];
                if( $attributeCode != 'qty' ) {
                    $selectAttributes = $this->sql->select()->from('productattribute_'.$dataType)->columns([$attributeCode=>'value'])->where(['attribute_id'=>$attributeId, 'productattribute_'.$dataType.'.dataState'=>1]);
                    $selectAttributes->join(array('u' => 'users'),'u.userid = productattribute_'.$dataType.'.changedby ' ,array('fName' => 'firstname', 'lName' => 'lastname'),Select::JOIN_LEFT);
                    $selectAttributes->join(array('p' => 'product'),'p.entity_id = productattribute_'.$dataType.'.entity_id' ,array('id' => 'entity_id', 'sku' => 'productid'),Select::JOIN_LEFT);
                }
//                    echo $selectAttributes->getSqlString(new \PDO($this->adapter)) . "\n";
                $statementAttributes = $this->sql->prepareStatementForSqlObject($selectAttributes);
                $resultAttributes = $statementAttributes->execute();
                $resultSetAttributes = new ResultSet;
                if ($resultAttributes instanceof ResultInterface && $resultAttributes->isQueryResult()) {
                    $resultSetAttributes->initialize($resultAttributes);
                }
                $attributes = $resultSetAttributes->toArray();
                foreach ($attributes as $atts ) {
                    $soap[$count]['id'] = $atts['id'];
                    $soap[$count]['sku'] = $atts['sku'];
//                    if( $attributeCode == 'qty' ) {
//                        $soap[$count]['stock_data'][$attributeCode] = $atts[$attributeCode];
//                    } else {
//                    if ( $attributeCode != 'qty' ) {
                        $soap[$count][$attributeCode] = $atts[$attributeCode];
//                    }
//                    }
                    $count++;
                }
//                $count++;
            }
//            return $result;
//            $count++;
//        }
//        var_dump($soap);
//        die();
        return $soap;
    }

    public function fetchNewItems()
    {
        //fetches all attribute codes from look up table and looks them up in corresponding attribute tables only if they are new.
        $soapBundle = [];
        $count = 0;
        $select = $this->sql->select()->from('product')->columns([
            'id'      =>  'entity_id',
            'sku'           =>  'productid',
            'productType'   =>  'product_type',
            'website'       =>  'website',
            'dateCreated'   =>  'creationdate',
        ])->where(array('dataState'=>2));
        $contentReviewed = new Expression("i.entity_id=product.entity_id and attribute_id = 1676 and value = 1");
        $select->join(['i'=>'productattribute_int'],$contentReviewed,['value'=>'value']);
        $statement = $this->sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $resultSet = new ResultSet;
        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet->initialize($result);
        }
        $products = $resultSet->toArray();
        foreach($products as $index => $value){
            $entityId = $value['id'];
            $attributes = $this->productAttributeLookup($this->sql);
            $soapBundle[$count]['id'] = $value['id'];
            $soapBundle[$count]['sku'] = $value['sku'];
            foreach( $attributes as $key => $fields ){
                $tableType = $fields['dataType'];
                $attributeId = (int)$fields['attId'];
                $attributeCode = $fields['attCode'];
                $selectAttributes = $this->sql->select()->from('productattribute_'.$tableType)
                          ->columns([$attributeCode=>'value'])
                          ->where(['entity_id'=>$entityId,'attribute_id'=>$attributeId, 'dataState'=>2]);
                $statementAtts = $this->sql->prepareStatementForSqlObject($selectAttributes);
                $resultAtts = $statementAtts->execute();
                $resultSetAtts = new ResultSet;
                if ($resultAtts instanceof ResultInterface && $resultAtts->isQueryResult()) {
                    $resultSetAtts->initialize($resultAtts);
                }
                $attributeValues = $resultSetAtts->toArray();
//                $attributeValues = $this->productAttribute($this->sql, [$attributeCode=>'value'],['entity_id'=>$entityId,'attribute_id'=>$attributeId, 'dataState'=>2],$tableType)->toArray();
                foreach($attributeValues as $keyValue => $valueOption){
                    $soapBundle[$count]['website'] = $value['website'];
//                    $soapBundle[$count][$attributeCode] = $attributeValues[$keyValue][$attributeCode];
                    if ( array_key_exists($attributeCode,$this->stockData) ) {
                        $soapBundle[$count]['stock_data'][$attributeCode] = $valueOption[$attributeCode];
                    } else {
                        if( is_null($attributeValues[$keyValue][$attributeCode]) && $attributeCode ==  'status' ){
                            $soapBundle[$count][$attributeCode] = 0;
                        }
                        if( isset($attributeValues[$keyValue][$attributeCode]) ){
//                            if( $attributeCode == 'content_reviewed' && (int)$valueOption[$attributeCode] == 1 ) {
                                if ( $attributeCode ==  'status' ) {
                                    $soapBundle[$count][$attributeCode] = (int)$valueOption[$attributeCode];
                                } else {
                                    $soapBundle[$count][$attributeCode] = $valueOption[$attributeCode];
                                }
//                            }
                        }

                    }
                }
            }
            $count++;
        }
//        var_dump($soapBundle);
//        die();
        return $soapBundle;
    }

} 